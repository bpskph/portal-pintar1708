<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Notification extends ActiveRecord
{
    public static function tableName()
    {
        return 'notification';
    }

    public function rules()
    {
        return [
            [['user_id', 'message'], 'required'],
            [['is_read'], 'integer'],
            [['message', 'user_id'], 'string'],
            [['created_at'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'link' => 'Topik',
            'message' => 'Pesan',
            'created_at' => 'Dikirim Pada',
        ];
    }

    public static function createNotification($userId, $message, $link, $linkId)
    {
        $notification = new self();
        $notification->user_id = $userId;
        $notification->message = $message;
        $notification->link = $link;
        $notification->link_id = $linkId;
        $notification->save();
        // if ($notification->save()) {
        //     Yii::info('Pusher event triggered: ' . json_encode(['message' => $message, 'user_id' => $userId, 'title' => $title]));
        // } else {
        //     Yii::error('Failed to save notification: ' . json_encode($notification->errors));
        // }
    }
}
