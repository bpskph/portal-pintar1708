<?php
namespace app\models;
class Suratrepoeksttd extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'suratrepoeksttd';
    }
    public function rules()
    {
        return [
            [['nama', 'jabatan'], 'required'],
            [['nama', 'jabatan'], 'string', 'max' => 255],
        ];
    }
    public function attributeLabels()
    {
        return [
            'id_suratrepoeksttd' => 'Id Suratrepoeksttd',
            'nama' => 'Nama',
            'jabatan' => 'Jabatan',
        ];
    }
}
