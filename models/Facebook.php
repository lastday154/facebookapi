<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\models\Post;
use app\models\PostLikes;
use app\models\Curl;
use yii\db\Exception;

class Facebook extends Model
{
    const BASE_URL = 'https://graph.facebook.com/';
    const ACCESS_TOKEN = '413325375751335|uMXe87Fgk2hRyHihYX0y27QJg3w';

    protected $curl;

    const LIKES = 'likes';

    const COMMENTS = 'comments';

    const ERROR = 'error';

    public function __construct(Curl $curl)
    {
        $this->curl = $curl;
    }

    /**
     * Send request to facebook api, save and export data
     * @param $url string - ID or URL of the designated Facebook Page
     * @return array
     */
    public function processPage($url)
    {
        $pageId = $this->getPageId($url);
        if (empty($pageId)) {
            return [self::ERROR => 'cannot find page id'];
        }

        $posts = $this->getPosts($pageId);
        if (empty($posts)) {
            return [self::ERROR => 'no posts in the pages'];
        }

        if (!$this->savePosts($posts, $pageId)) {
            return [self::ERROR => 'cannot save all posts to database'];
        }

        return ['success' => true, 'pageId' => $pageId];
    }

    /**
     * @param string $url - ID or URL of the designated Facebook Page
     * @return string - the page id of user page input
     */
    public function getPageId($url)
    {
        $pos = strrpos($url, '/');
        if (!$pos) {
            return $url;
        }

        $identifier = substr($url, $pos+1);
        $requestUrl = self::BASE_URL . $identifier . '/?access_token=' . self::ACCESS_TOKEN;
        $result = $this->curl->request($requestUrl);
        return isset($result['id']) ? $result['id'] : '';
    }

    /**
     * @param $pageId - page id
     * @return array - get data of 25 posts in a facebook page
     */
    public function getPosts($pageId)
    {
        $url= self::BASE_URL . $pageId . '/feed?access_token=' . self::ACCESS_TOKEN;
        $result = $this->curl->request($url);
        return isset($result['data']) ? $result['data'] : [];
    }

    /**
     * save posts
     * @param $posts
     * @param $pageId
     * @return array
     */
    protected function savePosts($posts, $pageId)
    {
        $postRows = [];
        $likeModel = new Like;
        $likesRow = [];
        foreach ($posts as $post) {
            $row = [];
            if (!isset($post['id'])) {
                continue;
            }
            $postId = $post['id'];
            $postData = $this->getPostData($postId);
            $row[Post::POST_ID] = $postId;
            $row[Post::PAGE_ID] = $pageId;
            if (isset($postData['from'])) {
                $from = $postData['from'];
                $row[Post::FROM_NAME] = isset($from['name']) ? $from['name'] : '';
                $row[Post::FROM_CATEGORY] = isset($from['category']) ? $from['category'] : '';
                $row[Post::FROM_ID] = isset($from['id']) ? $from['id'] : '';
            }
            if (isset($postData['to']['data'])) {
                $to = $postData['to']['data'];
                $row['to_name'] = implode("|", array_column($to, 'name'));
                $row['to_category'] = implode("|", array_column($to, 'category'));
                $row['to_id'] = implode("|", array_column($to, 'id'));
            } else {
                $row['to_name'] = '';  $row['to_category'] = ''; $row['to_id'] = '';
            }
            $row['message'] = isset($postData['message']) ? $postData['message'] : '';
            $row['message_tags'] = !empty($postData['message_tags']) ? true : false;
            $row['picture'] = isset($postData['picture']) ? $postData['picture'] : '';
            $row['link'] = isset($postData['link']) ? $postData['link'] : '';
            $row['name'] = isset($postData['name']) ? $postData['name'] : '';
            $row['caption'] = isset($postData['caption']) ? $postData['caption'] : '';
            $row['description'] = isset($postData['description']) ? $postData['description'] : '';
            $row['source'] = isset($postData['source']) ? $postData['source'] : '';
            $row['properties'] = !empty($postData['properties']) ? json_encode($postData['properties']) : '' ;
            $row['icon'] = isset($postData['icon']) ? $postData['icon'] : '';
            $row['actions'] = !empty($postData['actions']) ? json_encode($postData['actions']) : '' ;
            $row['privacy'] = !empty($postData['privacy']) ? json_encode($postData['privacy']) : '' ;
            $row['type'] = isset($postData['type']) ? $postData['type'] : '' ;
            $row['place'] = isset($postData['place']) ? $postData['place'] : '' ;
            $row['story'] = isset($postData['story']) ? $postData['story'] : '' ;
            $numberOfLikes = $this->getTotalCount($postId, self::LIKES);
            $row[Post::NUMBER_OF_LIKES] = $numberOfLikes;
            $row[Post::NUMBER_OF_COMMENTS] = $this->getTotalCount($postId, self::COMMENTS);
            $row['object_id'] = isset($postData['object_id']) ? $postData['object_id'] : '' ;
            $row['application'] = !empty($postData['application']) ? json_encode($postData['application']) : '' ;
            $row['created_time'] = isset($postData['created_time']) ? $postData['created_time'] : '' ;
            $row['updated_time'] = isset($postData['updated_time']) ? $postData['updated_time'] : '' ;
            $row['with_tags'] = !empty($postData['with_tags']) ? true : false;
            $row['data_aquired_time'] = date("Y-m-d H:i:s");
            $postRows[] = $row;
            $likesRow = array_merge($likesRow, $this->getLikeRows($pageId, $postId, $numberOfLikes));
        }
        try {
            if (empty($postRows)) {
                return [self::ERROR => 'no post found'];
            }
            $postModel = new Post;
            $db = Yii::$app->db;
            $sql = $db->queryBuilder->batchInsert(Post::tableName(), $postModel->attributes(), $postRows);
            $db->createCommand(str_replace("INSERT INTO ","REPLACE INTO",$sql))->execute();
            if (!empty($likesRow)) {
                $likeColumns = $likeModel->attributes();
                array_shift($likeColumns);
                $sql = $db->queryBuilder->batchInsert(Like::tableName(), $likeColumns, $likesRow);
                $db->createCommand(str_replace("INSERT INTO ","REPLACE INTO",$sql))->execute();
            }
            return ['success' => true];
        } catch (Exception $exception) {
            Yii::warning($exception->getMessage());
        }
    }

