<?php

namespace app\models;

class Dltujuan extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'dltujuan';
    }
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
