<?php
namespace app\models;
use Yii;
/**
 * This is the model class for table "linkmat".
 *
 * @property int $id_linkmat
 * @property string $judul
 * @property string $link
 * @property string $keyword
 * @property int $views
 * @property int $active
 * @property string $owner
 * @property string|null $keterangan
 * @property string $timestamp
 * @property string $timestamp_lastupdate
 */
class Linkmat extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'linkmat';
    }
    /**
     * {@inheritdoc}
     */
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
    /**
     * {@inheritdoc}
     */
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
