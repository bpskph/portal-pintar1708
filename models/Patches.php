<?php
namespace app\models;
use Yii;
/**
 * This is the model class for table "patches".
 *
 * @property int $id_patches
 * @property string $timestamp
 * @property string $description
 */
class Patches extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'patches';
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['timestamp', 'is_notification'], 'safe'],
            [['description', 'title'], 'required'],
            [['description'], 'string'],
        ];
    }
    /**
     * {@inheritdoc}
     */
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
