<?php
namespace app\models;
use Yii;
/**
 * This is the model class for table "suratsubkode".
 *
 * @property int $id_suratsubkode
 * @property string $fk_suratkode
 * @property string $kode_suratsubkode
 * @property string $rincian_suratsubkode
 */
class Suratsubkode extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'suratsubkode';
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fk_suratkode', 'kode_suratsubkode', 'rincian_suratsubkode'], 'required'],
            [['rincian_suratsubkode'], 'string'],
            [['fk_suratkode'], 'string', 'max' => 2],
            [['kode_suratsubkode'], 'string', 'max' => 4],
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_suratsubkode' => 'Id Suratsubkode',
            'fk_suratkode' => 'Fk Suratkode',
            'kode_suratsubkode' => 'Kode Suratsubkode',
            'rincian_suratsubkode' => 'Rincian Suratsubkode',
        ];
    }
}
