<?php

namespace app\models;

class AccessLogs extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'access_logs';
    }
    public function rules()
    {
        return [
            [['user_ip'], 'required'],
            [['user_agent'], 'string'],
            [['timestamp'], 'safe'],
            [['controller', 'action'], 'string', 'max' => 255],
            [['user_id'], 'string', 'max' => 50],
            [['user_ip'], 'string', 'max' => 45],
        ];
    }
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'controller' => 'Controller',
            'action' => 'Action',
            'user_id' => 'User ID',
            'user_ip' => 'User IP',
            'user_agent' => 'User Agent',
            'timestamp' => 'Timestamp',
        ];
    }
    public function getPenggunae()
    {
        return $this->hasOne(Pengguna::className(), ['username' => 'user_id']);
    }
}
