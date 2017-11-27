<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "post".
 *
 * @property string $post_id
 * @property integer $page_id
 * @property string $from_name
 * @property string $from_category
 * @property integer $from_id
 * @property string $to_name
 * @property string $to_category
 * @property integer $to_id
 * @property string $message
 * @property integer $message_tags
 * @property integer $number_of_likes
 * @property integer $number_of_comments
 * @property integer $with_tags
 * @property string $data_aquired_time
 *
 * @property Like[] $likes
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
            [['page_id', 'from_id', 'to_id', 'message_tags', 'number_of_likes', 'number_of_comments', 'with_tags'], 'integer'],
            [['message'], 'string'],
            [['data_aquired_time'], 'safe'],
            [['post_id', 'from_name', 'from_category', 'to_name', 'to_category'], 'string', 'max' => 255],
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
            'number_of_likes' => 'Number Of Likes',
            'number_of_comments' => 'Number Of Comments',
            'with_tags' => 'With Tags',
            'data_aquired_time' => 'Data Aquired Time',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLikes()
    {
        return $this->hasMany(Like::className(), ['fk_post_id' => 'post_id']);
    }
}
