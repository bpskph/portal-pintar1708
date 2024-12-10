<?php

namespace app\models;

class Suratmasukpejabat extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'suratmasukpejabat';
    }

    public function rules()
    {
        return [
            [['pegawai', 'jabatan'], 'required'],
            [['status'], 'integer'],
            [['pegawai'], 'string', 'max' => 50],
            [['jabatan'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id_suratmasukpejabat' => 'Id Suratmasukpejabat',
            'pegawai' => 'Pegawai',
            'jabatan' => 'Jabatan',
            'status' => 'Status',
        ];
    }
}
