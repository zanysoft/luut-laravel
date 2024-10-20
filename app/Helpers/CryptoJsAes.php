<?php

namespace App\Helpers;

use const OPENSSL_RAW_DATA;

class CryptoJsAes
{
    /**
     * Encrypt any value
     * @param mixed $value Any value
     * @return string
     */
    public static function encrypt($value)
    {
        $passphrase = date('Y');
        $salt = openssl_random_pseudo_bytes(8);
        $salted = '';
        $dx = '';
        while (strlen($salted) < 48) {
            $dx = md5($dx . $passphrase . $salt, true);
            $salted .= $dx;
        }
        $key = substr($salted, 0, 32);
        $iv = substr($salted, 32, 16);
        $encrypted_data = openssl_encrypt(json_encode($value), 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
        $data = ["b" => base64_encode($encrypted_data), "i" => bin2hex($iv), "s" => bin2hex($salt)];
        return json_encode($data);
    }

    /**
     * Decrypt a previously encrypted value
     * @param string $jsonStr Json stringified value
     * @return mixed
     */
    public static function decrypt(string $jsonStr)
    {
        $passphrase = date('Y');

        $json = json_decode($jsonStr, true);
        $ct = base64_decode($json["b"]);
        $iv = hex2bin($json["i"]);
        $salt = hex2bin($json["s"]);
        $concatedPassphrase = $passphrase . $salt;
        $md5 = [];
        $md5[0] = md5($concatedPassphrase, true);
        $result = $md5[0];
        $i = 1;
        while (strlen($result) < 32) {
            $md5[$i] = md5($md5[$i - 1] . $concatedPassphrase, true);
            $result .= $md5[$i];
            $i++;
        }
        $key = substr($result, 0, 32);
        $data = openssl_decrypt($ct, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
        return json_decode($data, true);
    }
}
