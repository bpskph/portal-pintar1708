<?php
namespace app\models;
use yii\db\ActiveRecord;

class User extends ActiveRecord implements \yii\web\IdentityInterface
{
    public static function tableName()
    {
        return 'pengguna';
    }

    public static function findIdentity($id)
    {
        // return isset(self::$users[$id]) ? new static(self::$users[$id]) : null;
        return static::findOne($id);
    }
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
    }
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username]);
        //return static::findOne(['username' => $username,'id_bidsie'=>5]);
    }
    public function getId()
    {
        return $this->username;
    }
    public function getAuthKey()
    {
        //return $this->authKey;
    }
    public function validateAuthKey($authKey)
    {
        //return $this->authKey === $authKey;
    }
    public function validatePassword($password)
    {
        return $this->password === md5($password);
    }
    public function getThemechoice()
    {
        $theme = $this->theme;
        if ($theme == 0) {
            $themechoice = '';
        } else {
            $themechoice = 'gelap';
        }
        return $themechoice;
    }
    public function getThemechoiceheader()
    {
        $theme = $this->theme;
        if ($theme == 0) {
            $themechoiceheader = '';
        } else {
            $themechoiceheader = 'headergelap';
        }
        return $themechoiceheader;
    }
    public function getIssdmmember()
    {
        $user = $this->username;
        $ismember = Projectmember::find()
            ->select('*')
            ->where(['fk_project' => 1])
            ->andWhere(['pegawai' => $user])
            ->andWhere(['NOT', ['member_status' => 0]])
            ->count();
        if ($ismember > 0) {
            $themechoice = true;
        } else {
            $themechoice = false;
        }
        return $themechoice;
    }
    public function getIssdmleader()
    {
        $user = $this->username;
        $ismember = Projectmember::find()
            ->select('*')
            ->where(['fk_project' => 1])
            ->andWhere(['pegawai' => $user])
            ->andWhere(['member_status' => 2])
            ->count();
        if ($ismember > 0) {
            $themechoice = true;
        } else {
            $themechoice = false;
        }
        return $themechoice;
    }
    public function getIssekretaris()
    {
        $user = $this->username;
        if ($user == 'sekbps17') {
            $themechoice = true;
        } else {
            $themechoice = false;
        }
        return $themechoice;
    }
    public function getIssuratmasukpejabat()
    {
        $username = $this->username;
        $pemberidisposisi = Suratmasukpejabat::find()->select('pegawai')->where('status = 1')->column();

        return in_array($username, $pemberidisposisi);
    }
    public function getIsteamleader()
    {
        $username = $this->username;
        $ketuatim = Teamleader::find()->select('nama_teamleader')->where('leader_status = 1')->column();

        return in_array($username, $ketuatim);
    }
}
