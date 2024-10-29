<?php
namespace app\models;
use Yii;
/**
 * This is the model class for table "suratrepoeksttd".
 *
 * @property int $id_suratrepoeksttd
 * @property string $nama
 * @property string $jabatan
 */
class Suratrepoeksttd extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'suratrepoeksttd';
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nama', 'jabatan'], 'required'],
            [['nama', 'jabatan'], 'string', 'max' => 255],
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_suratrepoeksttd' => 'Id Suratrepoeksttd',
            'nama' => 'Nama',
            'jabatan' => 'Jabatan',
        ];
    }
}
