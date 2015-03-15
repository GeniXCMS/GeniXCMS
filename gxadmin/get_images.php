<?php 

define('GX_PATH', realpath(__DIR__ . '/../'));
define('GX_LIB', GX_PATH.'/inc/lib/');
define('GX_MOD', GX_PATH.'/inc/mod/');
define('GX_THEME', GX_PATH.'/inc/themes/');
define('GX_ASSET', GX_PATH.'/assets/');

function __autoload($f) {
    require GX_LIB. $f . '.class.php';
}

new System();
new Db();
new Site();
Session::start();
User::secure();

$path = '/assets/images/uploads/';
$upload_dir = GX_PATH.'/assets/images/uploads/';
$handle=opendir($upload_dir);
while($file=readdir($handle)){
    if(!is_dir($upload_dir.$file)&&!is_link($upload_dir.$file)){
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $type = $finfo->file($upload_dir.$file);
        if($type=='image/bmp'|| $type=='image/x-windows-bmp' || 
            $type=='image/jpeg' || $type=='image/pjpeg' || 
            $type=='image/png'||$type=='image/gif'||
            $type=='image/tiff'||$type=='image/x-tiff'){
            $docs[]=$file;
        }
    }
}
sort($docs);
foreach($docs as $key=>$file){
    $array[]=array(
        'thumb'=>$path.$file,
        'image'=>$upload_dir.$file,
        'title'=>$file
    );
}

echo stripslashes(json_encode($array));