<?php
namespace app\models;
use Yii;
/**
 * This is the model class for table "suratkode".
 *
 * @property string $id_suratkode
 * @property int $jenis
 * @property string $rincian_suratkode
 */
class Suratkode extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'suratkode';
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_suratkode', 'jenis', 'rincian_suratkode'], 'required'],
            [['jenis'], 'integer'],
            [['rincian_suratkode'], 'string'],
            [['id_suratkode'], 'string', 'max' => 2],
            [['id_suratkode'], 'unique'],
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_suratkode' => 'Id Suratkode',
            'jenis' => 'Jenis',
            'rincian_suratkode' => 'Rincian Suratkode',
        ];
    }
}
