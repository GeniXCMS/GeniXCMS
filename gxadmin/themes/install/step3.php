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
<h3>Installation: Step 3</h3>
<form action="?step=4" method="post">
<div class="table-responsive">
    <h4>Verify Installation</h4>
    <table class="table table-responsive">
        <tr>
            <td>Site Name</td><td><?php echo Session::val('sitename');?></td>
        </tr>
        <tr>
            <td>Site Slogan</td><td><?php echo Session::val('siteslogan');?></td>
        </tr>
        <tr>
            <td>Site Domain</td><td><?=Session::val('sitedomain');?></td>
        </tr>
        <tr>
            <td>Site URL</td><td><?=Session::val('siteurl');?></td>
        </tr>
        <tr>
            <td>Administrator Name</td><td><?=Session::val('adminname');?></td>
        </tr>
        <tr>
            <td>Administrator Username</td><td><?=Session::val('adminuser');?></td>
        </tr>
        <tr>
            <td>Administrator Password</td><td><?=Session::val('adminpass');?></td>
        </tr>
        <tr>
            <td></td><td><input type="submit" name="step3" value="Create Config File" class="btn btn-primary"> <a href="?step=2" class="btn btn-danger">Back Step</a></td>
        </tr>
    </table>
</div>
</form>
