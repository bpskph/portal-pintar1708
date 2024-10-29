<?php

namespace app\models;

use Yii;
use yii\helpers\Html;

/**
 * This is the model class for table "projectmember".
 *
 * @property int $id_projectmember
 * @property int $fk_project
 * @property string $pegawai
 * @property int $member_status
 */
class Projectmember extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'projectmember';
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fk_project', 'pegawai'], 'required'],
            [['fk_project', 'member_status'], 'integer'],
            [['pegawai'], 'string', 'max' => 50],
            ['pegawai', 'validateMember'],
            ['member_status', 'validateMemberStatus',],
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_projectmember' => 'Id Projectmember',
            'fk_project' => 'Project Tim Kerja',
            'pegawai' => 'Pegawai',
            'member_status' => 'Member Status',
        ];
    }
    public function getPenggunae()
    {
        return $this->hasOne(Pengguna::className(), ['username' => 'pegawai']);
    }
    public function getProjecte()
    {
        return $this->hasOne(Project::className(), ['id_project' => 'fk_project']);
    }
    public function getTeame()
    {
        return $this->hasOne(Team::className(), ['id_team' => 'fk_team'])->via('projecte');
    }
    public function validateMember($attribute, $params)
    {
        if (Yii::$app->controller->action->id == 'create') {
            $count = static::find()
                ->where(['fk_project' => $this->fk_project, 'pegawai' => $this->pegawai])
                ->count();
            if ($count > 0) {
                $this->addError($attribute, 'Pegawai tersebut telah terdaftar dalam project ini.');
            }
        }
    }
    public function validateMemberStatus($attribute, $params)
    {
        if ($this->member_status == 2) {
            $count = static::find()
                ->where(['fk_project' => $this->fk_project, 'member_status' => 2])
                // ->andWhere(['<>', 'id_projectmember', $this->id_projectmember])
                ->count();
            if ($count > 0) {
                $cari = static::find()
                    ->where(['fk_project' => $this->fk_project, 'member_status' => 2])
                    ->joinWith('penggunae')
                    ->one();
                $url = ['pengguna/view', 'username' => $cari->penggunae->username];
                $nama = Html::a($cari->penggunae->nama, $url, [
                    'class' => 'modal-link', 
                    'data-bs-toggle' => 'modal',
                    'data-bs-target' => '#exampleModal'
                ]);
                $nama = '<a class="modal-link" href="/portalpintar2.0/pengguna/view?username=aswien" data-bs-toggle="modal" data-bs-target="#exampleModal">Aswien Oktavian Perdana, SS</a>';
                // untuk di WebApps:
                // $nama = '<a class="modal-link" href="/portalpintar/pengguna/view?username=aswien" data-bs-toggle="modal" data-bs-target="#exampleModal">Aswien Oktavian Perdana, SS</a>';
                $this->addError($attribute, 'Sudah ada ketua dalam project ini : ' . $nama . '');
            }
        }
    }
    public function getYears()
    {
        $currentYear = date('Y');
        $yearFrom = 2023;
        $yearsRange = range($currentYear, $yearFrom);
        return array_combine($yearsRange, $yearsRange);
    }
}
