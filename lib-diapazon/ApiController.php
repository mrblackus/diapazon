<?php
/**
 * Created by PhpStorm.
 * User: mathieu.savy
 * Date: 04/12/2013
 * Time: 14:51
 */

namespace Diapazon;

abstract class ApiController extends Controller
{
    const API_VERSION = '1.0';

    public function before_filter($params = array())
    {
        self::json_header();
    }

    protected static function error_500()
    {
        header('HTTP/1.0 500 Internal Server Error');
    }

    protected static function json_header()
    {
        header('Content-type: application/json');
    }

    /**
     * @param string $name
     * @param mixed $default
     * @return string
     */
    protected function getParam($name, $default = null)
    {
        return \Diapazon\HttpUtils::getHttpGetParam($name, $default);
    }

    /**
     * @param string$name
     * @param mixed $default
     * @return string
     */
    protected function getPostParam($name, $default = null)
    {
        return \Diapazon\HttpUtils::getHttpPostParam($name, $default);
    }

    /**
     * @param mixed $data
     * @return string
     */
    protected function jsonReturn($data)
    {
        echo json_encode(array(
            'api_version' => self::API_VERSION,
            'success'     => true,
            'error_code'  => '',
            'data'        => $data
        ));
    }

    /**
     * @param string $errorCode
     * @return string
     */
    protected function jsonError($errorCode)
    {
        \Diapazon\Log::logError('apiController::jsonError : ' . $errorCode);
        self::error_500();

        echo json_encode(array(
            'api_version' => self::API_VERSION,
            'success'     => false,
            'error_code'  => $errorCode,
            'data'        => ''
        ));
    }

    /*
     * @param string $url
     * @param array $postFields
     */
    protected function getApiResponse($url, $postFields = null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_COOKIESESSION, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postFields);

        \Diapazon\Log::logInfo('ApiController::getApiResponser : POST '.$url);
        $loginAccountResponse = curl_exec($curl);
        curl_close($curl);
        return $loginAccountResponse;
    }

    /**
     * @param string $url
     * @return string
     */
    protected function getRequestOn($url)
    {
        \Diapazon\Log::logInfo('ApiController::getRequestOn : GET '.$url);
        return @file_get_contents($url);
    }

    /**
     * Return true if input text is true, false otherwise
     * @param $str
     * @return bool
     */
    protected function stringToBool($str)
    {
        return strtolower(trim($str)) == 'true';
    }
} 