<?php

require_once dirname(__FILE__) . '/TestAuthContainer.php';


class POP3aContainer extends TestAuthContainer {


    function &getContainer() {
        //echo __METHOD__ . "\n";
        static $pop3a_container;

        $file = 'Net/POP3.php';
        if (!$fp = @fopen($file, 'r', true)) {
            $this->markTestSkipped("$file package is not installed.");
        }
        fclose($fp);

        if(!isset($pop3a_container)){
            require_once 'Auth/Container/POP3.php';
            include dirname(__FILE__) . '/auth_container_pop3a_options.php';
            $pop3a_container = new Auth_Container_POP3($options);
        }
        return $pop3a_container;
    }

    function &getExtraOptions() {
        include dirname(__FILE__) . '/auth_container_pop3a_options.php';
        return $extra_options;
    }
}




?>
