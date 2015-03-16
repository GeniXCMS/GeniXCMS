<?php if(!defined('GX_LIB')) die("Direct Access Not Allowed!");
/**
* GeniXCMS - Content Management System
* 
* PHP Based Content Management System and Framework
*
* @package GeniXCMS
* @since 0.0.1 build date 20150221
* @version 0.0.3
* @link https://github.com/semplon/GeniXCMS
* @link http://genixcms.org
* @author Puguh Wijayanto (www.metalgenix.com)
* @copyright 2014-2015 Puguh Wijayanto
* @license http://www.opensource.org/licenses/mit-license.php MIT
*
*/
?>
<h3>Installation: Step 0</h3>
<form action="?step=1" method="post">
<div class="table-responsive">
    <table class="table table-responsive">
        <tr>
            <td>Database Name</td><td><input type="text" name="dbname" class="form-control"></td>
        </tr>
        <tr>
            <td>Database Username</td><td><input type="text" name="dbuser" class="form-control"></td>
        </tr>
        <tr>
            <td>Database Password</td><td><input type="text" name="dbpass" class="form-control"></td>
        </tr>
        <tr>
            <td>Database Host</td><td><input type="text" name="dbhost" class="form-control" value="localhost"></td>
        </tr>
        <tr>
            <td></td><td><input type="submit" name="step1" value="Next Step" class="btn btn-primary"></td>
        </tr>
    </table>
</div>
</form>