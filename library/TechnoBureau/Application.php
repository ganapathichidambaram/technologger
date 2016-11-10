<?php


class TechnoBureau_Application
{

    public static $DocumentRootPath = '' ;
    public static $config ='';
    private static $_instance;

    function __construct()
    {

    }

           public static final function getInstance()
           {
                   if (!self::$_instance)
                   {
                           self::$_instance = new self;
                   }

                   return self::$_instance;
           }


           public  function beginApp()
           {
               date_default_timezone_set('Asia/Kolkata');
               self::$DocumentRootPath=str_replace('\\', '/', getcwd());
               $configArray=include self::$DocumentRootPath.'/config.php';
               self::$config=$this->arrayToObject($configArray);

               error_reporting(E_ALL | E_STRICT & ~8192);
               set_error_handler(array('TechnoBureau_Error', 'handlePhpError'));
               set_exception_handler(array('TechnoBureau_Error', 'handleException'));
               register_shutdown_function(array('TechnoBureau_Error', 'handleFatalError'));


           }

           public  function  arrayToObject($array)
           {
               if (!is_array($array)) {
                   return $array;
               }

               $object = new stdClass();
               if (is_array($array) && count($array) > 0) {
                        foreach ($array as $name=>$value)
                        {
                            $name = strtolower(trim($name));
                            if (!empty($name))
                            {
                                $object->$name = $this->arrayToObject($value);
                            }
                        }
                        return $object;
                }
                else {
                    return FALSE;
                }
            }

    public function get($value) {

        return self::$$value;

        }


}
