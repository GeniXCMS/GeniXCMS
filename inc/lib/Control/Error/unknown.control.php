<div class="col-sm-12">
<h2><i class="fa fa-warning"></i> Unknown Error Occured</h2>
<?php if(isset($val) && $val != '') {?>
<div class="alert alert-danger">
<?=$val;?>
</div>

<?php
    }
    Site::footer();
?>

</div>