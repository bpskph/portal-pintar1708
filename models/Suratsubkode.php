<?php
namespace app\models;

class Suratsubkode extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'suratsubkode';
    }
    public function rules()
    {
        return [
            [['fk_suratkode', 'kode_suratsubkode', 'rincian_suratsubkode'], 'required'],
            [['rincian_suratsubkode'], 'string'],
            [['fk_suratkode'], 'string', 'max' => 2],
            [['kode_suratsubkode'], 'string', 'max' => 4],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id_suratsubkode' => 'Id Suratsubkode',
            'fk_suratkode' => 'Fk Suratkode',
            'kode_suratsubkode' => 'Kode Suratsubkode',
            'rincian_suratsubkode' => 'Rincian Suratsubkode',
        ];
    }
}