    /**
     * @param $postId - post id
     * @return mixed - data of a post in facebook page
     */
    protected function getPostData($postId)
    {
        $url= self::BASE_URL . $postId . '/?fields=from,to,message,message_tags,with_tags,picture,link,name,caption,description,source,properties,icon,actions,privacy,type,place,story,object_id,application,created_time,updated_time&access_token=' . self::ACCESS_TOKEN;
        return $this->curl->request($url);
    }

    /**
     * @param $postId - post id
     * @param $attribute - comments/likes/etc ..
     * @return int - total number of comments/likes/etc..
     */
    public function getTotalCount($postId, $attribute)
    {
        $url= self::BASE_URL . $postId . '/' . $attribute . '?fields=total_count&summary=true&access_token=' . self::ACCESS_TOKEN;
        $result  = $this->curl->request($url);

        return isset($result['summary']['total_count']) ? $result['summary']['total_count'] : 0;
    }

    /**
     * get all likes per post
     * @param $pageId
     * @param $postId
     * @param $numberOfLikes
     * @return array get all likes data
     */
    protected function getLikeRows($pageId, $postId, $numberOfLikes)
    {
        $likes = $this->getLikes($postId, $numberOfLikes);
        $rows = [];
        foreach ($likes as $like) {
            $row = [];
            $row[Like::FK_POST_ID] = $postId;
            $row[Like::PAGE_ID] = $pageId;
            $row[Like::INDIVIDUAL_ID] = isset($like['id']) ? $like['id'] : '';
            $row[Like::INDIVIDUAL_NAME] = isset($like['name']) ? $like['name'] : '';
            $row[Like::INDIVIDUAL_CATEGORY] = isset($like['category']) ? $like['category'] : '';
            $row['data_aquired_time'] = date("Y-m-d H:i:s");
            $rows[] = $row;
        }

        return $rows;
    }

    /**
     * get all likes by post id
     * @param $postId
     * @param $limit
     * @return array
     */
    protected function getLikes($postId, $limit)
    {
        $limit = 'limit=' . $limit;
        $url = self::BASE_URL . $postId . '/likes?' . $limit . '&access_token=' . self::ACCESS_TOKEN;
        $result = $this->curl->request($url);
        return isset($result['data']) ? $result['data'] : [];
    }
}
