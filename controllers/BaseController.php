<?php

namespace app\controllers;

use yii\web\Controller;
use app\components\AccessLogsBehavior;

class BaseController extends Controller
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'actionLog' => [
                'class' => AccessLogsBehavior::class,
            ],
        ]);
    }
}
