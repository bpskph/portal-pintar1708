<?php
namespace app\models;
use Yii;
/**
 * This is the model class for table "agendapimpinan".
 *
 * @property int $id_agendapimpinan
 * @property string|null $waktumulai
 * @property string $waktuselesai
 * @property string $tempat
 * @property string $kegiatan
 * @property string|null $pendamping
 * @property string|null $pendamping_lain
 * @property string $reporter
 * @property string $timestamp
 * @property string $timestamp_agendapimpinan_lastupdate
 */
class Agendapimpinan extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public $waktu;
    public static function tableName()
    {
        return 'agendapimpinan';
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tempat', 'kegiatan', 'reporter'], 'required'],
            [['id_agendapimpinan'], 'integer'],
            [['waktumulai', 'waktuselesai', 'timestamp', 'timestamp_agendapimpinan_lastupdate', 'pendamping', 'pendamping_lain'], 'safe'],
            [['kegiatan'], 'string'],
            [['tempat'], 'string', 'max' => 255],
            [['reporter'], 'string', 'max' => 50],
            [['id_agendapimpinan'], 'unique'],
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_agendapimpinan' => 'Id Agendapimpinan',
            'waktumulai' => 'Waktumulai',
            'waktuselesai' => 'Waktuselesai',
            'tempat' => 'Tempat',
            'kegiatan' => 'Kegiatan',
            'pendamping' => 'Pendamping',
            'pendamping_lain' => 'Pendamping Lain',
            'reporter' => 'Reporter',
            'timestamp' => 'Diinput',
            'timestamp_agendapimpinan_lastupdate' => 'Terakhir Diupdate',
        ];
    }
    public function getReportere()
    {
        return $this->hasOne(Pengguna::className(), ['username' => 'reporter']);
    }
}
