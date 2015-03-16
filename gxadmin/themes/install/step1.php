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
<h3>Installation: Step 1</h3>
<form action="?step=2" method="post">
<div class="table-responsive">
    <table class="table table-responsive">
        <tr>
            <td>Site Name</td><td><input type="text" name="sitename" class="form-control"></td>
        </tr>
        <tr>
            <td>Site Slogan</td><td><input type="text" name="siteslogan" class="form-control"></td>
        </tr>
        <tr>
            <td>Site Domain</td><td><input type="text" name="sitedomain" class="form-control"></td>
        </tr>
        <tr>
            <td>Site URL</td><td><input type="text" name="siteurl" class="form-control"></td>
        </tr>
        <tr>
            <td></td><td><input type="submit" name="step1" value="Next Step" class="btn btn-primary"> <a href="?" class="btn btn-danger">Back Step</a></td>
        </tr>
    </table>
</div>
</form>