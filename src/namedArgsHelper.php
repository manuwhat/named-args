<?php

namespace EZAMA{
    class namedArgsHelper
    {
        protected static function merge(&$arr1, &$arr2)
        {
            foreach ($arr1 as $k=>$v) {
                if (array_key_exists($k, $arr2)) {
                    $arr1[$k] = &$arr2[$k];
                }
            }
        }
        
        protected static function format($OneDarray)
        {
            if (!\is_array($OneDarray)) {
                return '';
            }
            if (\count($OneDarray) > 1) {
                $end = array_pop($OneDarray);
                return \join(',', $OneDarray)." and $end ";
            } else {
                return array_pop($OneDarray);
            }
        }
        
        protected static function throwError($error)
        {
            $error = (string) ($error);
            if (\version_compare(\PHP_VERSION, '7.0.0') < 0) {
                \trigger_error($error, \E_USER_ERROR);
            } else {
                throw new \Error($error);
            }
        }
        
        protected static function is_valid_associative($array)
        {
            if (!\is_array($array)) {
                return \false;
            }
            foreach ($array as $k=>$v) {
                if (!\preg_match('#^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$#', (string) $k)) {
                    return \false;
                }
            }
            return \true;
        }
        
        protected static function getReflection(&$func)
        {
            if (!\is_string($func)) {
                throw new \BadFunctionCallException('Try to call undefined Function');
            }
            try {
                $func = new \reflectionFunction($func);
            } catch (\ReflectionException $e) {
                throw new \BadFunctionCallException('Try to call undefined Function');
            }
        }
    }
}
