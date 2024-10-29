<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pengguna".
 *
 * @property string $username
 * @property string $password
 * @property int $nip
 * @property string $nama
 * @property string $tgl_daftar
 * @property int $admin
 * @property int $theme
 */
class Pengguna extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public $password_repeat;
    public static function tableName()
    {
        return 'pengguna';
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username', 'password', 'nip', 'nama', 'nomor_hp'], 'required'],
            [['nip', 'nipbaru', 'level', 'theme'], 'integer'],
            [['tgl_daftar'], 'safe'],
            [['username'], 'string', 'max' => 50],
            [['password', 'nama'], 'string', 'max' => 255],
            [['username', 'nip', 'nipbaru'], 'unique'],
            [['nipbaru'], 'string', 'max' => 18],
            [['nipbaru'], 'string', 'min' => 18],
            [['nip'], 'string', 'max' => 9],
            [['nip'], 'string', 'min' => 9],
            ['password_repeat', 'required', 'skipOnEmpty' => !$this->isNewRecord],
            ['password_repeat', 'compare', 'compareAttribute' => 'password', 'message' => "Password tidak sesuai"],
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'username' => 'Username',
            'password' => 'Password',
            'nip' => 'NIP BPS (9 Digit)',
            'nipbaru' => 'NIP (18 Digit)',
            'nama' => 'Nama',
            'tgl_daftar' => 'Tgl Daftar',
            'level' => 'Akses Level',
            'theme' => 'Theme',
            'nomor_hp' => 'Nomor HP (WhatsApp)',
        ];
    }
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->password = md5($this->password);
                return true;
            } else
                return true;
        } else {
            return false;
        }
    }
    public function getFormattedNip()
    {
        $number = str_pad($this->nipbaru, 18, "0", STR_PAD_LEFT);
        $part1 = substr($number, 0, 8);
        $part2 = substr($number, 8, 6);
        $part3 = substr($number, 14, 1);
        $part4 = substr($number, 15, 3);
        return $part1 . ' ' . $part2 . ' ' . $part3 . ' ' . $part4;
    }
    public function getFormattedPhoneNumber()
    {
        // Ensure the phone number starts with "62"
        if (substr($this->nomor_hp, 0, 2) !== '62') {
            $phoneNumber = '62' . ltrim($this->nomor_hp, '0');
        }

        // Remove any non-digit characters
        $phoneNumber = preg_replace('/\D/', '', $this->nomor_hp);

        // Trim the phone number to at most 14 digits
        $phoneNumber = substr($phoneNumber, 0, 14);

        // Format the phone number into groups
        $part1 = substr($phoneNumber, 0, 2);  // Country code
        $part2 = substr($phoneNumber, 2, 3);  // First part
        $part3 = substr($phoneNumber, 5, 4);  // Second part
        $part4 = substr($phoneNumber, 9, 4);  // Third part

        // Return the formatted phone number
        return "+$part1-$part2-$part3-$part4";
    }

    public function getProjectmembere()
    {
        return $this->hasMany(Projectmember::className(), ['pegawai' => 'username']);
    }
    public function getProjecte()
    {
        return $this->hasMany(Project::className(), ['id_project' => 'fk_project'])->via('projectmembere');
    }
    public function getTeamleadere()
    {
        return $this->hasOne(Teamleader::className(), ['nama_teamleader' => 'username']);
    }
}
