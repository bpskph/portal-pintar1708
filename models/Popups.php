<?php

namespace app\models;

class Popups extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'popups';
    }
    public function rules()
    {
        return [
            [['judul_popups', 'rincian_popups'], 'required'],
            [['rincian_popups'], 'string'],
            [['deleted'], 'integer'],
            [['timestamp', 'timestamp_lastupdate'], 'safe'],
            [['judul_popups'], 'string', 'max' => 255],
        ];
    }
    public function attributeLabels()
    {
        return [
            'id_popups' => 'Id Popups',
            'judul_popups' => 'Judul Popups',
            'rincian_popups' => 'Rincian Popups',
            'deleted' => 'Deleted',
            'timestamp' => 'Timestamp',
            'timestamp_lastupdate' => 'Timestamp Lastupdate',
        ];
    }
}
