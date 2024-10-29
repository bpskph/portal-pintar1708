<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "dltujuan".
 *
 * @property string $id_dltujuan
 * @property string $nama_tujuan
 * @property string $fk_prov
 */
class Dltujuan extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'dltujuan';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_dltujuan', 'nama_tujuan', 'fk_prov'], 'required'],
            [['id_dltujuan'], 'string', 'max' => 4],
            [['nama_tujuan'], 'string', 'max' => 255],
            [['fk_prov'], 'string', 'max' => 2],
            [['id_dltujuan'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_dltujuan' => 'Id Dltujuan',
            'nama_tujuan' => 'Nama Tujuan',
            'fk_prov' => 'Fk Prov',
        ];
    }
    public function getTujuanprove()
    {
        return $this->hasOne(Dltujuanprov::className(), ['id_dltujuanprov' => 'fk_prov']);
    }
}
