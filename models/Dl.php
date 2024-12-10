<?php

namespace app\models;

class Dl extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'dl';
    }
    public function rules()
    {
        return [
            [['pegawai', 'tanggal_mulai', 'tanggal_selesai', 'fk_tujuan', 'tugas', 'reporter'], 'required'],
            [['tugas'], 'string'],
            [['tanggal_mulai', 'tanggal_selesai', 'timestamp', 'timestamp_lastupdate'], 'safe'],
            [['deleted'], 'integer'],
            [['fk_tujuan'], 'string', 'max' => 4],
            [['tim'], 'string', 'max' => 255],
            [['reporter'], 'string', 'max' => 50],
        ];
    }
    public function attributeLabels()
    {
        return [
            'id_dl' => 'Id Dl',
            'pegawai' => 'Pegawai yang Bertugas',
            'tanggal_mulai' => 'Tanggal Mulai DL',
            'tanggal_selesai' => 'Tanggal Selesai DL',
            'fk_tujuan' => 'Kabupaten/Kota Tujuan',
            'tugas' => 'Tugas DL',
            'tim' => 'Tim',
            'reporter' => 'Reporter',
            'deleted' => 'Deleted',
            'timestamp' => 'Timestamp',
            'timestamp_lastupdate' => 'Timestamp Lastupdate',
        ];
    }
    public function getReportere()
    {
        return $this->hasOne(Pengguna::className(), ['username' => 'reporter']);
    }
    public function getTujuane()
    {
        return $this->hasOne(Dltujuan::className(), ['id_dltujuan' => 'fk_tujuan']);
    }
    public function getPelaksanae()
    {
        $db = Project::findOne(['id_project' => $this->tim]);
        if ($db !== null) {
            return $db->panggilan_project;
        } else {
            return $this->tim;
        }
    }
}
