<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "dltujuanprov".
 *
 * @property string $id_dltujuanprov
 * @property string $nama_tujuanprov
 */
class Dltujuanprov extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'dltujuanprov';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_dltujuanprov', 'nama_tujuanprov'], 'required'],
            [['id_dltujuanprov'], 'string', 'max' => 2],
            [['nama_tujuanprov'], 'string', 'max' => 255],
            [['id_dltujuanprov'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_dltujuanprov' => 'Id Dltujuanprov',
            'nama_tujuanprov' => 'Nama Tujuanprov',
        ];
    }
}
