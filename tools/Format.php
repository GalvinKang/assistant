<?php
namespace Ass\tools;

/**
 * kly
 * 验证数据格式
*/
final class Format
{
    /**
     * kly
     * 判断是否为合法的json
     * @param mixed $string
     * @return bool
    */
    public static function isLegalJson($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}