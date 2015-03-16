<?php if(!defined('GX_LIB')) die("Direct Access Not Allowed!");
/**
* GeniXCMS - Content Management System
* 
* PHP Based Content Management System and Framework
*
* @package GeniXCMS
* @since 0.0.1 build date 20150219
* @version 0.0.3
* @link https://github.com/semplon/GeniXCMS
* @link http://genixcms.org
* @author Puguh Wijayanto (www.metalgenix.com)
* @copyright 2014-2015 Puguh Wijayanto
* @license http://www.opensource.org/licenses/mit-license.php MIT
*
*/
?>
<div class="row">
    <div class="col-sm-12">
        <h1>Not Allowed !!</h1><hr>You don't have Access to this page. Maybe You want to go to <a href="<?=Options::get('siteurl');?>">frontpage</a> or just <a href="logout.php">Logout</a>
    </div>
</div>

<style>
    #page-wrapper {
        margin-left: 0px!important;
    }
</style>