<?php
namespace app\models;

class Teamleader extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'teamleader';
    }
    public function rules()
    {
        return [
            [['nama_teamleader', 'fk_team'], 'required'],
            [['fk_team'], 'integer'],
            [['nama_teamleader'], 'string', 'max' => 50],
        ];
    }
    public function attributeLabels()
    {
        return [
            'id_teamleader' => 'Id Teamleader',
            'nama_teamleader' => 'Nama Teamleader',
            'fk_team' => 'Fk Team',
        ];
    }

    public function getTeame()
    {
        return $this->hasOne(Team::className(), ['id_team' => 'fk_team']);
    }

    public function getPenggunae()
    {
        return $this->hasOne(Pengguna::className(), ['username' => 'nama_teamleader']);
    }
}
