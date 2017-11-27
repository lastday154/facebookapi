<?php

namespace app\models;

use Yii;
use yii\base\Model;

class Curl extends Model
{
    /**
     * send curl request
     * @param $url
     * @return mixed
     */
    public function request($url)
    {
        //  Initiate curl
        $ch = curl_init();
        // Disable SSL verification
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // Will return the response, if false it print the response
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // Set the url
        curl_setopt($ch, CURLOPT_URL,$url);
        // Execute
        $result=curl_exec($ch);
        // Closing
        curl_close($ch);
        return json_decode($result, true);
    }
}
