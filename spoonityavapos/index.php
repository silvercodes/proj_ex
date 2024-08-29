<?php

/**
 * root path
 */
define('ROOT', dirname(__FILE__));

require_once ROOT . '/lib/Parser.php';
require_once ROOT . '/lib/Logger.php';
require_once ROOT . '/lib/Sender.php';

try {
    if(isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $log = Logger::getLogger();

        $inputJson = file_get_contents('php://input');

        if ($inputJson) {
            $parser = new Parser($inputJson);
            $sender = new Sender();

            // --> orders parsing
            $orders = $parser->parseOrders(); // returns array of orders
//            file_put_contents('./debugLogs/sentDataToSpoonity.json', json_encode($orders));

            // --> sending orders to the Spoonity API
            $apiAnswers = $sender->sendOrdersToApi($orders);
//            file_put_contents('./debugLogs/spoonityAnswers.json', $apiAnswers);

            // --> getting the userInfo using card_number
            if ($orders) {
                $card_number = $parser->getCardNumber();

                $userData = $sender->getUserData($card_number); // return object
//                file_put_contents('./debugLogs/userData.json', json_encode($userData));

                $hash = $userData->pos_session->hash;
                $userInfo = $sender->getUserInfo($hash, $card_number); // return object
//                file_put_contents('./debugLogs/userInfo.json', json_encode($userInfo));

                // --> attempt to update user on the AVAPOS platform
                $userObj = $parser->parseUser($userInfo); // returns array

                //region for debugging (creating a custom user)
//                $userObj = [
//                    'nombre' => 'Jose Antonio1111',
//                    'identificacion' => '0902571397',
//                    'direccion' => '205 Bolton St',
//                    'email' => 'jose@spoonity.com',
//                    'telefono' => ''
//                ];
                //endregion

                $avaposAnswer = $sender->updateUser($userObj);
//                var_dump('about update: ', $avaposAnswer);
                $avaposAnswer = stristr($avaposAnswer, '"');
                $avaposAnswer = '{'.$avaposAnswer.'}';

                $avaposAnswerObj = json_decode($avaposAnswer, true);
//                var_dump('json decoded:', $avaposAnswerObj);
                // --> if updating was successfully
                if (!empty($avaposAnswerObj)) {
                    if($avaposAnswerObj['result']['success'] === '1') {
//                        var_dump('User updated successfully: ' . $avaposAnswer);
                        $log->info('User updated successfully');
                    }
                } else {
                    $avaposAnswer = $sender->createUser($userObj);
//                    var_dump('User created successfully: ' . $avaposAnswer);
                    // TODO processing avapos answer
                    $log->info('User created successfully');
                }
            } else {
                $log->info('index.php says: orders array is empty');
            }
        } else {
            $log->info('index.php says: Request body is empty');
        }
    }
} catch (Exception $e) {
    var_dump($e->getMessage());
    $log->error('index.php says: ' . $e->getMessage());
}