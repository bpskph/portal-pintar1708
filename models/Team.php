<?php
namespace app\models;

class Team extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'team';
    }
    public function rules()
    {
        return [
            [['nama_team', 'panggilan_team'], 'required'],
            [['nama_team'], 'string'],
            [['panggilan_team'], 'string', 'max' => 255],
        ];
    }
    public function attributeLabels()
    {
        return [
            'id_team' => 'Id Team',
            'nama_team' => 'Nama Team',
            'panggilan_team' => 'Panggilan Team',
        ];
    }
}
