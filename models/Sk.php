<?php

namespace app\models;

use yii\web\UploadedFile;

class Sk extends \yii\db\ActiveRecord
{
    public $filepdf;
    public static function tableName()
    {
        return 'sk';
    }

    public function rules()
    {
        return [
            [['nomor_sk', 'tanggal_sk', 'tentang_sk', 'reporter'], 'required'],
            [['tanggal_sk', 'nama_dalam_sk', 'timestamp', 'timestamp_lastupdate'], 'safe'],
            [['tentang_sk'], 'string'],
            [['deleted'], 'integer'],
            [['nomor_sk'], 'string', 'max' => 255],
            [['reporter'], 'string', 'max' => 50],
            [['nomor_sk'], 'unique', 'message' => 'Nomor SK tersebut sudah ada pada database.'],
            [['filepdf'], 'file', 'extensions' => 'pdf'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'id_sk' => 'ID SK',
            'nomor_sk' => 'Nomor pada SK',
            'tanggal_sk' => 'Tanggal pada SK',
            'tentang_sk' => 'Perihal/Judul SK',
            'nama_dalam_sk' => 'Nama Pegawai yang Terdapat Dalam SK',
            'reporter' => 'Reporter',
            'deleted' => 'Deleted',
            'timestamp' => 'Timestamp',
            'timestamp_lastupdate' => 'Timestamp Update Terakhir',
        ];
    }
    public function getReportere()
    {
        return $this->hasOne(Pengguna::className(), ['username' => 'reporter']);
    }
    public function upload()
    {
        if ($this->validate() && $this->filepdf instanceof UploadedFile) {
            $filePath = 'sk/' . $this->id_sk . '.' . $this->filepdf->extension;
            $this->filepdf->saveAs($filePath);
            return $filePath;
        } else {
            return false;
        }
    }
}
