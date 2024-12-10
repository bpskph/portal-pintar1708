<?php

namespace app\models;

class Zoomstype extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'zoomstype';
    }

    public function rules()
    {
        return [
            [['nama_zoomstype'], 'required'],
            [['kuota', 'active'], 'integer'],
            [['timestamp'], 'safe'],
            [['nama_zoomstype'], 'string', 'max' => 200],
        ];
    }
    public function attributeLabels()
    {
        return [
            'id_zoomstype' => 'Id Zoomstype',
            'nama_zoomstype' => 'Nama Zoomstype',
            'kuota' => 'Kuota',
            'active' => 'Active',
            'timestamp' => 'Timestamp',
        ];
    }
}
