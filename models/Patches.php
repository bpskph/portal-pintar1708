<?php
namespace app\models;

class Patches extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'patches';
    }
    public function rules()
    {
        return [
            [['timestamp', 'is_notification'], 'safe'],
            [['description', 'title'], 'required'],
            [['description'], 'string'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'id_patches' => 'ID Patch/Update',
            'timestamp' => 'Timestamp',
            'description' => 'Deskripsi Patch/Update',
            'title' => 'Judul Patch/Update',
            'is_notification' => 'Status Notifikasi untuk Pengguna',
        ];
    }
}
