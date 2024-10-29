<?php
namespace app\models;
use Yii;
/**
 * This is the model class for table "laporan".
 *
 * @property int $id_laporan
 * @property string $laporan
 * @property string $dokumentasi
 */
class Laporan extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public $filepdf;
    public static function tableName()
    {
        return 'laporan';
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['laporan', 'dokumentasi'], 'safe'],
            [['dokumentasi'], 'string'],
            ['dokumentasi', 'url', 'validSchemes' => ['http', 'https']],
            [['filepdf'], 'file', 'extensions' => 'pdf'],
        ];
    }
    /**
     * {@inheritdoc}
     */
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
