<?php

namespace app\models;

class Kategori extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'kategori';
    }
    public function rules()
    {
        return [
            [['nama_kategori'], 'required'],
            [['timestamp'], 'safe'],
            [['nama_kategori'], 'string', 'max' => 255],
        ];
    }
    public function attributeLabels()
    {
        return [
            'id_kategori' => 'Id Kategori',
            'nama_kategori' => 'Nama Kategori',
            'timestamp' => 'Timestamp',
        ];
    }
}
