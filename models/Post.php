<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "post".
 *
 * @property string $post_id
 * @property string $page_id
 * @property string $from_name
 * @property string $from_category
 * @property string $from_id
 * @property string $to_name
 * @property string $to_category
 * @property string $to_id
 * @property string $message
 * @property integer $message_tags
 * @property string $picture
 * @property string $link
 * @property string $name
 * @property string $caption
 * @property string $description
 * @property string $source
 * @property string $properties
 * @property string $icon
 * @property string $actions
 * @property string $privacy
 * @property string $type
 * @property string $place
 * @property string $story
 * @property integer $number_of_likes
 * @property integer $number_of_comments
 * @property string $object_id
 * @property string $application
 * @property string $created_time
 * @property string $updated_time
 * @property integer $with_tags
 * @property string $data_aquired_time
 */
class Post extends \yii\db\ActiveRecord
{
    const POST_ID = 'post_id';

    const PAGE_ID = 'page_id';

    const FROM_NAME = 'from_name';

    const FROM_ID = 'from_id';

    const FROM_CATEGORY = 'from_category';

    const NUMBER_OF_LIKES = 'number_of_likes';

    const NUMBER_OF_COMMENTS = 'number_of_comments';
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'post';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['post_id', 'page_id'], 'required'],
            [['message', 'actions', 'privacy', 'type', 'story'], 'string'],
            [['message_tags', 'number_of_likes', 'number_of_comments', 'with_tags'], 'integer'],
            [['created_time', 'updated_time', 'data_aquired_time'], 'safe'],
            [['post_id', 'page_id', 'from_name', 'from_category', 'from_id', 'to_name', 'to_category', 'to_id', 'place', 'object_id', 'application'], 'string', 'max' => 255],
            [['picture', 'link', 'name', 'caption', 'description', 'source', 'properties', 'icon'], 'string', 'max' => 2083],
            [['post_id'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'post_id' => 'Post ID',
            'page_id' => 'Page ID',
            'from_name' => 'From Name',
            'from_category' => 'From Category',
            'from_id' => 'From ID',
            'to_name' => 'To Name',
            'to_category' => 'To Category',
            'to_id' => 'To ID',
            'message' => 'Message',
            'message_tags' => 'Message Tags',
            'picture' => 'Picture',
            'link' => 'Link',
            'name' => 'Name',
            'caption' => 'Caption',
            'description' => 'Description',
            'source' => 'Source',
            'properties' => 'Properties',
            'icon' => 'Icon',
            'actions' => 'Actions',
            'privacy' => 'Privacy',
            'type' => 'Type',
            'place' => 'Place',
            'story' => 'Story',
            'number_of_likes' => 'Number Of Likes',
            'number_of_comments' => 'Number Of Comments',
            'object_id' => 'Object ID',
            'application' => 'Application',
            'created_time' => 'Created Time',
            'updated_time' => 'Updated Time',
            'with_tags' => 'With Tags',
            'data_aquired_time' => 'Data Aquired Time',
        ];
    }
}
