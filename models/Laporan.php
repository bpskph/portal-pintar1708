<?php
namespace app\models;

class Laporan extends \yii\db\ActiveRecord
{
    public $filepdf;
    public static function tableName()
    {
        return 'laporan';
    }
    public function rules()
    {
        return [
            [['laporan', 'dokumentasi', 'uploader'], 'safe'],
            [['dokumentasi'], 'string'],
            ['dokumentasi', 'url', 'validSchemes' => ['http', 'https']],
            [['filepdf'], 'file', 'extensions' => 'pdf'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'id_laporan' => 'Id Laporan',
            'laporan' => 'Laporan',
            'dokumentasi' => 'Link Dokumentasi',
        ];
    }
    public function getAgendae()
    {
        return $this->hasOne(Agenda::className(), ['id_agenda' => 'id_laporan']);
    }
    public function upload()
    {
        if ($this->validate()) {
            $this->filepdf->saveAs('laporans/' . $this->id_laporan . '.' . $this->filepdf->extension);
            return true;
        } else {
            return false;
        }
    }
}
