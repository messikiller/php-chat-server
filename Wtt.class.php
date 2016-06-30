<?php
/**
 *	Wtt Protocol
 *
 *	@author heqiming
 */
class Wtt
{
    /**
     * error info in operation
     *
     * @var mixed
     */
    private $error_info;

    /**
     * function while check message legal
     *
     * @var string
     */
    private $check_func;

    /**
     * string added with base64_encode
     *
     * @var string
     */
    private $encode_salt;

    /**
     * flag between message header and body
     *
     * @var string
     */
    private $divider;

    public function __construct()
    {
        $this->error_info  = false;
        $this->encode_salt = '#%*';
        $this->check_func  = 'crc32';
        $this->divider     = ':';
    }

    /**
     * pack steps: encode->check->combine
     *
     * @param  string $msg
     * @return mixed
     */
    public function pack($msg)
    {
        $body = $this->encode($msg);
        $code = $this->getCheckCode($body);
        $arr  = array($code, $body);
        $str  = implode($this->divider, $arr);
        return $str;
    }

    /**
     * unpack steps: divide->check->decode
     *
     * @param  string $str
     * @return mixed
     */
    public function unpack($str)
    {
        $arr  = explode($this->divider, $str);
        $code = $arr[0];
        $body = $arr[1];

        if (! $this->checkStr($body, $code)) {
            $this->error_info = 'check false';
            return false;
        }

        $msg = $this->decode($body);
        return $msg;
    }

    /**
     * get error info for user
     *
     * @return mixed error info
     */
    public function getErrorInfo()
    {
        return $this->error_info;
    }

    /**
     * encode body message
     *
     * @param  string $msg messages to encode
     * @return string      encoded body string
     */
    private function encode($msg)
    {
        $salt   = $this->encode_salt;
        $str    = $salt . $msg . strrev($salt);
        $secret = base64_encode($str);
        return $secret;
    }

    /**
     * decode body message
     *
     * @param  string $secret encoded body string
     * @return string         decoded message string
     */
    private function decode($secret)
    {
        $str  = base64_decode($secret);
        $salt = $this->encode_salt;
        $str  = ltrim($str, $salt);
        $msg  = rtrim($str, strrev($salt));
        return $msg;
    }

    /**
     * get body string check code
     *
     * @param  string $body message body to send
     * @return string       specific check code
     */
    private function getCheckCode($body)
    {
        $func = $this->check_func;
        $code = sprintf("%u", $func($body));
        return $code;
    }

    /**
     * check body if legal or not
     *
     * @param  string $body message body string to check
     * @param  string $code header's check code
     * @return bool         check result
     */
    private function checkStr($body, $code)
    {
        $real_code = $this->getCheckCode($body);
        return $real_code == $code ? true : false;
    }
}
