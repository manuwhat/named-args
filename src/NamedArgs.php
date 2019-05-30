<?php
namespace EZAMA{
    class NamedArgs
    {
        protected $parameters=array();
        
        public function __construct($mandatory)
        {
            if (!\is_array($mandatory)) {
                throw new \InvalidArgumentException(\sprintf('parameter type must be array, %s given', \gettype($mandatory)));
            }
            $this->parameters=$mandatory;
        }
        
        protected static function merge(&$arr1, &$arr2)
        {
            foreach ($arr1 as $k=>$v) {
                if (array_key_exists($k, $arr2)) {
                    $arr1[$k]=&$arr2[$k];
                }
            }
        }
        
        protected static function format($OneDarray)
        {
            if (!\is_array($OneDarray)) {
                return '';
            }
            if (\count($OneDarray)>1) {
                $end=array_pop($OneDarray);
                return \join(',', $OneDarray)." and $end ";
            } else {
                return array_pop($OneDarray);
            }
        }
        
        protected static function throwError($error)
        {
            $error=(string)($error);
            if (\version_compare(\PHP_VERSION, '7.0.0') < 0) {
                \trigger_error($error, \E_USER_ERROR);
            } else {
                throw new \Error($error);
            }
        }
         
        
        protected static function &ProcessParams(&$argument, $required, $default)
        {
            $missing=array();
            if (!\is_array($argument)) {
                return \false;
            }
            $argument=array_intersect_key($argument, $default);//keep only predefined names
            //check for missing required parameters
            foreach ($required as $k=>$v) {
                if (!array_key_exists($v, $argument)) {
                    $missing[]=$v;
                }
            }

            if (!empty($missing)) {
                $function=\debug_backtrace();
                $function=\end($function);
                $function=$function['function'];
                $cm=\count($missing);
                $error=\call_user_func_array('sprintf', array('Function  %s\'s  Required '.($cm>1?'parameters %s are':'parameter %s is').' missing',$function,NamedArgs::format($missing)));
                self::throwError($error);
            }
            
            
            self::merge($default, $argument);//assign given values to parameters while keeping references
            return $default;
        }
        
        public function &getParams($required, $default)
        {
            if (self::is_valid_associative($this->parameters)) {
                return self::ProcessParams($this->parameters, $required, $default);
            } else {
                $cp=\count($this->parameters);
                if ($cp>=\count($required)) {
                    foreach (array_keys($default) as $k=>$v) {
                        if ($k===$cp) {
                            break;
                        }
                        $default[$v]=&$this->parameters[$k];
                    }
                    return self::ProcessParams($default, $required, $default);
                } else {
                    $function=\debug_backtrace();
                    $function=\end($function);
                    $function=$function['function'];

                    self::throwError(\sprintf('Function  %s : Two few parameters supplied', $function));
                }
            }
        }
        
        protected static function is_valid_associative($array)
        {
            if (!\is_array($array)) {
                return \false;
            }
            foreach ($array as $k=>$v) {
                if (!\preg_match('#^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$#', (string)$k)) {
                    return \false;
                }
            }
            return \true;
        }
        
        public static function __callStatic($name, $mandatory)
        {
            if (empty($mandatory)) {
                $mandatory[0]=[];
            }
            
            if ($mandatory[0] instanceof NamedArgs) {
                return self::func($name, $mandatory[0]);
            } elseif (\is_array($mandatory[0])) {
                return self::func($name, new self($mandatory[0]));
            }
            return \false;
        }
        
        
        protected static function func($func, NamedArgs $mandatory)
        {
            $args=&$mandatory->parameters;
            return self::processParamsAndArgs($func, $args);
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
        
        protected static function getValues(&$func, &$params, $paramsArgs, &$args, $associative)
        {
            foreach ((array)$params as $k=> $param) {
                $key=$associative?$param->name:$k;
                if (array_key_exists($key, $args)) {
                    $paramsArgs[]=&$args[$key];
                } else {
                    self::elseifGetValues($func, $param, $paramsArgs);
                }
            }
            return $paramsArgs;
        }
        
        protected static function handleOptional($notOptional, $func, $param)
        {
            if ($notOptional) {
                self::throwError(\sprintf('Function  %s\'s required parameter %s is missing', $func, $param));
            }
        }
        
        protected static function getParamDefaultValue(\reflectionParameter $param)
        {
            return $param->getDefaultValueConstantName()?\constant($param->getDefaultValueConstantName()):$param->getDefaultValue();
        }
        
        protected static function canGetParamDefaultValue(\reflectionFunction $func, \reflectionParameter $param)
        {
            return !$func->isInternal()&&($param->isDefaultValueAvailable()||$param->isDefaultValueConstant());
        }
        
        
        protected static function elseifGetValues(\reflectionFunction $func, \reflectionParameter $param, &$paramsArgs)
        {
            if (self::canGetParamDefaultValue($func, $param)) {
                $paramsArgs[] = self::getParamDefaultValue($param);
            } elseif ($param->allowsNull()) {
                $paramsArgs[]=null;
            } else {
                self::handleOptional(!$param->isOptional(), (string)$func->name, (string)$param->name);
            }
        }

        protected static function processParamsAndArgs($func, $args)
        {
            self::getReflection($func);
            $paramsArgs = array();
            $params =$func->getParameters();
            return $func->invokeArgs(self::getValues($func, $params, $paramsArgs, $args, self::is_valid_associative($args)));
        }
    }

}
