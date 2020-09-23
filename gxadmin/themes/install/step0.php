<?php defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 * @package GeniXCMS
 * @since 0.0.1 build date 20150221
 * @version 1.1.11
 * @link https://github.com/semplon/GeniXCMS
 * 
 * @author Puguh Wijayanto <metalgenix@gmail.com>
 * @copyright 2014-2020 Puguh Wijayanto
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */
?>
<h3>Installation: Step 0</h3>
<form action="?step=1" method="post">
<div class="table-responsive">
    <table class="table table-responsive">
        <tr>
            <td>Database Name</td>
            <td >
                <div class="form-group">
                    <input type="text" name="dbname" class="form-control">
                    <span class="help-block">please create database first.</span>
                </div>
            </td>
        </tr>
        <tr>
            <td>Database Username</td>
            <td>
                <div class="form-group">
                    <input type="text" name="dbuser" class="form-control">
                    <span class="help-block">please insert database username.</span>
                </div>
            </td>
        </tr>
        <tr>
            <td>Database Password</td>
            <td>
                <div class="form-group">
                    <input type="text" name="dbpass" class="form-control">
                    <span class="help-block">please insert database password.</span>
                </div>
            </td>
        </tr>
        <tr>
            <td>Database Host</td>
            <td>
                <div class="form-group">
                    <input type="text" name="dbhost" class="form-control" value="localhost">
                    <span class="help-block">please insert database host. usually <kbd>localhost</kbd></span>
                </div>
            </td>
        </tr>
        <tr>
            <td></td>
            <td>
                <input type="submit" name="step1" value="Next Step" class="btn btn-primary">
            </td>
        </tr>
    </table>
</div>
</form>