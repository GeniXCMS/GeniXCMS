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
<h3>Installation: Step 1</h3>
<form action="?step=2" method="post">
<div class="table-responsive">
    <table class="table table-responsive">
        <tr>
            <td>Site Name</td>
            <td>
                <div class="form-group">
                    <input type="text" name="sitename" class="form-control">
                    <span class="help-block">please insert the website name. this will be a title of your website.</span>
                </div>
            </td>
        </tr>
        <tr>
            <td>Site Slogan</td>
            <td>
                <div class="form-group">
                    <input type="text" name="siteslogan" class="form-control">
                    <span class="help-block">please insert the website slogan. if no slogan, you can leave it empty.</span>
                </div>
            </td>
        </tr>
        <tr>
            <td>Site Domain</td>
            <td>
                <div class="form-group">
                    <input type="text" name="sitedomain" class="form-control">
                    <span class="help-block">please insert the website domain. eg: <code>metalgenix.com</code></span>
                </div>
            </td>
        </tr>
        <tr>
            <td>Site URL</td>
            <td>
                <div class="form-group">
                    <input type="text" name="siteurl" class="form-control">
                    <span class="help-block">please insert the website url. eg: <code>http://metalgenix.com/</code> 
                    please use trailing slash at the end of the url.</span>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <a href="?" class="btn btn-danger">Back Step</a>
            </td>
            <td>
                <input type="submit" name="step1" value="Next Step" class="btn btn-primary"> 
                
            </td>
        </tr>
    </table>
</div>
</form>
