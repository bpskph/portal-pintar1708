<?php
namespace app\models;
use Yii;
use yii\base\Model;

class EmailForm extends Model {
    public $email;
    public function rules() {
        return [
            [['email'], 'required'],
            [['email'], 'email'],
        ];
    }
    public function attributeLabels() {
        return array(
            'email' => 'email@bps.go.id',
        );
    }
}
