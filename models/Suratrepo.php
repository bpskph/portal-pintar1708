<?php
namespace app\models;
use Yii;

class Suratrepo extends \yii\db\ActiveRecord
{
    public $filepdf, $fileword;
    public static function tableName()
    {
        return 'suratrepo';
    }
    public function rules()
    {
        return [
            [['penerima_suratrepo', 'tanggal_suratrepo', 'perihal_suratrepo', 'fk_suratsubkode', 'nomor_suratrepo', 'owner', 'jenis'], 'required'],
            [['fk_agenda', 'fk_suratsubkode'], 'integer'],
            [['tanggal_suratrepo', 'timestamp', 'timestamp_suratrepo_lastupdate', 'isi_suratrepo', 'lampiran', 'tembusan', 'pihak_pertama', 'pihak_kedua', 'ttd_by', 'ttd_by_jabatan', 'isi_lampiran', 'isi_lampiran_orientation', 'is_undangan'], 'safe'],
            [['perihal_suratrepo'], 'string'],
            [['penerima_suratrepo', 'nomor_suratrepo'], 'string', 'max' => 255],
            [['owner'], 'string', 'max' => 50],
            ['tanggal_suratrepo', 'validateSembilanBelasMei'],
            ['nomor_suratrepo', 'validateDuplikasi'],
            [['pihak_pertama', 'pihak_kedua'], 'required', 'when' => function () {
                return $this->jenis == 3;
            }, 'enableClientValidation' => false],
            [['ttd_by', 'ttd_by_jabatan'], 'required', 'when' => function () {
                return $this->jenis != 3;
            }, 'enableClientValidation' => false],
            [['filepdf'], 'file', 'extensions' => 'pdf'],
            [['fileword'], 'file', 'extensions' => 'doc, docx'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'id_suratrepo' => 'ID Surat',
            'fk_agenda' => 'Agenda',
            'penerima_suratrepo' => 'Kepada',
            'tanggal_suratrepo' => 'Tanggal',
            'perihal_suratrepo' => 'Perihal',
            'is_undangan' => 'Apakah ini merupakan surat undangan?',
            'fk_suratsubkode' => 'Subjek Surat',
            'nomor_suratrepo' => 'Nomor Surat',
            'owner' => 'Owner',
            'timestamp' => 'Diinput',
            'timestamp_suratrepo_lastupdate' => 'Dimutakhirkan',
            'isi_suratrepo' => 'Isi Surat (Opsional)',
            'ttd_by_jabatan' => 'TTD Oleh (Jabatannya)',
            'ttd_by' => 'TTD Oleh (Namanya)',            
        ];
    }
    public function getAgendae()
    {
        return $this->hasOne(Agenda::className(), ['id_agenda' => 'fk_agenda']);
    }
    public function getSuratsubkodee()
    {
        return $this->hasOne(Suratsubkode::className(), ['id_suratsubkode' => 'fk_suratsubkode']);
    }
    public function getSuratkodee()
    {
        return $this->hasOne(Suratkode::className(), ['id_suratkode' => 'fk_suratkode'])->via('suratsubkodee');
    }
    public function getPihakpertamae()
    {
        return $this->hasOne(Pengguna::className(), ['username' => 'pihak_pertama']);
    }
    public function getPihakkeduae()
    {
        return $this->hasOne(Pengguna::className(), ['username' => 'pihak_kedua']);
    }
    public function getOwnere()
    {
        return $this->hasOne(Pengguna::className(), ['username' => 'owner']);
    }
    public function validateSembilanBelasMei()
    {
        if (Yii::$app->controller->action->id == 'update' || Yii::$app->controller->action->id == 'create') {
            if (strtotime($this->tanggal_suratrepo) < strtotime(date("2023-05-19"))) {
                $this->addError('tanggal_suratrepo', 'Portal Pintar hanya menerima data sejak 19 Mei 2023.');
            }
        }
    }
    public function validateDuplikasi()
    {
        $nomor_suratrepo = $this->nomor_suratrepo;
        $tanggal = $this->tanggal_suratrepo;
        if (Yii::$app->controller->action->id == 'update' || Yii::$app->controller->action->id == 'create') {
            if (preg_match('/-(.*?)\//', $nomor_suratrepo, $matches)) {
                $nosurat = ($matches[1]);
            }
            if (Yii::$app->controller->action->id == 'update')
                $duplikat = suratrepo::find()
                    ->where(['deleted' => 0])
                    ->andWhere(['not', ['id_suratrepo' => $this->id_suratrepo]])
                    ->andWhere(['YEAR (tanggal_suratrepo)' => date('Y', strtotime($tanggal))])
                    ->all();
            else
                $duplikat = suratrepo::find()
                    ->where(['deleted' => 0])
                    ->andWhere(['YEAR (tanggal_suratrepo)' => date('Y', strtotime($tanggal))])
                    ->all();
            $listItems = [];
            foreach ($duplikat as $key => $name) {
                if (preg_match('/-(.*?)\//', $name->nomor_suratrepo, $matches)) {
                    $nosuratcari = ($matches[1]);
                }
                array_push($listItems, $nosuratcari);
            }
            if (in_array($nosurat, $listItems))
                $this->addError('nomor_suratrepo', 'Maaf. Nomor tersebut telah diambil oleh operator lain. Mohon ulangi proses input/edit surat.');
        }
    }
    public function upload()
    {
        if ($this->validate()) {
            $this->filepdf->saveAs('surat/internal/pdf/' . $this->id_suratrepo . '.' . $this->filepdf->extension);
            return true;
        } else {
            return false;
        }
    }
    public function uploadWord()
    {
        if ($this->validate()) {
            $this->fileword->saveAs('surat/internal/word/' . $this->id_suratrepo . '.' . $this->fileword->extension);
            return true;
        } else {
            return false;
        }
    }
}
