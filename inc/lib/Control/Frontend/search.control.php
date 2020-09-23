<?php if(!defined('GX_LIB')) die("Direct Access Not Allowed!");
/**
 * GeniXCMS - Content Management System
 * 
 * PHP Based Content Management System and Framework
 *
 * @package GeniXCMS
 * @since 0.0.1 build date 20141006
 * @version 1.1.11
 * @link https://github.com/semplon/GeniXCMS
 * 
 * @author Puguh Wijayanto (www.metalgenix.com)
 * @copyright 2014-2015 Puguh Wijayanto
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 *
 */
System::gZip(true);

$data = Router::scrap($param);
if (isset($_GET['q'])) {
    # code...

    $q = Typo::cleanX($_GET['q']);
    $data['sitetitle'] = "Search: ".$q;
    $sq = explode(' ', $q);
    $where = '';
    $pwhere = '';
    foreach ($sq as $k) {
        $where .= "AND (A.`title` LIKE '%{$k}%' 
                                OR A.`content` LIKE '%{$k}%') ";
        $pwhere .= "AND (`title` LIKE '%{$k}%' 
                                OR `content` LIKE '%{$k}%') ";
    }

    $data['q'] = $q;
}else{
    $q = '';
    $data['sitetitle'] = "Search: ";
    $where = '';
    $pwhere = '';
    $data['q'] = '';
}
$data['max'] = Options::get('post_perpage');

if(isset($_GET['paging'])){
    $paging = sprintf('%d', $_GET['paging']);
    if($paging > 0) {
        $offset = ($paging-1)*$data['max'];
    }else{
        $offset = 0;
    }
    $qpage = "?&q={$q}&token={$_GET['token']}";
}else{
    $paging = 1;
    $offset = 0;
    $qpage = "?&q={$q}&token={$_GET['token']}";
}
$url = Url::search().$qpage;
$paging = array(
                'paging' => $paging,
                'table' => 'posts',
                'where' => '`type` = \'post\' AND `status` = \'1\' '.$pwhere,
                'max' => $data['max'],
                'url' => $url,
                'type' => 'number'
            );
$data['paging'] =  Paging::create($paging);

//echo $paging;
$data['posts'] = Db::result(
                        sprintf("SELECT * FROM `posts`
                            WHERE `type` = 'post'
                            %s
                            AND `status` = '1'
                            ORDER BY `date` 
                            DESC LIMIT %d, %d",
                            $where, $offset, $data['max']
                            )
                        );
$data['num'] = Db::$num_rows;

Theme::theme('header',$data);
Theme::theme('search', $data);
Theme::footer($data);

System::Zipped();
/* End of file default.control.php */
/* Location: ./inc/lib/Control/Frontend/default.control.php */