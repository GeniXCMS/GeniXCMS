<?php
    if( isset($_POST['upload']) ){
        //echo "string";
        //print_r($_FILES);
        if(isset($_FILES['images']) && $_FILES['images'] != ''){
            $path = "/assets/images/uploads/";
            $allowed = array('png', 'jpg', 'gif');
            $upload = Upload::go('images', $path, $allowed );
            if(isset($upload['error']) != ''){
                echo $upload['error'];
            }else{
                $im = $upload['filepath'];
                $dst = GX_PATH."/assets/images/uploads/thumbs/".$upload['filename'];
                Image::resize($im, $dst, "300", '300', 1);
                list($w, $h) = getimagesize($im);
                Image::resize($im, $im, $w, $h, 0);
            }
        }
        echo "<pre>";
        print_r($upload);
        echo "</pre>";
    }

    

?>
<div class="row">
    <div class="col-md-12">
        <h1>GeniXCMS TestImage Sample</h1>
        <hr />
    </div>
    <div class="col-md-12">
        <form action="" method="post" enctype="multipart/form-data">
            <input type="file" name="images" class="form-control">
            <button type="submit" class="btn btn-primary" name="upload"><i class="fa fa-upload"></i> Upload</button>
        </form>
    </div>

</div>