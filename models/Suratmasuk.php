<?php

namespace app\models;

class Suratmasuk extends \yii\db\ActiveRecord
{
    public $filepdf, $pemberidisposisi, $penerimadisposisi;
    public static function tableName()
    {
        return 'suratmasuk';
    }

    public function rules()
    {
        return [
            [['pengirim_suratmasuk', 'perihal_suratmasuk', 'tanggal_diterima', 'nomor_suratmasuk', 'tanggal_suratmasuk', 'sifat', 'fk_suratmasukpejabat', 'reporter'], 'required'],
            [['perihal_suratmasuk'], 'string'],
            [['tanggal_diterima', 'tanggal_suratmasuk', 'timestamp', 'timestamp_lastupdate'], 'safe'],
            [['sifat', 'deleted'], 'integer'],
            [['pengirim_suratmasuk', 'nomor_suratmasuk'], 'string', 'max' => 255],
            [['reporter'], 'string', 'max' => 50],
            [['nomor_suratmasuk'], 'unique'],
            ['tanggal_suratmasuk', 'validateDates'],
            [['filepdf'], 'required', 'on' => 'create'],
            [['filepdf'], 'file', 'extensions' => 'pdf', 'skipOnEmpty' => !$this->isNewRecord, 'maxSize' => 1024 * 1024 * 2],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id_suratmasuk' => 'ID Surat Masuk',
            'pengirim_suratmasuk' => 'Pengirim Surat',
            'perihal_suratmasuk' => 'Perihal Surat',
            'tanggal_diterima' => 'Tanggal Surat Diterima',
            'nomor_suratmasuk' => 'Nomor Surat',
            'tanggal_suratmasuk' => 'Tanggal pada Surat',
            'fk_suratmasukpejabat' => 'Pejabat Pemberi Disposisi',
            'sifat' => 'Sifat',
            'reporter' => 'Reporter',
            'deleted' => 'Deleted',
            'timestamp' => 'Timestamp',
            'timestamp_lastupdate' => 'Timestamp Last Update',
        ];
    }

    public function validateDates()
    {
        if (strtotime($this->tanggal_diterima) < strtotime($this->tanggal_suratmasuk)) {
            $this->addError('tanggal_diterima', 'Tanggal pada surat tidak bisa melebihi tanggal surat diterima.');
            $this->addError('tanggal_suratmasuk', 'Tanggal pada surat tidak bisa melebihi tanggal surat diterima.');
        }
    }

    public function getReportere()
    {
        return $this->hasOne(Pengguna::className(), ['username' => 'reporter']);
    }

    public function getSuratmasukdisposisie()
    {
        return $this->hasMany(Suratmasukdisposisi::className(), ['fk_suratmasuk' => 'id_suratmasuk']);
    }

    public function getTeame()
    {
        return $this->hasOne(Team::className(), ['id_team' => 'tujuan_disposisi_team'])->via('suratmasukdisposisie');
    }

    public function getPejabate()
    {
        return $this->hasOne(Pengguna::className(), ['username' => 'fk_suratmasukpejabat']);
    }

    public function upload()
    {
        if ($this->validate()) {
            $this->filepdf->saveAs('surat/masuk/' . $this->id_suratmasuk . '.' . $this->filepdf->extension);
            return true;
        } else {
            return false;
        }
    }
}
