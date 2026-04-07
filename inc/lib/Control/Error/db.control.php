<?php defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.1 build date 20150219
 *
 * @version 2.1.0
 *
 * @link https://github.com/GeniXCMS/GeniXCMS
 * 
 *
 * @author Puguh Wijayanto <metalgenix@gmail.com>
 * @author GenixCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */
?>
<h3><i class="fa fa-database text-danger"></i> Database Error</h3>
Something went wrong with the database.<br />
<div class="alert alert-danger">
    <?php
    if (is_array($val)) {
        foreach ($val as $k => $v) {
            if (is_array($val[$k])) {
                echo '<ul class="list-unstyled">';
                for ($i = 0; $i < count($val[$k]); ++$i) {
                    echo '<li>' . $val[$k][$i] . '</li>';
                }
                echo '</ul>';
            } else {
                echo $val[$k];
            }
        }
    } else {
        echo $val;
    }

    ?>
</div>