<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "zoomstype".
 *
 * @property int $id_zoomstype
 * @property string $nama_zoomstype
 * @property int $kuota
 * @property int $active
 * @property string $timestamp
 */
class Zoomstype extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'zoomstype';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nama_zoomstype'], 'required'],
            [['kuota', 'active'], 'integer'],
            [['timestamp'], 'safe'],
            [['nama_zoomstype'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
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
