<?php
namespace app\models;
use Yii;
/**
 * This is the model class for table "team".
 *
 * @property int $id_team
 * @property string $nama_team
 * @property string $panggilan_team
 */
class Team extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'team';
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nama_team', 'panggilan_team'], 'required'],
            [['nama_team'], 'string'],
            [['panggilan_team'], 'string', 'max' => 255],
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_team' => 'Id Team',
            'nama_team' => 'Nama Team',
            'panggilan_team' => 'Panggilan Team',
        ];
    }
}
