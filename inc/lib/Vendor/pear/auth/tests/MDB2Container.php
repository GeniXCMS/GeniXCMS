<?php

require_once dirname(__FILE__) . '/TestAuthContainer.php';


class MDB2Container extends TestAuthContainer {

    function &getContainer() {
        //echo __METHOD__ . "\n";
        static $mdb2_container;
        static $mdb2_dsninfo;
        static $mdb2_databases;

        // Workaround for PHPUnit messing with globals.
        if (!empty($mdb2_container)) {
            $GLOBALS['_MDB2_dsninfo_default'] = $mdb2_dsninfo;
            $GLOBALS['_MDB2_databases'] = $mdb2_databases;
        }

        $file = 'MDB2.php';
        if (!$fp = @fopen($file, 'r', true)) {
            $this->markTestSkipped("$file package is not installed.");
        }
        fclose($fp);

        if (!isset($mdb2_container)) {
            require_once 'Auth/Container/MDB2.php';
            include dirname(__FILE__) . '/auth_container_db_options.php';
            $options = unserialize(PEAR_AUTH_TEST_OPTIONS);
            $mdb2_container = new Auth_Container_MDB2($options);
            // Catch if DB connection cannot be made
            $res = $mdb2_container->_prepare();
            if (PEAR::isError($res)) {
                $this->markTestSkipped($res->getUserInfo());
            }
        }

        if (!MDB2::isConnection($mdb2_container->db)) {
            $this->markTestSkipped("MDB2 is not a connection object, check dsn");
        }

        // Workaround for PHPUnit messing with globals.
        if (!$mdb2_dsninfo) {
            $mdb2_dsninfo = $GLOBALS['_MDB2_dsninfo_default'];
            $mdb2_databases = $GLOBALS['_MDB2_databases'];
        }

        return $mdb2_container;
    }

    function &getExtraOptions() {
        include dirname(__FILE__) . '/auth_container_db_options.php';
        $extra_options = unserialize(PEAR_AUTH_TEST_EXTRA_OPTIONS);
        return $extra_options;
    }
}
?>
