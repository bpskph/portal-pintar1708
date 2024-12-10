<?php
namespace app\models;
use Yii;
use yii\base\Model;

class EmailblastForm extends Model
{
    public $name;
    public $email;
    public $subject;
    public $body;

    public function rules()
    {
        return [
            // name, email, subject and body are required
            [['name', 'email', 'subject', 'body'], 'required'],
            // email has to be a valid email address
            ['email', 'email'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'verifyCode' => 'Verification Code',
        ];
    }
    public function contact($email)
    {
        if ($this->validate()) {
            Yii::$app->mailer->compose('contact', ['message' => $this->body, 'email' => $this->email])
                ->setFrom([$this->email => $this->name])
                ->setTo($email)
                ->setSubject($this->subject)
                ->send();
            return true;
        }
        return false;
    }
}
