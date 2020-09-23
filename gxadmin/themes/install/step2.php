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
<h3>Installation: Step 2</h3>
<form action="?step=3" method="post">
<div class="table-responsive">
    <table class="table table-responsive">
        <tr>
            <td>Administrator Name</td>
            <td>
                <div class="form-group">
                    <input type="text" name="adminname" class="form-control">
                    <span class="help-block">name of the administrator.</span>
                </div>
            </td>
        </tr>
        <tr>
            <td>Administrator Username</td>
            <td>
                <div class="form-group">
                    <input type="text" name="adminuser" class="form-control">
                    <span class="help-block">username of the administrator.</span>
                </div>
            </td>
        </tr>
        <tr>
            <td>Administrator Password</td>
            <td>
                <div class="form-group">
                    <input type="password" name="adminpass" class="form-control">
                    <span class="help-block">password of the administrator.</span>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <a href="?step=1" class="btn btn-danger">Back Step</a>
            </td>
            <td>
                <input type="submit" name="step2" value="Next Step" class="btn btn-primary"> 
            </td>
        </tr>
    </table>
</div>
</form>