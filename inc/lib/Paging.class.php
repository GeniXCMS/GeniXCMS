<?php if(!defined('GX_LIB')) die("Direct Access Not Allowed!");
/*
*    GeniXCMS - Content Management System
*    ============================================================
*    Build          : 20140925
*    Version        : 0.0.1 pre
*    Developed By   : Puguh Wijayanto (www.metalgenix.com)
*    License        : MIT License
*    ------------------------------------------------------------
* filename : Paging.class.php
* version : 0.0.1 pre
* build : 20141002
*/

class Paging
{
    public function __construct() {
    }

    /* Paging Options 
    *
    *  $vars = array(
    *            'offset' => '',
    *            'table' => '',
    *            'where' => '',
    *            'max' => '',
    *            'url' => '',
    *            'type' => '', // pager or number
    *            'total' => ''
    *            );
    */

    public static function create($vars, $smart = false) {
        if(is_array($vars)) {
            if(isset($vars['where'])){
                $where = ' WHERE '.$vars['where'];
            }else{
                $where = '';
            }
            if(isset($vars['table'])){
                Db::result("SELECT * FROM `{$vars['table']}` {$where}");
                $dbtotal = Db::$num_rows;
            }
            
            if(isset($vars['total'])){
                $total = $vars['total'];
            }else{
                $total = $dbtotal;
            }
            
            if(isset($vars['type']) && $vars['type'] == 'number'){ // NUMBER
                $r = "<ul class=\"pagination\">";
                $maxpage = 7;
                $curr = $vars['paging'];
                if ($curr < $maxpage/2) {
                    # code...
                    $p=1;
                    if ($maxpage > ceil($total/$vars['max'])) {
                        # code...
                        $limit = ceil($total/$vars['max']);
                    }else{
                        $limit = $maxpage;
                    }
                    
                }elseif($curr + floor($maxpage/2) >= ceil($total/$vars['max'])){
                    $p = $vars['paging']-(ceil($maxpage/2)-1);
                    $limit = ceil($total/$vars['max']);
                    // echo "more total";
                }elseif($curr + floor($maxpage/2) > $maxpage){
                    $p = $vars['paging']-(ceil($maxpage/2)-1);
                    $limit = $curr+ceil($maxpage/2)-1;
                    // echo "more maxpage";
                }else{
                    $p = $vars['paging']-(ceil($maxpage/2)-1);
                    $limit = $curr + floor($maxpage/2);
                }

                
                for ($i=$p ; $i <= $limit /*ceil($total/$vars['max'])+1*/ ; $i++ ) { 
                    # code...
                    if($smart == true){
                        $url = $vars['url']."/paging/".$i;
                    }else{
                        $url = $vars['url']."&paging=".$i;
                    }
                    if($vars['paging'] == $i){ $sel = "class=\"active\"";}else{$sel='';}
                    $r .= "<li {$sel}><a href=\"{$url}\">$i</a></li>";
                }
                $r .= "</ul>";
            }elseif(isset($vars['type']) && $vars['type'] == 'pager'){ // PAGER
                $r = "<ul class=\"pager\">";
                $limit = ceil($total/$vars['max']);
                
                if($vars['paging'] == 1){
                    $prev = $vars['paging']+1;

                }elseif($vars['paging'] < $limit  || $vars['paging'] = $limit ){
                    $prev = ($vars['paging'])-1;
                    if($smart == true){
                    $url = $vars['url']."/paging/".$prev;
                    }else{
                        $url = $vars['url']."&paging=".$prev;
                    }
                    $r .= "<li class=\"pull-left\"><a href=\"{$url}\">Previous</a></li>";
                }
                
                if($vars['paging'] < $limit ){
                    $next = ($vars['paging'])+1 ;
                    # code...
                    if($smart == true){
                        $url = $vars['url']."/paging/".$next;
                    }else{
                        $url = $vars['url']."&paging=".$next;
                    }
                    $r .= "
                    <li class=\"pull-right\"><a href=\"{$url}\">Next</a></li>";
                }
                $r .= "</ul>";
            }

        }else{
            $r = "<alert>Query Error, in Array Please</alert>";
        }
        return $r;
    }
    
}

/* End of file Paging.class.php */
/* Location: ./inc/lib/Paging.class.php */