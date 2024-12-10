<?php
namespace app\models;

class Suratkode extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'suratkode';
    }
    public function rules()
    {
        return [
            [['id_suratkode', 'jenis', 'rincian_suratkode'], 'required'],
            [['jenis'], 'integer'],
            [['rincian_suratkode'], 'string'],
            [['id_suratkode'], 'string', 'max' => 2],
            [['id_suratkode'], 'unique'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'id_suratkode' => 'Id Suratkode',
            'jenis' => 'Jenis',
            'rincian_suratkode' => 'Rincian Suratkode',
        ];
    }
}
