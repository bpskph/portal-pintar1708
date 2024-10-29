<?php
namespace app\models;
use Yii;
/**
 * This is the model class for table "apel".
 *
 * @property int $id_apel
 * @property int $jenis_apel
 * @property string $tanggal_apel
 * @property string $pembina_inspektur
 * @property string $pemimpin_komandan
 * @property string|null $perwira
 * @property string $mc
 * @property string $uud
 * @property string $korpri
 * @property string $doa
 * @property string $ajudan
 * @property string $operator
 * @property string|null $bendera
 * @property string $reporter
 * @property string $timestamp
 * @property string $timestamp_apel_lastupdate
 */
class Apel extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'apel';
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['jenis_apel'], 'integer'],
            [['tanggal_apel', 'pembina_inspektur', 'pemimpin_komandan', 'mc', 'uud', 'doa', 'ajudan', 'operator'], 'required'],
            [['tanggal_apel', 'korpri', 'timestamp', 'timestamp_apel_lastupdate', 'reporter', 'bendera'], 'safe'],
            [['tambahsatu_text', 'tambahsatu_petugas', 'tambahdua_text', 'tambahdua_petugas'], 'safe'],
            [['pembina_inspektur', 'pemimpin_komandan', 'perwira', 'mc', 'uud', 'korpri', 'doa', 'ajudan', 'operator', 'reporter'], 'string', 'max' => 50],
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_apel' => 'ID Apel/Upacara',
            'jenis_apel' => 'Jenis',
            'tanggal_apel' => 'Tanggal Apel/Upacara',
            'pembina_inspektur' => 'Pembina/Inspektur',
            'pemimpin_komandan' => 'Pemimpin/Komandan',
            'perwira' => 'Perwira',
            'mc' => 'MC',
            'uud' => 'Pembaca UUD',
            'korpri' => 'Panca Prasetya KORPRI',
            'doa' => 'Pembaca Doa',
            'ajudan' => 'Ajudan',
            'operator' => 'Operator Lagu',
            'bendera' => 'Pengibar Bendera',
            'reporter' => 'Reporter',
            'timestamp' => 'Diinput',
            'timestamp_apel_lastupdate' => 'Terakhir Diupdate',
            'tambahsatu_text' => 'Jabatan Petugas Tambahan Pertama',
            'tambahsatu_petugas' => 'Nama Tambahan Pertama',
            'tambahdua_text' => 'Jabatan Petugas Tambahan Kedua',
            'tambahdua_petugas' => 'Nama Tambahan Kedua',
        ];
    }
    public function getReportere()
    {
        return $this->hasOne(Pengguna::className(), ['username' => 'reporter']);
    }
    public function getPetugase($data)
    {
        if ($data != null) {
            $pengguna = Pengguna::findOne(['username' => $data]);
            return $pengguna->nama;
        } else
            return '-';
    }
}
