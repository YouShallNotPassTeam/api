<?php

namespace app\controllers;

use yii\rest\Controller;
use yii\filters\ContentNegotiator;
use yii\web\Response;

class ValidateController extends Controller
{
    private $format = 'json';
    public $enableCsrfValidation = false;


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
        $first_name = $request->post('first_name');
        $last_name = $request->post('last_name');
        $code = $request->post('code');

        $hint = $this->checkHint($code);
        $secret = $this->checkSecret($first_name, $last_name);
        $success = $this->checkSuccess($code);

        return $this->response($hint, $secret, $success);
    }

    public function response($hint = [], $secret = [], $success = false)
    {
        return $this->serializeData([
            'success' => $success,
            'hint' => $hint,
            'secret' => $secret,
        ]);
    }

    private function checkHint($code)
    {
        if ($code === "" || $code === NULL) {
            return ['emplty' => 'Secret Code is required.'];
        }

        if (!preg_match("([a-zA-Z])", $code)) {
            return ['numbers' => 'Secret Code should not contain only numbers.'];
        }

        if (!preg_match("([0-9])", $code)) {
            return ['letters' => 'Secret Code should not contain only letters.'];
        }

        $under_code = str_replace(' ', '_', $code);
        if (preg_match("(\W)", $under_code)) {
            return ['text' => 'Secret Code should not contain special chars.'];
        }

        if (!preg_match("(.+\s.+)", $code)) {
            return ['words' => 'Secret Code should not contain just one word.'];
        }

        if (preg_match("(a|e|y|i|o|A|E|Y|I|O)", $code)) {
            return ['3L11T' => 'Secret Code should not contain any vowels. V0w3ls 4r3 L4m3!'];
        }

        if (preg_match("((n0t\s.+) | (.+\sn0t\s.+) | (.+\sn0t))", $code)) {
            return ['YouShallNotPass' => 'Secret Code should not be so negative.'];
        }

        if ($code === '1 sh4ll p4ss' || $code === '1 Sh4ll P4ss') {
            return ['Ishallpass' => 'Success! Click _here_ to submit your score.'];
        }

        return [];
    }

    private function checkSecret($first_name, $last_name)
    {
        if (preg_match("(\';.+)", $first_name)) {
            return ['bobu' => "Boby'; tables"];
        }

        $names = $first_name . $last_name;
        if (strlen($names) > 4 &&
            !preg_match("(a|e|y|i|o|A|E|Y|I|O)", $names) &&
            preg_match("([a-zA-Z])", $names) &&
            preg_match("([0-9])", $names)
        ) {
            return ['3l1tN4m3' => "3l1t N4m3!"];
        }

        if($names == 'ChuckNorris'){
            return ['chuck' => "Chuck Norris Approved"];
        }

        return [];
    }

    private function checkSuccess($code)
    {
        if ($code === '1 sh4ll p4ss' || $code === '1 Sh4ll P4ss') {
            return true;
        }
        return false;
    }

}
