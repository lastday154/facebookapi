<?php

namespace app\controllers;
use app\models\Csv;
use app\models\CsvFacebook;
use app\models\Facebook;
use app\models\PostLikes;
use app\models\Post;
use Yii;
use app\models\FacebookForm;
use app\models\Curl;
use yii\web\Controller;

class FacebookController extends  Controller
{
    public function actionIndex()
    {
        $model = new FacebookForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $facebook = new Facebook(new Curl());
            $result = $facebook->processPage($model->url);
            if (isset($result['error'])) {
                return $this->render('index', ['model' => $model, 'error' => $result['error']]);
            }
            $csvFacebook = new CsvFacebook();
            $csvFacebook->export($csvFacebook->getCsvData($result['pageId']));
            return $this->render('index', ['model' => $model]);
        } else {
            // either the page is initially displayed or there is some validation error
            return $this->render('index', ['model' => $model]);
        }
    }
}