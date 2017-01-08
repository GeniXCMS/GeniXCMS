
<?php
if (User::access(0)) {
    ?>
<div class="row">
    <div class="col-md-12">
        <h2>Default Themes Options
        <hr />
        </h2>

        <?php
        if (isset($_POST['install_mdo_options'])) {
            $var = array('mdo_theme_options' => '{"mdo_analytics":"","mdo_adsense":""}');
            Options::insert($var);
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
                Options::update('mdo_theme_options', $opt);
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
        <form method="post" action="">
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
            <div class="col-md-12">
                <button name="mdo_save_options" class="btn btn-success"><i class="fa fa-save"></i> Save Options</button>
            </div>
        </div>
        </form>
        <?php
        } else {
            /* do installation */
                echo '<div class="alert alert-warning"><i class="fa fa-warning"></i> Default Theme Options not installed yet, Please install it first to use the theme options. <form action="" method="post"><button class="btn btn-danger" name="install_mdo_options"><i class="fa fa-check-circle"></i> Install Options</button></form></div>';
        } ?>

    </div>
    
</div>
<?php
} else {
    Control::error('noaccess');
}
?>