<?php
namespace app\models;

class Rooms extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'rooms';
    }
    public function rules()
    {
        return [
            [['nama_ruangan'], 'required'],
            [['timestamp_rooms'], 'safe'],
            [['nama_ruangan'], 'string', 'max' => 255],
        ];
    }
    public function attributeLabels()
    {
        return [
            'id_rooms' => 'Id Rooms',
            'nama_ruangan' => 'Nama Ruangan',
            'timestamp_rooms' => 'Timestamp Rooms',
        ];
    }
}
