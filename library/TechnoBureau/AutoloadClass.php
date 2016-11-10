<?php

class TechnoBureau_AutoloadClass
{

    function __construct() {

        spl_autoload_register(array($this, 'TBautoload'));

    }


    public function TBautoload($class)
    {

        if (class_exists($class, false) || interface_exists($class, false))
        {
            print "true";
        }

        if ($class == 'utf8_entity_decoder')
        {
            return true;
        }

        $filename = $this->autoloaderClassToFile($class);
        //print $filename."\n";

        if (!$filename)
        {
            return false;
        }

        if (file_exists($filename))
        {
            include($filename);
            return (class_exists($class, false) || interface_exists($class, false));
        }

    return false;
    }

    public function autoloaderClassToFile($class)
    {
        if (preg_match('#[^a-zA-Z0-9_\\\\]#', $class))
        {
            return false;
        }

        return  'library/' . str_replace(array('_', '\\'), '/', $class) . '.php';
    }



}
