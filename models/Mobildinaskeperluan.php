<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "mobildinaskeperluan".
 *
 * @property int $id_mobildinaskeperluan
 * @property string $nama_mobildinaskeperluan
 * @property string $timestamp
 */
class Mobildinaskeperluan extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'mobildinaskeperluan';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nama_mobildinaskeperluan'], 'required'],
            [['timestamp'], 'safe'],
            [['nama_mobildinaskeperluan'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_mobildinaskeperluan' => 'Id Mobildinaskeperluan',
            'nama_mobildinaskeperluan' => 'Nama Mobildinaskeperluan',
            'timestamp' => 'Timestamp',
        ];
    }
}
