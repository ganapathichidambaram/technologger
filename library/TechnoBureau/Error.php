<?

abstract class TechnoBureau_Error
{

    /*

TechnoBureau - Handle all Php Error in the application

*/
    public static function handlePhpError($errorType, $errorString, $file, $line)
    {

        if ($errorType & error_reporting())
        {
            $e = new ErrorException($errorString, 0, $errorType, $file, $line);
            TechnoBureau_Error::logException($e);
        }
    }

/*

TechnoBureau - Handle all Fatal Error in the application

*/
    public static function handleFatalError()
    {
        $error = @error_get_last();

        if (!$error)
        {
            return ;
        }

        if (empty($error['type']) || !($error['type'] & (E_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR)))
        {
            return ;
        }

        try
        {
            TechnoBureau_Error::logException(
                new ErrorException("Fatal Error: " . $error['message'], $error['type'], 1, $error['file'], $error['line'])
            );
        }

        catch (Exception $e) {

        }
    }


    /*
    TechnoBureau - Handle all Exceptional Error in the application
    */
    public static function handleException(Exception $e)
    {
        TechnoBureau_Error::logException($e);
    }


    public static function logException(Exception $e, $rollbackTransactions = true, $messagePrefix = '')
    {
        try
        {

            if(TechnoBureau_Application::getInstance()->get('config')->debug)
            echo self::getExceptionTrace($e);

            $LogError=self::getExceptionTrace($e,"txt");

            TechnoBureau_File::log("your",$LogError);
            exit;
        }

        catch (Exception $e) {
            if(TechnoBureau_Application::getInstance()->get('config')->debug)
                echo self::getExceptionTrace($e);

            $LogError=self::getExceptionTrace($e,"txt");
            TechnoBureau_File::log("your",$LogError);

            exit;
        }
    }


/*

TechnoBureau - Error Log Trace and print human readable format

*/

    public static function getExceptionTrace(Exception $e,$Ext='html')
    {

        $cwd = str_replace('\\', '/', getcwd());

        if (PHP_SAPI == 'cli')
        {
            $file = str_replace("$cwd/library/", '', $e->getFile());
            $trace = str_replace("$cwd/library/", '', $e->getTraceAsString());

            return PHP_EOL . "An exception occurred: {$e->getMessage()} in {$file} on line {$e->getLine()}" . PHP_EOL . $trace . PHP_EOL;
        }

        $traceHtml = '';
		$tracetxt = "\n--------------------------------\n Details \n--------------------------------\n"; $i=0;
        $tracearry  = array( );

        foreach ($e->getTrace() AS $traceEntry)
        {
            $function = ( isset($traceEntry['class']) ? $traceEntry['class'] . $traceEntry['type'] : '') . $traceEntry['function'];

            if (isset($traceEntry['file']))
            {

                $file = str_replace("$cwd/library/", '', str_replace('\\', '/', $traceEntry['file']));

            }

            else
            {
                $file = '';
            }

            $traceHtml .= "\t<li><b class=\"function\">" . htmlspecialchars($function) . "()</b>" . (isset($traceEntry['file']) && isset($traceEntry['line']) ? ' <span class="shade">in</span> <b class="file">' . $file . "</b> <span class=\"shade\">at line</span> <b class=\"line\">$traceEntry[line]</b>" : '') . "</li>\n";

            $tracetxt .= "\t". ++$i ." . " . htmlspecialchars($function) . "() " . (isset($traceEntry['file']) && isset($traceEntry['line']) ? ' in **' . $file . "** at line $traceEntry[line]" : '') . "\n";
            $tracearry[$i]=htmlspecialchars($function) . "() " . (isset($traceEntry['file']) && isset($traceEntry['line']) ? ' in **' . $file . "** at line $traceEntry[line]" : '');

        }

        $message = htmlspecialchars($e->getMessage());

        $file = htmlspecialchars($e->getFile());

        $line = $e->getLine();

        if($Ext=="html")

            return "<p>An exception occurred: $message in $file on line $line</p><ol>$traceHtml</ol>";

        else if($Ext =="array")

            return array('Message' => $message, 'File' => $file ,'Line' => $line, 'Socket_Trace' => $tracearry);

        else

            return "\n================================\n Error at ". date('d-M-Y H:i:s')."\n================================\n
                        An exception occurred: $message in $file on line $line \n $tracetxt \n";

        }
}
