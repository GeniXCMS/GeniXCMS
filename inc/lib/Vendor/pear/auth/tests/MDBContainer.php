<?php

require_once dirname(__FILE__) . '/TestAuthContainer.php';


class MDBContainer extends TestAuthContainer {


    function &getContainer() {
        //echo __METHOD__ . "\n";
        static $mdb_container;

        $file = 'MDB.php';
        if (!$fp = @fopen($file, 'r', true)) {
            $this->markTestSkipped("$file package is not installed.");
        }
        fclose($fp);

        if(!isset($mdb_container)){
            require_once 'Auth/Container/MDB.php';
            include dirname(__FILE__) . '/auth_container_db_options.php';
            $options = unserialize(PEAR_AUTH_TEST_OPTIONS);
            $mdb_container = new Auth_Container_MDB($options);
            // Catch if DB connection cannot be made
            $res = $mdb_container->_prepare();
        }

        if (!MDB::isConnection($mdb_container->db)) {
            $this->markTestSkipped("MDB is not a connection object, check dsn");
        }
        return $mdb_container;
    }

    function &getExtraOptions() {
        include dirname(__FILE__) . '/auth_container_db_options.php';
        $extra_options = unserialize(PEAR_AUTH_TEST_EXTRA_OPTIONS);
        return $extra_options;
    }
}




?>
