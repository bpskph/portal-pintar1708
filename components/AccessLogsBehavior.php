<?php
namespace app\components;

use Yii;
use yii\base\Behavior;
use yii\web\Controller;
use app\models\AccessLogs;

class AccessLogsBehavior extends Behavior
{
    public function events()
    {
        return [
            Controller::EVENT_AFTER_ACTION => 'logAction',
        ];
    }

    public function logAction($event)
    {
        $log = new AccessLogs();
        $log->controller = $event->action->controller->id;
        $log->action = $event->action->id;
        $log->user_id = Yii::$app->user->isGuest ? null : Yii::$app->user->id;
        $log->user_ip = Yii::$app->request->userIP;
        $log->user_agent = Yii::$app->request->userAgent;
        $log->save();
    }
}
