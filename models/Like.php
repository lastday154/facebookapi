<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "like".
 *
 * @property integer $like_id
 * @property string $fk_post_id
 * @property string $page_id
 * @property string $individual_id
 * @property string $individual_name
 * @property string $individual_category
 * @property string $data_aquired_time
 */
class Like extends \yii\db\ActiveRecord
{
    const LIKE_ID = 'like_id';

    const FK_POST_ID = 'fk_post_id';

    const PAGE_ID = 'page_id';

    const INDIVIDUAL_ID = 'individual_id';

    const INDIVIDUAL_NAME = 'individual_name';

    const INDIVIDUAL_CATEGORY = 'individual_category';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'like';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fk_post_id', 'page_id', 'individual_id', 'individual_name'], 'required'],
            [['data_aquired_time'], 'safe'],
            [['fk_post_id', 'page_id', 'individual_id', 'individual_name', 'individual_category'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'like_id' => 'Like ID',
            'fk_post_id' => 'Fk Post ID',
            'page_id' => 'Page ID',
            'individual_id' => 'Individual ID',
            'individual_name' => 'Individual Name',
            'individual_category' => 'Individual Category',
            'data_aquired_time' => 'Data Aquired Time',
        ];
    }
}
