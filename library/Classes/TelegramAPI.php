<?php

/**
 * Serviço para envio de mensagens via Telegram Bot API
 */
class TelegramAPI
{
    private static $token = '7916890316:AAE4fAXdq6sBGGqGfJsC4NIuE2PPHkF_gIo';
    private static $chat_id = '-4925780284';

    const CURL_TIMEOUT = 30;
    const MAX_MESSAGE_LENGTH = 4096;
    const MAX_RETRIES = 3;

    public static function configure($token, $chatId)
    {
        self::$token = $token;
        self::$chat_id = $chatId;
    }

    private static function sendTelegram($message, $async = true, $retry = 0)
    {
        if (empty(self::$token) || empty(self::$chat_id)) {
            error_log("TelegramAPI: Token ou Chat ID não configurados");
            return false;
        }

        $message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

        $url = 'https://api.telegram.org/bot' . self::$token . '/sendMessage';
        $params = http_build_query([
            'chat_id' => self::$chat_id,
            'parse_mode' => 'HTML',
            'text' => $message,
            'disable_web_page_preview' => true
        ]);

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $params,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => self::CURL_TIMEOUT,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_USERAGENT => 'TelegramAPI/2.0',
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded',
                'Accept: application/json'
            ]
        ]);

        if ($async) {
            return self::executeAsync($curl);
        } else {
            return self::executeSync($curl, $message, $retry);
        }
    }

    private static function executeAsync($curl)
    {
        try {
            $multiCurl = curl_multi_init();
            curl_multi_add_handle($multiCurl, $curl);
            $running = null;
            do {
                $status = curl_multi_exec($multiCurl, $running);
                if ($running) curl_multi_select($multiCurl, 0.1);
            } while ($running > 0 && $status === CURLM_OK);
            curl_multi_remove_handle($multiCurl, $curl);
            curl_multi_close($multiCurl);
            curl_close($curl);
            return true;
        } catch (Exception $e) {
            error_log("TelegramAPI async error: " . $e->getMessage());
            return false;
        }
    }

    private static function executeSync($curl, $message, $retry)
    {
        try {
            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $curlError = curl_error($curl);
            curl_close($curl);

            if ($response === false || !empty($curlError)) {
                throw new Exception("cURL Error: " . $curlError);
            }

            $decoded = json_decode($response);

            if ($httpCode !== 200 || !$decoded || !$decoded->ok) {
                $errorMsg = isset($decoded->description) ? $decoded->description : "HTTP Error: $httpCode";
                if ($retry < self::MAX_RETRIES && in_array($httpCode, [429, 500, 502, 503, 504])) {
                    sleep(pow(2, $retry));
                    return self::sendTelegram($message, false, $retry + 1);
                }
                throw new Exception("Telegram API Error: " . $errorMsg);
            }

            return $decoded;
        } catch (Exception $e) {
            error_log("TelegramAPI sync error: " . $e->getMessage());
            return false;
        }
    }

    private static function formatMessage($object)
    {
        try {
            if (is_array($object)) {
                return json_encode($object, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            } elseif (is_string($object)) {
                return $object;
            } elseif (is_scalar($object)) {
                return var_export($object, true);
            } elseif (is_null($object)) {
                return 'null';
            } elseif (is_object($object) && method_exists($object, '__toString')) {
                return (string) $object;
            } elseif (is_object($object)) {
                $json = json_encode($object, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                return (json_last_error() === JSON_ERROR_NONE) ? $json : print_r($object, true);
            } else {
                return print_r($object, true);
            }
        } catch (Exception $e) {
            return "Erro ao formatar: " . gettype($object);
        }
    }

    private static function truncateMessage($message)
    {
        if (mb_strlen($message, 'UTF-8') <= self::MAX_MESSAGE_LENGTH) {
            return $message;
        }
        return mb_substr($message, 0, self::MAX_MESSAGE_LENGTH - 10, 'UTF-8') . "\n\n[...]";
    }

    public static function echoT($object, $async = true)
    {
        try {
            $message = self::formatMessage($object);
            if (empty(trim($message))) return false;
            $message = self::truncateMessage(trim($message));
            $result = self::sendTelegram($message, $async);
            if (!$async && $result === false) {
                return self::sendTelegram($message, true) !== false;
            }
            return $result !== false;
        } catch (Exception $e) {
            error_log("TelegramAPI: " . $e->getMessage());
            return false;
        }
    }

    public static function send($message, $async = true)
    {
        return self::echoT($message, $async);
    }
}
