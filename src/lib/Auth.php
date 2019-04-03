<?php

namespace App\lib;

use Symfony\Component\HttpFoundation\JsonResponse;
use Firebase\JWT\JWT;

/*
 * Class thats create, return and check the tokens implementing the JWT library.
 */

class Auth
{

    /*
     * Database object adapter.
     */

    private static $secret_key = 'Sdw1s9x8@';
    private static $encrypt  = ['HS256'];
    private static $aud = null;


    /**
     * Method thats create and return the tokens of app.
     *
     * @return String with token.
     */

    public static function SignIn($data)
    {
        $time = time();

        $token = array(
            'exp' => $time + (60*60),
            'aud' => self::Aud(),
            'data' => $data
        );

        return JWT::encode($token, self::$secret_key);
    }


    /**
     * Method thats check the tokens of app.
     *
     * @param String $token
     * @return Boolean true where it's valid and false where is not.
     */

    public static function Check($token)
    {
        if (empty($token)) {
            return false;
        }

        $decode = JWT::decode(
            $token,
            self::$secret_key,
            self::$encrypt
        );

        if ($decode->aud !== self::Aud()) {
            return false;
        }

        return true;
    }


    /**
     * Method thats return the data that was used for generate the token.
     *
     * @param String $token
     * @return Array with data.
     */

    public static function GetData($token)
    {
        return JWT::decode(
            $token,
            self::$secret_key,
            self::$encrypt
        )->data;
    }


    /**
     * Method thats get the auth info from the server.
     *
     * @return String with the info.
     */

    private static function Aud()
    {
        $aud = '';

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $aud = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $aud = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $aud = $_SERVER['REMOTE_ADDR'];
        }

        $aud .= @$_SERVER['HTTP_USER_AGENT'];
        $aud .= gethostname();

        return sha1($aud);
    }
}
