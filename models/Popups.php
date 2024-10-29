<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "popups".
 *
 * @property int $id_popups
 * @property string $judul_popups
 * @property string $rincian_popups
 * @property int $deleted
 * @property string $timestamp
 * @property string $timestamp_lastupdate
 */
class Popups extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'popups';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['judul_popups', 'rincian_popups'], 'required'],
            [['rincian_popups'], 'string'],
            [['deleted'], 'integer'],
            [['timestamp', 'timestamp_lastupdate'], 'safe'],
            [['judul_popups'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_popups' => 'Id Popups',
            'judul_popups' => 'Judul Popups',
            'rincian_popups' => 'Rincian Popups',
            'deleted' => 'Deleted',
            'timestamp' => 'Timestamp',
            'timestamp_lastupdate' => 'Timestamp Lastupdate',
        ];
    }
}
