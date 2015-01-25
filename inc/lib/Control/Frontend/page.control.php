<?php if(!defined('GX_LIB')) die("Direct Access Not Allowed!");
/*
*    GeniXCMS - Content Management System
*    ============================================================
*    Build          : 20140925
*    Version        : 0.0.1 pre
*    Developed By   : Puguh Wijayanto (www.metalgenix.com)
*    License        : MIT License
*    ------------------------------------------------------------
* filename : page.control.php
* version : 0.0.1 pre
* build : 20141006
*/

            switch ($_GET['page']) {
                case 'sitemap':
                    # code...
                    Sitemap::create();
                    break;
                
                default:
                    # code...
                    $page = $_GET['page'];
                    $data['posts'] = Db::result(
                                        sprintf("SELECT * FROM `posts` 
                                                WHERE `id` = '%d' 
                                                OR `slug` = '%s' 
                                                LIMIT 1", 
                                                $page, 
                                                $page
                                                )
                                        );
                    Theme::theme('header', $data);
                    Theme::theme('page', $data);
                    Theme::footer();
                    break;
            }

/* End of file page.control.php */
/* Location: ./inc/lib/Control/Frontend/page.control.php */