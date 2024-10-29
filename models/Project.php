<?php
namespace app\models;
use Yii;
/**
 * This is the model class for table "project".
 *
 * @property int $id_project
 * @property string $nama_project
 * @property int $fk_team
 * @property string $panggilan_project
 */
class Project extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'project';
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nama_project', 'fk_team', 'panggilan_project'], 'required'],
            [['nama_project'], 'string'],
            [['fk_team'], 'integer'],
            [['panggilan_project'], 'string', 'max' => 255],
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_project' => 'Id Project',
            'nama_project' => 'Nama Project',
            'fk_team' => 'Fk Team',
            'panggilan_project' => 'Panggilan Project',
        ];
    }
    public function getTeame()
    {
        return $this->hasOne(Team::className(), ['id_team' => 'fk_team']);
    }
    public function getTeamleadere()
    {
        return $this->hasOne(Teamleader::className(), ['fk_team' => 'id_team'])->via('teame');
    }
    public function getProjectmembere()
    {
        return $this->hasMany(Projectmember::className(), ['fk_project' => 'id_project']);
    }
}
