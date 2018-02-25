<?php

namespace app\controllers;

use yii\rest\Controller;

class ValidateController extends Controller
{
    private $format = 'json';
    public $enableCsrfValidation = true;

    public function actionIndex()
    {
        return $this->serializeData(['success'=>'true']);
    }

}
