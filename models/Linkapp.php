<?php
namespace app\models;

class Linkapp extends \yii\db\ActiveRecord
{
    public $screenshot;
    public static function tableName()
    {
        return 'linkapp';
    }
    public function rules()
    {
        return [
            [['judul', 'link', 'keyword'], 'required'],
            [['link', 'keyword'], 'string'],
            [['views'], 'integer'],
            [['timestamp', 'timestamp_lastupdate', 'owner'], 'safe'],
            [['judul'], 'string', 'max' => 255],
            [['owner'], 'string', 'max' => 50],
            ['link', 'url', 'validSchemes' => ['http', 'https']],
            ['keyword', 'validateComma'],
            [['screenshot'], 'file', 'skipOnEmpty' => !$this->isNewRecord, 'extensions' => 'png'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'id_linkapp' => 'Id Linkapp',
            'judul' => 'Judul',
            'link' => 'Link',
            'keyword' => 'Keyword',
            'views' => 'Views',
            'owner' => 'Owner',
            'timestamp' => 'Timestamp',
            'timestamp_lastupdate' => 'Timestamp Lastupdate',
            'screenshot' => 'File Screenshot'
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
