
<?php
if (User::access(0)) {
    ?>
<form method="post" action="">
<section class="app-content-header">
    <!--begin::Container-->
    <div class="container-fluid">
        <!--begin::Row-->
        <div class="row">
            <div class="col-sm-6">
                <h3 class="mb-0"><?=_("Default Themes Options");?></h3>
            </div>
            <div class="col-sm-6">
                <div class="breadcrumb float-sm-end">
                <button type="submit" name="mdo_save_options" class="btn btn-success">
                <span class="bi bi-check2"></span> 
                <span class=""><?=_("Save Options");?></span>
                </button>
                </div>
            </div>
        </div>
        <!--end::Row-->
    </div>
    <!--end::Container-->
</section>

<section class="app-content">
<div class="container-fluid">

    
        

        <?php
        $data[] = "";
        if (isset($_POST['install_mdo_options'])) {
            $var = array('mdo_theme_options' => '{"mdo_analytics":"","mdo_adsense":""}');
            Options::insert($var);
            $data['alertSuccess'][] = "Options Installed";
        }
        $mdo_theme_options = Options::isExist('mdo_theme_options');
        if ($mdo_theme_options) {
            if (isset($_POST['mdo_save_options'])) {
                unset($_POST['mdo_save_options']);
                $opt = array();
                foreach ($_POST as $k => $v) {
                    $opt[$k] = urlencode($v);
                }
                $opt = json_encode($opt);
                if ( Options::update('mdo_theme_options', $opt) ) {
                    $data['alertSuccess'][] = "Options Saved";
                } else {
                    $data['alertDanger'][] = "Error: Options Not Saved";
                }
                
            } 

            $opt = Options::get('mdo_theme_options');
            // $opt = utf8_encode($opt);
            $opt = json_decode($opt, true);
            if (is_array($opt)) {
                $o = array();
                foreach ($opt as $k => $v) {
                    $o[$k] = urldecode($v);
                }
            } else {
                $o['mdo_analytics'] = '';
                $o['mdo_adsense'] = '';
            }
        ?>
        <div class="card card-outline card-warning">
            <div class="card-header with-border">
                <div class="card-title">
                <?=_("Settings");?>
                </div>
            </div>
            <div class="card-body">
            
                    <div class="row">
                        <div class="col-md-6">
                            <label>Analytics Code</label>
                            <div class="form-group">
                                <textarea class="form-control" name="mdo_analytics"><?=$o['mdo_analytics']; ?></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label>Adsense Code</label>
                            <div class="form-group">
                                <textarea class="form-control" name="mdo_adsense"><?=$o['mdo_adsense']; ?></textarea>
                            </div>
                        </div>
                        
                    </div>
                
            </form>
            </div>

        </div>
        
        <?php
        } else {
            /* do installation */
                echo '<div class="alert alert-warning"><i class="fa fa-warning"></i> Default Theme Options not installed yet, Please install it first to use the theme options. <form action="" method="post"><button class="btn btn-danger" name="install_mdo_options"><i class="fa fa-check-circle"></i> Install Options</button></form></div>';
        } ?>

    

</div>
    </section>
    </form>
<?php
System::alert($data);
} else {
    Control::error('noaccess');
}
?>