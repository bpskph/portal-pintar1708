<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "kategori".
 *
 * @property int $id_kategori
 * @property string $nama_kategori
 * @property string $timestamp
 */
class Kategori extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'kategori';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nama_kategori'], 'required'],
            [['timestamp'], 'safe'],
            [['nama_kategori'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_kategori' => 'Id Kategori',
            'nama_kategori' => 'Nama Kategori',
            'timestamp' => 'Timestamp',
        ];
    }
}
