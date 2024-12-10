<?php
namespace app\models;

class Linkmat extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'linkmat';
    }
    public function rules()
    {
        return [
            [['judul', 'link', 'keyword'], 'required'],
            [['link', 'keyword', 'keterangan'], 'string'],
            [['views', 'active'], 'integer'],
            [['timestamp', 'timestamp_lastupdate', 'owner'], 'safe'],
            [['judul'], 'string', 'max' => 255],
            [['owner'], 'string', 'max' => 50],
            ['link', 'url', 'validSchemes' => ['http', 'https']],
            ['keyword', 'validateComma'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'id_linkmat' => 'Id Linkmat',
            'judul' => 'Judul',
            'link' => 'Link',
            'keyword' => 'Keyword',
            'views' => 'Views',
            'active' => 'Active',
            'owner' => 'Owner',
            'keterangan' => 'Keterangan',
            'timestamp' => 'Diinput',
            'timestamp_lastupdate' => 'Dimutakhirkan',
        ];
    }
    public function getOwnere()
    {
        return $this->hasOne(Pengguna::className(), ['username' => 'owner']);
    }
    public function validateComma($attribute, $params)
    {
        $value = $this->keyword;
        if (!preg_match('/^[a-zA-Z0-9, ]+$/', $value)) {
            $this->addError($attribute, 'Keyword hanya dapat terisi huruf, angka, koma, dan spasi.');
        }
    }
}
