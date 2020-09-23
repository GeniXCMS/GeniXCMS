<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.1 build date 20140925
 *
 * @version 1.1.11
 *
 * @link https://github.com/semplon/GeniXCMS
 * 
 *
 * @author Puguh Wijayanto <metalgenix@gmail.com>
 * @copyright 2014-2020 Puguh Wijayanto
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */
class Paging
{
    public function __construct()
    {
    }

    /**
     * Paging Options.
     *
     * <code>
     *     $vars = array(
     *             'offset' => '',
     *             'table' => '',
     *             'where' => '',
     *             'max' => '',
     *             'url' => '',
     *             'type' => '', // pager or number
     *             'total' => ''
     *             );
     *</code>
     */
    public static function create($vars, $smart = false)
    {
        if (is_array($vars)) {
            if (isset($vars['where'])) {
                $where = ' WHERE '.$vars['where'];
            } else {
                $where = '';
            }
            if (isset($vars['table'])) {
                if (is_array($vars['table'])) {
                    $table = '';
                    $on = " ON ";
                    $total_table = count($vars['table']);
                    $i = 0;
                    foreach ($vars['table'] as $k => $v) {
                        $i = $i + 1;
                        $join = ($i < $total_table) ? $v[1]: '';
                        $table .= "`{$k}` AS {$v[0]} {$join} ";
                        $on .= " {$v[0]}.`{$v[2]}` =";
                    }
                    $on = substr($on, 0, -1);
                    $table = $table.$on;
                    $sel = $vars['select'];
                } else {
                    $table = "`".Typo::cleanX($vars['table'])."`";
                    $sel = '`id`';
                }
                
//                echo $table;
                Db::result("SELECT {$sel} FROM {$table} {$where}");
                $dbtotal = Db::$num_rows;
            }

            if (isset($vars['total'])) {
                $total = Typo::int($vars['total']);
            } else {
                $total = $dbtotal;
            }

            if (isset($vars['type']) && $vars['type'] == 'number') { // NUMBER
                $r = '<ul class="pagination pagination-sm no-margin pull-right">';
                $maxpage = 7;
                $curr = Typo::int($vars['paging']);
                $max = Typo::int($vars['max']);
                if ($curr < $maxpage / 2) {
                    $p = 1;
                    if ($maxpage > ceil($total / $max)) {
                        $limit = ceil($total / $max);
                    } else {
                        $limit = $maxpage;
                    }
                } elseif ($curr + floor($maxpage / 2) >= ceil($total / $max)) {
                    $p = $curr - (ceil($maxpage / 2) - 1);
                    $limit = ceil($total / $max);
                    // echo "more total";
                } elseif ($curr + floor($maxpage / 2) > $maxpage) {
                    $p = $curr - (ceil($maxpage / 2) - 1);
                    $limit = $curr + ceil($maxpage / 2) - 1;
                    // echo "more maxpage";
                } else {
                    $p = $curr - (ceil($maxpage / 2) - 1);
                    $limit = $curr + floor($maxpage / 2);
                }

                for ($i = $p; $i <= $limit /*ceil($total/$vars['max'])+1*/; ++$i) {
                    if ($smart == true) {
                        $url = $vars['url'].'paging/'.$i.'/';
                    } else {
                        $url = $vars['url'].'&paging='.$i;
                    }
                    if ($curr == $i) {
                        $sel = 'class="active"';
                    } else {
                        $sel = '';
                    }
                    $r .= "<li {$sel}><a href=\"{$url}\">$i</a></li>";
                }
                $r .= '</ul>';
            } elseif (isset($vars['type']) && $vars['type'] == 'pager') { // PAGER
                $r = '<ul class="pagination-sm no-margin pager">';
                $limit = ceil($total / $vars['max']);
                $curr = Typo::int($vars['paging']);
                $max = Typo::int($vars['max']);

                if ($curr == 1) {
                    $prev = $curr + 1;
                } elseif ($curr < $limit  || $curr = $limit) {
                    $prev = ($curr) - 1;
                    if ($smart == true) {
                        $url = $vars['url'].'paging/'.$prev.'/';
                    } else {
                        $url = $vars['url'].'&paging='.$prev;
                    }
                    $r .= "<li class=\"pull-left\"><a href=\"{$url}\">Previous</a></li>";
                }

                if ($curr < $limit) {
                    $next = ($curr) + 1;

                    if ($smart == true) {
                        $url = $vars['url'].'paging/'.$next.'/';
                    } else {
                        $url = $vars['url'].'&paging='.$next;
                    }
                    $r .= "
                    <li class=\"pull-right\"><a href=\"{$url}\">Next</a></li>";
                }
                $r .= '</ul>';
            }
        } else {
            $r = '<alert>Query Error, in Array Please</alert>';
        }

        return $r;
    }
}

/* End of file Paging.class.php */
/* Location: ./inc/lib/Paging.class.php */
