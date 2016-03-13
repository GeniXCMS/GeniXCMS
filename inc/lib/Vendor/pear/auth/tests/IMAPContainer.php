<?php

require_once dirname(__FILE__) . '/TestAuthContainer.php';


class IMAPContainer extends TestAuthContainer {


    function &getContainer() {
        //echo __METHOD__ . "\n";
        static $imap_container;

        if (!extension_loaded('imap')) {
            $this->markTestSkipped("This test needs the IMAP extension");
        }

        if(!isset($imap_container)){
            require_once 'Auth/Container/IMAP.php';
            include dirname(__FILE__) . '/auth_container_imap_options.php';
            $imap_container = new Auth_Container_IMAP($options);
        }
        return $imap_container;
    }

    function &getExtraOptions() {
        include dirname(__FILE__) . '/auth_container_imap_options.php';
        return $extra_options;
    }
}




?>
