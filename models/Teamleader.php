<?php
namespace app\models;
use Yii;
/**
 * This is the model class for table "teamleader".
 *
 * @property int $id_teamleader
 * @property string $nama_teamleader
 * @property int $fk_team
 */
class Teamleader extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'teamleader';
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nama_teamleader', 'fk_team'], 'required'],
            [['fk_team'], 'integer'],
            [['nama_teamleader'], 'string', 'max' => 50],
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_teamleader' => 'Id Teamleader',
            'nama_teamleader' => 'Nama Teamleader',
            'fk_team' => 'Fk Team',
        ];
    }
}
