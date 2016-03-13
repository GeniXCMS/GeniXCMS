<?php

include_once dirname(__FILE__) . '/TestAuthContainer.php';


class DBContainer extends TestAuthContainer {


    function &getContainer() {
        //echo __METHOD__ . "\n";
        static $db_container;

        $file = 'DB.php';
        if (!$fp = @fopen($file, 'r', true)) {
            $this->markTestSkipped("$file package is not installed.");
        }
        fclose($fp);

        if(!isset($db_container)){
            require_once 'Auth/Container/DB.php';
            include dirname(__FILE__) . '/auth_container_db_options.php';
            $options = unserialize(PEAR_AUTH_TEST_OPTIONS);
            $db_container = new Auth_Container_DB($options);
            // Catch if DB connection cannot be made
            $res = $db_container->_prepare();
        }

        if (!DB::isConnection($db_container->db)) {
            $this->markTestSkipped("DB is not a connection object, check dsn");
        }

        return $db_container;
    }

    function &getExtraOptions() {
        include dirname(__FILE__) . '/auth_container_db_options.php';
        $extra_options = unserialize(PEAR_AUTH_TEST_EXTRA_OPTIONS);
        return $extra_options;
    }
}




?>
