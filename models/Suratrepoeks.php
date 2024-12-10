<?php
namespace app\models;
use Yii;

class Suratrepoeks extends \yii\db\ActiveRecord
{
    public $filepdf, $fileword;
    public static function tableName()
    {
        return 'suratrepoeks';
    }
    public function rules()
    {
        return [
            [['penerima_suratrepoeks', 'tanggal_suratrepoeks', 'perihal_suratrepoeks', 'fk_suratsubkode', 'nomor_suratrepoeks', 'owner', 'sifat', 'jenis', 'ttd_by', 'approver'], 'required'],
            [['fk_agenda', 'fk_suratsubkode'], 'integer'],
            [['tanggal_suratrepoeks', 'timestamp', 'timestamp_suratrepoeks_lastupdate', 'isi_suratrepoeks', 'tembusan', 'lampiran', 'komentar', 'invisibility', 'isi_lampiran', 'isi_lampiran_orientation', 'shared_to'], 'safe'],
            [['perihal_suratrepoeks'], 'string'],
            [['nomor_suratrepoeks'], 'unique'],
            [['penerima_suratrepoeks', 'nomor_suratrepoeks'], 'string', 'max' => 255],
            [['owner'], 'string', 'max' => 50],
            ['tanggal_suratrepoeks', 'validateSembilanBelasMei'],
            ['nomor_suratrepoeks', 'validateDuplikasi'],
            [['filepdf'], 'file', 'extensions' => 'pdf'],
            [['fileword'], 'file', 'extensions' => 'doc, docx, pdf'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'id_suratrepoeks' => 'ID Surat',
            'fk_agenda' => 'Agenda',
            'penerima_suratrepoeks' => 'Kepada',
            'tanggal_suratrepoeks' => 'Tanggal',
            'perihal_suratrepoeks' => 'Perihal',
            'fk_suratsubkode' => 'Subjek Surat',
            'nomor_suratrepoeks' => 'Nomor Surat',
            'owner' => 'Owner',
            'timestamp' => 'Diinput',
            'timestamp_suratrepoeks_lastupdate' => 'Dimutakhirkan',
            'isi_suratrepoeks' => 'Isi Surat (Opsional)',
            'ttd_by_jabatan' => 'TTD Oleh (Jabatannya)',
            'ttd_by' => 'Keterangan TTD',
            'invisibility' => 'Surat Anda Rahasiakan',
            'shared_to' => 'Berbagi dengan Tim'
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
    public function getOwnere()
    {
        return $this->hasOne(Pengguna::className(), ['username' => 'owner']);
    }
    public function getTtdbye()
    {
        return $this->hasOne(Suratrepoeksttd::className(), ['id_suratrepoeksttd' => 'ttd_by']);
    }
    public function getProjecte()
    {
        if ($this->shared_to !== null) {
            $db = Project::findOne(['id_project' => $this->shared_to]);
            return $db->nama_project;
        } else {
            return $this->shared_to;
        }
    }
    public function getVisibletome()
    {
        if ($this->shared_to !== null) {
            $db = Projectmember::find()
                ->select('*')
                ->where(['fk_project' => $this->shared_to])
                ->andWhere('member_status <> 0')
                ->andWhere(['pegawai' => Yii::$app->user->identity->username])
                ->count();
            if ($db > 0)
                return true;
            else
                return false;
        } else {
            return false;
        }
    }
    public function getSharedtoe()
    {
        return $this->hasOne(Project::className(), ['id_project' => 'shared_to']);
    }
    public function getSharedtomembere()
    {
        return $this->hasMany(Projectmember::className(), ['fk_project' => 'id_project'])->via('sharedtoe');
    }
    public function validateSembilanBelasMei()
    {
        if (Yii::$app->controller->action->id == 'update' || Yii::$app->controller->action->id == 'create') {
            if (strtotime($this->tanggal_suratrepoeks) < strtotime(date("2023-02-02"))) {
                $this->addError('tanggal_suratrepoeks', 'Portal Pintar hanya menerima data sejak 02 Januari 2023.');
            }
        }
    }
    public function validateDuplikasi()
    {
        $nomor_suratrepo = $this->nomor_suratrepoeks;
        $tanggal = $this->tanggal_suratrepoeks;
        if (Yii::$app->controller->action->id == 'update' || Yii::$app->controller->action->id == 'create') {
            if (preg_match('/-(.*?)\//', $nomor_suratrepo, $matches)) {
                $nosurat = ($matches[1]);
            }
            if (Yii::$app->controller->action->id == 'update')
                $duplikat = Suratrepoeks::find()
                    ->where(['deleted' => 0])
                    ->andWhere(['not', ['id_suratrepoeks' => $this->id_suratrepoeks]])
                    ->andWhere(['YEAR (tanggal_suratrepoeks)' => date('Y', strtotime($tanggal))])
                    ->all();
            else
                $duplikat = Suratrepoeks::find()
                    ->where(['deleted' => 0])
                    ->andWhere(['YEAR (tanggal_suratrepoeks)' => date('Y', strtotime($tanggal))])
                    ->all();
            $listItems = [];
            foreach ($duplikat as $key => $name) {
                if (preg_match('/-(.*?)\//', $name->nomor_suratrepoeks, $matches)) {
                    $nosuratcari = ($matches[1]);
                }
                array_push($listItems, $nosuratcari);
            }
            if (in_array($nosurat, $listItems))
                $this->addError('nomor_suratrepoeks', 'Maaf. Nomor tersebut telah diambil oleh operator lain. Mohon ulangi proses input/edit surat.');
        }
    }
    public function upload()
    {
        if ($this->validate()) {
            $this->filepdf->saveAs('surat/eksternal/pdf/' . $this->id_suratrepoeks . '.' . $this->filepdf->extension);
            return true;
        } else {
            return false;
        }
    }

    public function uploadWord()
    {
        if ($this->validate()) {
            $this->fileword->saveAs('surat/eksternal/word/' . $this->id_suratrepoeks . '.' . $this->fileword->extension);
            return true;
        } else {
            return false;
        }
    }
}
