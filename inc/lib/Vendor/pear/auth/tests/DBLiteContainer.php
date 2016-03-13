<?php

require_once dirname(__FILE__) . '/TestAuthContainer.php';


class DBLiteContainer extends TestAuthContainer {

    function &getContainer() {
        //echo __METHOD__ . "\n";
        static $dblite_container;

        $file = 'DB.php';
        if (!$fp = @fopen($file, 'r', true)) {
            $this->markTestSkipped("$file package is not installed.");
        }
        fclose($fp);

        if(!isset($dblite_container)){
            require_once 'Auth/Container/DBLite.php';
            include dirname(__FILE__) . '/auth_container_db_options.php';
            $options = unserialize(PEAR_AUTH_TEST_OPTIONS);
            $dblite_container = new Auth_Container_DBLite($options);
            // Catch if DB connection cannot be made
            $res = $dblite_container->_prepare();
        }

        if(!DB::isConnection($dblite_container->db)){
            $this->markTestSkipped("DB is not a connection object, check dsn");
        }
        return $dblite_container;
    }

    function &getExtraOptions() {
        include dirname(__FILE__) . '/auth_container_db_options.php';
        $extra_options = unserialize(PEAR_AUTH_TEST_EXTRA_OPTIONS);
        return $extra_options;
    }
}




?>
