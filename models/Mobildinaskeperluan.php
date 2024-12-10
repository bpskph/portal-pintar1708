<?php

namespace app\models;

class Mobildinaskeperluan extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'mobildinaskeperluan';
    }

    public function rules()
    {
        return [
            [['nama_mobildinaskeperluan'], 'required'],
            [['timestamp'], 'safe'],
            [['nama_mobildinaskeperluan'], 'string', 'max' => 255],
        ];
    }
    public function attributeLabels()
    {
        return [
            'id_mobildinaskeperluan' => 'Id Mobildinaskeperluan',
            'nama_mobildinaskeperluan' => 'Nama Mobildinaskeperluan',
            'timestamp' => 'Timestamp',
        ];
    }
}
