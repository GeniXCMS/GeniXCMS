<?php

require_once dirname(__FILE__) . '/TestAuthContainer.php';

class FileContainer extends TestAuthContainer {


    function &getContainer() {
        //echo __METHOD__ . "\n";
        static $file_container;
        static $file_passwd;

        // Workaround for PHPUnit messing with globals.
        if (!empty($file_container)) {
            $GLOBALS['_FILE_PASSWD_64'] = $file_passwd;
        }

        $file = 'File/Passwd.php';
        if (!$fp = @fopen($file, 'r', true)) {
            $this->markTestSkipped("$file package is not installed.");
        }
        fclose($fp);

        if(!isset($file_container)){
            require_once 'Auth/Container/File.php';
            include dirname(__FILE__) . '/auth_container_file_options.php';
            $file_container = new Auth_Container_File($options);
        }

        // Workaround for PHPUnit messing with globals.
        if (!$file_passwd) {
            $file_passwd = $GLOBALS['_FILE_PASSWD_64'];
        }

        return $file_container;
    }

    function &getExtraOptions() {
        include dirname(__FILE__) . '/auth_container_file_options.php';
        return $extra_options;
    }
}




?>
