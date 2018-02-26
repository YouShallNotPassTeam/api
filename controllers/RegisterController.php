<?php

namespace app\controllers;

use app\models\Registration;
use yii\db\Expression;
use yii\rest\Controller;
use yii\filters\ContentNegotiator;
use yii\web\Response;

class RegisterController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'contentNegotiator' => [
                'class' => ContentNegotiator::className(),
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                    // 'application/xml' => Response::FORMAT_XML,
                ],
            ],
            'corsFilter'  => [
                'class' => \yii\filters\Cors::className(),
                'cors'  => [
                    // restrict access to domains:
                    'Origin'                           => ['*'],
                    'Access-Control-Request-Method'    => ['POST'],
                    'Access-Control-Allow-Credentials' => true,
                    'Access-Control-Max-Age'           => 3600, // Cache (seconds)
                ],
            ],
        ]);
    }

    public function actionIndex()
    {
        $request = \Yii::$app->request;

        $registration = new Registration();
        $registration->first_name = $request->post('first_name');
        $registration->last_name = $request->post('last_name');
        $registration->email = $request->post('email');

        $registration->time = $request->post('time');
        $registration->hints = $request->post('hints');

        //$registration->score = ??;
        $registration->date = new Expression('NOW()');

        $saved = $registration->save();

        if ($saved) {
            return $this->serializeData([
                'success' => true,
                'errors' => []
            ]);
        } else {
            return $this->serializeData([
                'success' => false,
                'errors' => $registration->errors,
            ]);
        }

    }


    public function actionList()
    {
        $registrations = Registration::find()->orderBy('id')->all();

        $response = [];
        foreach($registrations as $each){
            $response[] = [
                'id' => $each->id,
                'first_name' => $each->first_name,
                'last_name' => $each->last_name,
                'email' => $each->email,
                'time' => $each->time,
                'hints' => $each->hints,
                'date' => $each->date,
            ];
        }
        return $this->serializeData($response);
    }
}

