<?php defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('enc_id')) {

    function enc_id($id)
    {
        $CI =& get_instance();

        $encrypted = $CI->encryption->encrypt($id);

        return rtrim(strtr(base64_encode($encrypted), '+/', '-_'), '=');
    }
}

if (!function_exists('dec_id')) {

    function dec_id($enc_id)
    {
        $CI =& get_instance();

        // restore base64 format
        $enc_id = strtr($enc_id, '-_', '+/');
        $enc_id = base64_decode($enc_id);

        // decrypt
        return $CI->encryption->decrypt($enc_id);
    }
}