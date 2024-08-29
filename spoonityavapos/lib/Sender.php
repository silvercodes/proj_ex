<?php

require_once 'Logger.php';
require_once 'ConfigManager.php';

/**
 * Class Sender
 */
class Sender
{
    /**
     * @var Logger|null
     */
    private $log;

    /**
     * required Spoonity API configurations
     * @var array
     */
    private $spoonityApiConfig;

    /**
     * required AvaPos API configurations
     * @var array
     */
    private $avaposApiConfig;

    /**
     * Sender constructor.
     * @throws Exception
     */
    public function __construct() {
        $this->spoonityApiConfig = ConfigManager::getSpoonityApiConfig();
        $this->avaposApiConfig = ConfigManager::getAvaposApiConfig();
        $this->log = Logger::getLogger();
    }

    /**
     * Send a POST request
     * @param string $url
     * @param string $postData
     * @return string
     */
    private function SendPostRequest($url, $postData) {
        $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($postData)]
            );
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
            $output = curl_exec($ch);

            if (!curl_errno($ch)) {
                $info = curl_getinfo($ch);
//                var_dump('POST curl_http_code = ' . $info['http_code']);
                if ($info['http_code'] === 200) {
                    $this->log->debug("POST to $url sent successfully");
                } else {
                    $errno = $info['http_code'];
                    $this->log->error("Error $errno while sending POST");
                }
            }

        curl_close($ch);

        return $output;
    }

    /**
     * Send a GET request
     * @param string $url
     * @return string
     */
    private function SendGetRequest($url) {
        $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $output = curl_exec($ch);

            if (!curl_errno($ch)) {
                $info = curl_getinfo($ch);
//                var_dump('GET curl_http_code = ' . $info['http_code']);
                if ($info['http_code'] === 200) {
                    $this->log->debug("GET to $url sent successfully");
                } else {
                    $errno = $info['http_code'];
                    $this->log->error("Error $errno while sending GET");
                }
            }
        curl_close($ch);

        return $output;
    }

    /**
     * Send each order from array to Spoonity API
     * @param array $dataArray
     * @return false|string
     */
    public function sendOrdersToApi($dataArray) {
        $apiAnswers = [];

        if ($dataArray) {
            foreach ($dataArray as $data) {
                $url = $this->spoonityApiConfig['endpoint'] . '/order?api_key=' . $this->spoonityApiConfig['apikey'];
                $postData = json_encode($data);

                $output = $this->SendPostRequest($url, $postData);
                $apiAnswers[] = json_decode($output);
            }
        } else {
            $this->log->error('Sender->sendOrdersToApi says: Attempt to process empty array');
        }

        return json_encode($apiAnswers);
    }

    /**
     * Receive user data from /onscreen using POST
     * @param string $cardNumber
     * @return object(stdClass)
     */
    public function getUserData($cardNumber) {
        if ($cardNumber) {
            $url = $this->spoonityApiConfig['endpoint'] .
                '/onscreen?api_key=' . $this->spoonityApiConfig['apikey'];
            $data = [
                'card_number' => $cardNumber
            ];

            $postData = json_encode($data);

            $output = $this->SendPostRequest($url, $postData);
        } else {
            $this->log->error('Sender->getUserData says: Card_number is empty');
        }

        return json_decode($output, false);
    }

    /**
     * Receive full user info from /onscreen using GET
     * @param string $hash
     * @param string $cardNumber
     * @return object(stdClass)
     */
    public function getUserInfo($hash, $cardNumber) {
        if($hash && $cardNumber) {
            $url = $this->spoonityApiConfig['endpoint'] .
                '/onscreen?api_key=' . $this->spoonityApiConfig['apikey'] .
                '&pos_session_hash=' . $hash .
                '&card_number=' . $cardNumber;

            $output = $this->SendGetRequest($url);
        } else {
            $this->log->error('Sender->getUserInfo says: hash || cardNumber is empty');
        }

        return json_decode($output, false);
    }

    /**
     * Get request to AVAPOS platform for updating user
     * @param array $userObj
     * @return string
     */
    public function updateUser($userObj) {
        $body = [
            'key'       => $this->avaposApiConfig['apikey'],
            'action'    => 'set',
            'typedata'  => 'clientes',
            'data'      => json_encode($userObj)
        ];
        $url = $this->avaposApiConfig['endpoint'] . '?' . http_build_query($body);

        $output = $this->SendGetRequest($url);

        return $output;
    }

    /**
     * Get request to AVAPOS platform for creating new user
     * @param array $userObj
     * @return string
     */
    public function createUser($userObj) {
        $body = [
            'key'       => $this->avaposApiConfig['apikey'],
            'action'    => 'create',
            'typedata'  => 'clientes',
            'data'      => json_encode($userObj)
        ];
        $url = $this->avaposApiConfig['endpoint'] . '?' . http_build_query($body);

        $output = $this->SendGetRequest($url);

        return $output;
    }
}