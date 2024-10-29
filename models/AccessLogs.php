<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "access_logs".
 *
 * @property int $id
 * @property string|null $controller
 * @property string|null $action
 * @property string|null $user_id
 * @property string $user_ip
 * @property string|null $user_agent
 * @property string|null $timestamp
 */
class AccessLogs extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'access_logs';
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
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
