<?php

namespace app\models;

class Suratmasukdisposisi extends \yii\db\ActiveRecord
{
    public $tujuan_disposisi_team_lain;
    public static function tableName()
    {
        return 'suratmasukdisposisi';
    }

    public function rules()
    {
        return [
            [['level_disposisi', 'fk_suratmasuk', 'tanggal_disposisi', 'pemberi_disposisi', 'instruksi'], 'required'],
            [['fk_suratmasuk', 'tujuan_disposisi_team', 'status_penyelesaian', 'deleted'], 'integer'],
            [['tanggal_disposisi', 'timestamp_lastupdate', 'timestamp', 'tujuan_disposisi_team_lain'], 'safe'],
            [['instruksi'], 'string'],
            [['level_disposisi'], 'string', 'max' => 2],
            [['pemberi_disposisi', 'tujuan_disposisi_pegawai'], 'string', 'max' => 50],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id_suratmasukdisposisi' => 'Id Suratmasukdisposisi',
            'level_disposisi' => 'Level Disposisi',
            'fk_suratmasuk' => 'Fk Suratmasuk',
            'tanggal_disposisi' => 'Tanggal Disposisi',
            'pemberi_disposisi' => 'Pemberi Disposisi',
            'tujuan_disposisi_team' => 'Tujuan Disposisi Team',
            'tujuan_disposisi_pegawai' => 'Tujuan Disposisi Pegawai',
            'instruksi' => 'Instruksi',
            'status_penyelesaian' => 'Status Penyelesaian',
            'deleted' => 'Deleted',
            'timestamp_lastupdate' => 'Timestamp Lastupdate',
            'timestamp' => 'Timestamp',
        ];
    }

    public function getTeame()
    {
        return $this->hasOne(Team::className(), ['id_team' => 'tujuan_disposisi_team']);
    }

    public function getPegawaie()
    {
        return $this->hasOne(Pengguna::className(), ['username' => 'tujuan_disposisi_pegawai']);
    }
}
