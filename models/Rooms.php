<?php
namespace app\models;
use Yii;
/**
 * This is the model class for table "rooms".
 *
 * @property int $id_rooms
 * @property string $nama_ruangan
 * @property string $timestamp_rooms
 */
class Rooms extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rooms';
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nama_ruangan'], 'required'],
            [['timestamp_rooms'], 'safe'],
            [['nama_ruangan'], 'string', 'max' => 255],
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_rooms' => 'Id Rooms',
            'nama_ruangan' => 'Nama Ruangan',
            'timestamp_rooms' => 'Timestamp Rooms',
        ];
    }
}
