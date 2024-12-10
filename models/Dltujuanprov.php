<?php

namespace app\models;

class Dltujuanprov extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'dltujuanprov';
    }

    public function rules()
    {
        return [
            [['id_dltujuanprov', 'nama_tujuanprov'], 'required'],
            [['id_dltujuanprov'], 'string', 'max' => 2],
            [['nama_tujuanprov'], 'string', 'max' => 255],
            [['id_dltujuanprov'], 'unique'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'id_dltujuanprov' => 'Id Dltujuanprov',
            'nama_tujuanprov' => 'Nama Tujuanprov',
        ];
    }
}
