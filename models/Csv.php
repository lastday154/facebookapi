<?php

namespace app\models;

use Yii;
use yii\base\Model;

class Csv extends Model
{
   public function export($rows, $filename = "export.csv", $delimiter=";")
   {
       header('Content-Type: application/csv');
       header('Content-Disposition: attachment; filename="'.$filename.'";');

       $fp = fopen('php://output', 'w');

       foreach ($rows as $row) {
           fputcsv($fp, $row, $delimiter);
       }

       fclose($fp);
       exit();
   }
}
