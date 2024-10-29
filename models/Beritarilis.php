<?php
namespace app\models;
use Yii;
/**
 * This is the model class for table "beritarilis".
 *
 * @property int $id_beritarilis
 * @property string $waktu_rilis
 * @property string $waktu_rilis_selesai
 * @property string $materi_rilis
 * @property string $narasumber
 * @property string $lokasi
 * @property string $reporter
 * @property string $timestamp
 * @property string $timestamp_lastupdate
 */
class Beritarilis extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public $tempattext, $pilihtempat;
    public static function tableName()
    {
        return 'beritarilis';
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['waktumulai', 'waktuselesai', 'materi_rilis', 'narasumber', 'lokasi', 'reporter'], 'required'],
            [['pilihtempat'], 'required', 'on' => ['create', 'update']],
            [['timestamp', 'timestamp_lastupdate'], 'safe'],
            [['materi_rilis'], 'string'],
            [['reporter'], 'string', 'max' => 50],
            [['lokasi'], 'string', 'max' => 255],
            ['lokasi', 'validateRooms'],
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_beritarilis' => 'Id Beritarilis',
            'waktumulai' => 'Waktu Rilis',
            'waktuselesai' => 'Waktu Rilis Selesai',
            'materi_rilis' => 'Materi Rilis',
            'narasumber' => 'Narasumber',
            'lokasi' => 'Lokasi Detail',
            'reporter' => 'Reporter',
            'timestamp' => 'Timestamp',
            'timestamp_lastupdate' => 'Timestamp Lastupdate',
            'pilihtempat' => 'Lokasi',
        ];
    }
    public function getReportere()
    {
        return $this->hasOne(Pengguna::className(), ['username' => 'reporter']);
    }
    public function getTempate()
    {
        $db = Rooms::findOne(['id_rooms' => $this->lokasi]);
        if ($db !== null) {
            return $db->nama_ruangan;
        } else {
            return $this->lokasi;
        }
    }
    public function validateRooms()
    {
        $mulai = $this->waktumulai;
        $selesai = $this->waktuselesai;
        $ruangan = $this->lokasi;
        $agenda = Agenda::find()
            ->where(['<=', 'waktuselesai', $selesai])
            ->andWhere(['>=', 'waktumulai', $mulai])
            ->andWhere(['progress' => '0'])
            ->andWhere(['tempat' => $ruangan])
            ->one();
        if (Yii::$app->controller->action->id != 'editpeserta' && $agenda !== null) {
            $this->addError('tempat', "Ruangan dan jadwal tersebut sudah digunakan untuk $agenda->kegiatan.");
        }
    }
}
