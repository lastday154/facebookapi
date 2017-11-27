<?php

namespace app\models;

use Yii;
use yii\base\Model;

class CsvFacebook extends Csv
{
    const HEADER = [
        Post::PAGE_ID,
        Post::POST_ID,
        'from: name',
        'from: id',
        'from: category',
        'Page_Owner',
        'to: name',
        'to: category',
        'to: id',
        'message',
        'message_tags',
        'picture',
        'link',
        'name',
        'caption',
        'description',
        'source',
        'properties',
        'icon',
        'actions: name: (Comment)',
        'action: link (Comment)',
        'actions: name: (Like)',
        'action: link (Like)',
        'privacy: description',
        'privacy: value',
        'type',
        'likes',
        'place',
        'story',
        'story_tags',
        'with_tags',
        'comments',
        'object_id',
        'application: name',
        'application: id',
        'created_time',
        'updated_time',
        'data_aquired_time',
    ];

    /**
     * Generate facebook csv file from database
     * @return array
     */
    public function getCsvData($pageId)
    {
        $rows = [self::HEADER];
        $sql = "SELECT * FROM `post` LEFT JOIN `like` ON post.post_id = like.fk_post_id WHERE post.page_id='" . $pageId . "'";
        $data = Yii::$app->db->createCommand($sql)->queryAll();
        foreach ($data as $row) {
            $csv = [];
            $pageId = $row[Post::PAGE_ID];
            $csv[] = $pageId;
            $csv[] = $row[Post::POST_ID];
            $csv[] = $row[Post::FROM_NAME];
            $fromId = $row[Post::FROM_ID];
            $csv[] = $fromId;
            $csv[] = $row[Post::FROM_CATEGORY];
            $csv[] = ($pageId == $fromId) ? 1 : 0;
            $csv[] = $row['to_name'];
            $csv[] = $row['to_category'];
            $csv[] = $row['to_id'];
            $csv[] = $row['message'];
            $csv[] = $row['message_tags'];
            $csv[] = $row['picture'];
            $csv[] = $row['link'];
            $csv[] = $row['name'];
            $csv[] = $row['caption'];
            $csv[] = $row['description'];
            $csv[] = $row['source'];
            $csv[] = $row['properties'];
            $csv[] = $row['icon'];
            if (!empty($row['actions'])) {
                $actions = json_decode($row['actions'], true);
                $like = [];
                $comment = [];
                foreach ($actions as $action) {
                    if ($action['name'] == 'Like') {
                        $like = $action;
                    } elseif ($action['name'] == 'Comment') {
                        $comment = $action;
                    }
                }
                if (isset($comment['name'])) {
                    $csv[] = $comment['name']; $csv[] = $comment['link'];
                }
                if (isset($like['name'])) {
                    $csv[] = $like['name']; $csv[] = $like['link'];
                }
            } else {
                $csv[] = ''; $csv[] = ''; $csv[] = ''; $csv[] = '';
            }
            if (!empty($row['privacy'])) {
                $privacy = json_decode($row['privacy'], true);
                $csv[] = isset($privacy['description']) ? $privacy['description'] : '';
                $csv[] = isset($privacy['value']) ? $privacy['value'] : '';
            } else {
                $csv[] = ''; $csv[] = '';
            }
            $csv[] =  $row['type'];
            $csv[] = $row[Post::NUMBER_OF_LIKES];
            $csv[] =  $row['place'];
            $csv[] =  $row['story'];
            $csv[] = $row['message_tags'];
            $csv[] =  $row['with_tags'];
            $csv[] = $row[Post::NUMBER_OF_COMMENTS];
            $csv[] =  isset($row['object_id']) ? $row['object_id'] : '';
            if (isset($row['application'])) {
                $application = $row['application'];
                $csv[] = isset($application['name']) ? $application['name'] : '';
                $csv[] = isset($application['id']) ? $application['id'] : '';
            } else {
                $csv[] = ''; $csv = '';
            }
            $csv[] = isset($row['created_time']) ? $row['created_time'] : '';
            $csv[] = isset($row['updated_time']) ? $row['updated_time'] : '';
            $csv[] = $row['data_aquired_time'];
            $rows[] = $csv;
        }

        return $rows;
    }
}