<?php
if (isset($_POST['installdb'])) {
    $arr = array(
            'intro_title' => 'Welcome to '.Site::$name,
            'intro_text' => '',
            'intro_image' => 'https://www.youtube.com/watch?v=M_oeub8YhXA',
            'featured_posts' => '',
            'adsense' => '',
            'analytics' => '',
            'front_layout' => 'blog',
            'panel_1' => '',
            'panel_1_color' => '',
            'panel_1_font_color' => '',
            'panel_2' => '',
            'panel_2_color' => '',
            'panel_2_font_color' => '',
            'panel_3' => '',
            'panel_3_color' => '',
            'panel_3_font_color' => '',
            'panel_4' => '',
            'panel_5' => '',
            'panel_5_color' => '',
            'panel_5_font_color' => '',
            'background_header' => Url::theme().'images/CNV000050bw.JPG',
            'background_color_header' => '#3d3c3f',
            'background_featured' => Url::theme().'images/pattern-13.jpg',
            'background_color_featured' => '#050505',
            'background_color_footer' => '#d63333',
            'font_color_footer' => '#fff',
            'font_color_header' => '#fff',
            'container_width' => '1280',
            'category_layout' => 'blog',
            'body_background_color' => '',
            'link_color' => '',
            'link_color_hover' => '',
            'background_footer' => '',
            'link_color_footer' => '',
            'sidebar_background_color_header' => '',
            'sidebar_font_color_header' => '',
            'sidebar_border_width' => '',
            'sidebar_border_color' => '',
            'sidebar_font_color_body' => '',
            'content_border_width' => '',
            'content_border_color' => '',
            'content_background_color_body' => '',
            'fullwidth_page' => '',
            'background_footer' => '',
            'link_color_footer' => '',
            'content_font_color_body' => '',
            'content_title_size' => '',
            'content_title_cat_size' => '',
            'content_title_color' => '',
            'content_title_color_hover' => '',
            'list_title_color' => '',
            'list_title_size' => ''
        );
    $opt = array(
            'gneex_options' => json_encode($arr),
        );
    Options::insert($opt);
}

if (isset($_POST['gneex_options_update'])) {
    unset($_POST['gneex_options_update']);
    $opt = [];
    foreach ($_POST as $k => $v) {
        // $opt[$k] = urlencode($v);
        // $opt[$k] = Typo::jsonFormat($v);
        $opt[$k] = $v;
        // echo $opt[$k];
    }

    $opt = json_encode($opt, JSON_UNESCAPED_UNICODE | JSON_HEX_APOS);
    // echo $opt;
    Options::update('gneex_options', $opt);
}

?>
<form action="" method="post">
 <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-brush"></i>  GneeX Theme <small >v1.1.3</small>

            <button class="pull-right btn btn-success" type="submit" name="gneex_options_update">
                Change
            </button>
        </h1>

    </section>

    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title">
                    
                </h3>

                <div class="box-tools pull-right">

                </div>
            </div>
            <div class="box-body">
<?php


if (Gneex::checkDB()) {
    $opt = Options::get('gneex_options');
    $opt = Typo::Xclean($opt);
    // var_dump($opt);

    $opt = json_decode($opt, true);
    // print_r($opt);
    if (is_array($opt)) {
        $o = [];
        foreach ($opt as $k => $v) {
            // $o[$k] = urldecode($v);
            $o[$k] = $v;
        }
    } 

?>
    <div class="col-md-3">
        <ul class="nav nav-pills nav-stacked" id="myTabs">
            <li role="presentation" class="active"><a href="#home"><i class="fa fa-home"></i> Home</a></li>
            <li role="presentation"><a href="#intro"><i class="fa fa-asterisk"></i> Intro</a></li>
            <li role="presentation"><a href="#headerfooter"><i class="fa fa-bars"></i> Header & Footer</a></li>
            <li role="presentation"><a href="#frontpage"><i class="fa fa-desktop"></i> Frontpage</a></li>
            <li role="presentation"><a href="#layout"><i class="fa fa-th-large"></i> Global Layout</a></li>
            <li role="presentation"><a href="#style"><i class="fa fa-paint-brush"></i> Styles</a></li>
            <li role="presentation"><a href="#codes"><i class="fa fa-code"></i> Codes</a></li>
        </ul>
    </div>

    <div class="col-md-9">
        <div class="row">
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="home">
                <h3>Welcome to GneeX Themes Options</h3>
                <div class="alert alert-info">This is our official release of customized themes. <br />
                This theme was developed to give you chance to create great
                website with GeniXCMS.</div>
                <p>Have any suggestions or advice ? please don't hesitate to contact us at info@genix.id</p>
                <p>Or you can submit issue or pull request at <a href="https://github.com/GeniXCMS/GneeX" target="_blank">here <i class="fa fa-external-link"></i></a> </p>
            </div>



            
            <div role="tabpanel" class="tab-pane" id="intro">
                <div class="col-md-6">
                <h4>Intro</h4>
                <hr />
                    <div class="form-group">
                        <label>Intro Title</label>
                        <input type="text" name="intro_title" class="form-control" value="<?=$o['intro_title']; ?>">
                        <small>intro title which will appear at the frontpage</small>
                    </div>
                    <div class="form-group">
                        <label>Intro Text</label>
                        <textarea  name="intro_text" class="form-control"><?=$o['intro_text']; ?></textarea>
                        <small>intro title which will appear at the frontpage</small>
                    </div>
                    <div class="form-group">
                        <label>Intro Image/Youtube</label>
                        <input type="text" name="intro_image" class="form-control" value="<?=$o['intro_image']; ?>">
                        <small>intro image/youtube url which will appear at the frontpage</small>
                    </div>
                </div>
            </div>





            <div role="tabpanel" class="tab-pane" id="frontpage">
                <div class="col-md-12">
                <h4>Featured Posts</h4>
                <hr />
                    <div class="form-group">
                        <label>Featured Posts</label>
                        <input type="text" name="featured_posts" class="form-control" value="<?=$o['featured_posts']; ?>">
                        <small>Type the ID of the posts to be featured. comma <kbd>,</kbd> separated.</small>
                    </div>
                    <div class="form-group">
                        <label>Featured Background Image</label>
                        <input type="text" name="background_featured" class="form-control" value="<?=$o['background_featured']; ?>">
                        <small>url of the image for your featured background</small>
                    </div>
                    <div class="form-group">
                        <label>Featured Background Color</label>
                        <div class="input-group colorpicker-component" id="background_color_featured">
                            <input type="text" name="background_color_featured" class="form-control" value="<?=$o['background_color_featured']; ?>">
                            <span class="input-group-addon"><i>&nbsp;&nbsp;&nbsp;</i></span>
                        </div>
                        <small>color of the featured background</small>
                    </div>
                <hr />
                </div>
                <div class="col-md-12">
            
                <h4>Frontpage</h4>
                <hr />
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Choose Layout</label>
                                        <select name="front_layout" class="form-control" id="frontpageSelector">
                                            <option value="blog" <?=($o['front_layout'] == 'blog') ? 'selected' : ''; ?>>Blog</option>
                                            <option value="magazine" <?=($o['front_layout'] == 'magazine') ? 'selected' : ''; ?>>Magazine</option>
                                            <option value="fullwidth" <?=($o['front_layout'] == 'fullwidth') ? 'selected' : ''; ?>>Full Width</option>
                                        </select>
                                        <small>choose the layout style for the frontpage</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="fullwidth">
                            <div class="col-md-6">
                                <select name="fullwidth_page" class="form-control">
                                    <?=Gneex::optionPost('page', $o['fullwidth_page']);?>
                                </select>
                            </div>
                        </div>
                        <div id="magazine">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Panel 1 <span class="label label-danger">magazine</span></label>
                                    <?php
                                       $vars = array(
                                            'name' => 'panel_1',
                                            'type' => 'post',
                                            'selected' => $o['panel_1'],
                                        );
    echo Categories::dropdown($vars); ?>
                                    <small>choose category for panel #1</small>
                                    <div class="input-group colorpicker-component" id="panel_1_color">
                                        <input type="text" name="panel_1_color" class="form-control" value="<?=$o['panel_1_color']; ?>">
                                        <span class="input-group-addon"><i></i></span>
                                    </div>
                                    <small>panel 1 body color</small>
                                    <div class="input-group colorpicker-component" id="panel_1_font_color">
                                        <input type="text" name="panel_1_font_color" class="form-control" value="<?=$o['panel_1_font_color']; ?>">
                                        <span class="input-group-addon"><i></i></span>
                                    </div>
                                    <small>panel 1 font color</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Panel 2 <span class="label label-danger">magazine</span></label>
                                    <?php
                                       $vars = array(
                                            'name' => 'panel_2',
                                            'type' => 'post',
                                            'selected' => $o['panel_2'],
                                        );
    echo Categories::dropdown($vars); ?>
                                    <small>choose category for panel #2</small>
                                    <div class="input-group colorpicker-component" id="panel_2_color">
                                        <input type="text" name="panel_2_color" class="form-control" value="<?=$o['panel_2_color']; ?>">
                                        <span class="input-group-addon"><i></i></span>
                                    </div>
                                    <small>panel 2 body color</small>
                                    <div class="input-group colorpicker-component" id="panel_2_font_color">
                                        <input type="text" name="panel_2_font_color" class="form-control" value="<?=$o['panel_2_font_color']; ?>">
                                        <span class="input-group-addon"><i></i></span>
                                    </div>
                                    <small>panel 2 font color</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Panel 3 <span class="label label-danger">magazine</span></label>
                                    <?php
                                       $vars = array(
                                            'name' => 'panel_3',
                                            'type' => 'post',
                                            'selected' => $o['panel_3'],
                                        );
    echo Categories::dropdown($vars); ?>
                                    <small>choose category for panel #3</small>
                                    <div class="input-group colorpicker-component" id="panel_3_color">
                                        <input type="text" name="panel_3_color" class="form-control" value="<?=$o['panel_3_color']; ?>">
                                        <span class="input-group-addon"><i></i></span>
                                    </div>
                                    <small>panel 3 body color</small>
                                    <div class="input-group colorpicker-component" id="panel_3_font_color">
                                        <input type="text" name="panel_3_font_color" class="form-control" value="<?=$o['panel_3_font_color']; ?>">
                                        <span class="input-group-addon"><i></i></span>
                                    </div>
                                    <small>panel 3 font color</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Panel 4 <span class="label label-danger">magazine</span></label>
                                    <?php
                                       $vars = array(
                                            'name' => 'panel_4',
                                            'type' => 'post',
                                            'selected' => $o['panel_4'],
                                        );
    echo Categories::dropdown($vars); ?>
                                    <small>choose category for panel #4</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Panel 5 <span class="label label-danger">magazine</span></label>
                                    <?php
                                       $vars = array(
                                            'name' => 'panel_5',
                                            'type' => 'post',
                                            'selected' => $o['panel_5'],
                                        );
    echo Categories::dropdown($vars); ?>
                                    <small>choose category for panel #5</small>
                                    <div class="input-group colorpicker-component" id="panel_5_color">
                                        <input type="text" name="panel_5_color" class="form-control" value="<?=$o['panel_5_color']; ?>">
                                        <span class="input-group-addon"><i></i></span>
                                    </div>
                                    <small>panel 5 body color</small>
                                    <div class="input-group colorpicker-component" id="panel_5_font_color">
                                        <input type="text" name="panel_5_font_color" class="form-control" value="<?=$o['panel_5_font_color']; ?>">
                                        <span class="input-group-addon"><i></i></span>
                                    </div>
                                    <small>panel 5 font color</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>




            
            <div role="tabpanel" class="tab-pane" id="headerfooter">
                <div class="col-md-12">
                <h4>Header</h4>
                <hr />
                    <div class="form-group">
                        <label>Header Background Image</label>
                        <input type="text" name="background_header" class="form-control" value="<?=$o['background_header']; ?>">
                        <small>url of the image for your header background</small>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Header Background Color</label>
                                <div class="input-group colorpicker-component" id="background_color_header">
                                    <input type="text" name="background_color_header" class="form-control" value="<?=$o['background_color_header']; ?>">
                                    <span class="input-group-addon"><i>&nbsp;&nbsp;&nbsp;</i></span>
                                </div>
                                <small>color of the header background</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Header Font Color</label>
                                <div class="input-group colorpicker-component" id="font_color_header">
                                    <input type="text" name="font_color_header" class="form-control" value="<?=$o['font_color_header']; ?>">
                                    <span class="input-group-addon"><i>&nbsp;&nbsp;&nbsp;</i></span>
                                </div>
                                <small>color of the header fonts</small>
                            </div>
                        </div>
                    </div>
                <hr />
                </div>
                <div class="col-md-12">
                <h4>Footer</h4>
                <hr />
                    <div class="form-group">
                        <label>Footer Background Image</label>
                        <input type="text" name="background_footer" class="form-control" value="<?=$o['background_footer']; ?>">
                        <small>url of the image for your footer background</small>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Footer Background Color</label>
                                <div class="input-group colorpicker-component" id="background_color_footer">
                                    <input type="text" name="background_color_footer" class="form-control" value="<?=$o['background_color_footer']; ?>">
                                    <span class="input-group-addon"><i>&nbsp;&nbsp;&nbsp;</i></span>
                                </div>
                                <small>color of the footer background</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Footer Font Color</label>
                                <div class="input-group colorpicker-component" id="font_color_footer">
                                    <input type="text" name="font_color_footer" class="form-control" value="<?=$o['font_color_footer']; ?>">
                                    <span class="input-group-addon"><i>&nbsp;&nbsp;&nbsp;</i></span>
                                </div>
                                <small>color of the footer fonts</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Footer Link Color</label>
                                <div class="input-group colorpicker-component" id="link_color_footer">
                                    <input type="text" name="link_color_footer" class="form-control" value="<?=$o['link_color_footer']; ?>">
                                    <span class="input-group-addon"><i>&nbsp;&nbsp;&nbsp;</i></span>
                                </div>
                                <small>color of the footer link</small>
                            </div>
                        </div>
                    </div>
                <hr />
                </div>
            </div>


            <div role="tabpanel" class="tab-pane" id="style">
                <div class="col-md-12">
                    <h4>Body</h4>
                    <hr />
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Body Background Color</label>
                                <div class="input-group colorpicker-component" id="body_background_color">
                                    <input type="text" name="body_background_color" class="form-control" value="<?=$o['body_background_color']; ?>">
                                    <span class="input-group-addon"><i>&nbsp;&nbsp;&nbsp;</i></span>
                                </div>
                                <small>color of the body background</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Link Color</label>
                                <div class="input-group colorpicker-component" id="link_color">
                                    <input type="text" name="link_color" class="form-control" value="<?=$o['link_color']; ?>">
                                    <span class="input-group-addon"><i>&nbsp;&nbsp;&nbsp;</i></span>
                                </div>
                                <small>color of the link</small>
                            </div>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Link Color Hover</label>
                                <div class="input-group colorpicker-component" id="link_color_hover">
                                    <input type="text" name="link_color_hover" class="form-control" value="<?=$o['link_color_hover']; ?>">
                                    <span class="input-group-addon"><i>&nbsp;&nbsp;&nbsp;</i></span>
                                </div>
                                <small>color of the body background</small>
                            </div>
                        </div>


                    </div>
                </div>
                <div class="col-md-12">
                    <h4>Sidebar</h4>
                    <hr />
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Sidebar Header Background Color</label>
                                <div class="input-group colorpicker-component" id="sidebar_background_color_header">
                                    <input type="text" name="sidebar_background_color_header" class="form-control" value="<?=$o['sidebar_background_color_header']; ?>">
                                    <span class="input-group-addon"><i>&nbsp;&nbsp;&nbsp;</i></span>
                                </div>
                                <small>color of the sidebar header background</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Sidebar Header Font Color</label>
                                <div class="input-group colorpicker-component" id="sidebar_font_color_header">
                                    <input type="text" name="sidebar_font_color_header" class="form-control" value="<?=$o['sidebar_font_color_header']; ?>">
                                    <span class="input-group-addon"><i>&nbsp;&nbsp;&nbsp;</i></span>
                                </div>
                                <small>color of the sidebar header fonts</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Sidebar Body Background Color</label>
                                <div class="input-group colorpicker-component" id="sidebar_background_color_body">
                                    <input type="text" name="sidebar_background_color_body" class="form-control" value="<?=$o['sidebar_background_color_body']; ?>">
                                    <span class="input-group-addon"><i>&nbsp;&nbsp;&nbsp;</i></span>
                                </div>
                                <small>color of the sidebar body background</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Sidebar Body Font Color</label>
                                <div class="input-group colorpicker-component" id="sidebar_font_color_body">
                                    <input type="text" name="sidebar_font_color_body" class="form-control" value="<?=$o['sidebar_font_color_body']; ?>">
                                    <span class="input-group-addon"><i>&nbsp;&nbsp;&nbsp;</i></span>
                                </div>
                                <small>color of the sidebar body fonts</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Sidebar Border Width</label>
                                <input type="text" name="sidebar_border_width" class="slider form-control" data-slider-min="0" data-slider-max="100" data-slider-step="1" data-slider-value="<?=$o['sidebar_border_width'];?>" data-slider-orientation="horizontal" value="<?=$o['sidebar_border_width'];?>" data-slider-selection="before" data-slider-tooltip="show" data-slider-id="red">
                                <small>Current Width : <span id="containerWidthSliderVal"><?=$o['sidebar_border_width']; ?></span></small>

                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Sidebar Border Color</label>
                                <div class="input-group colorpicker-component" id="sidebar_border_color">
                                    <input type="text" name="sidebar_border_color" class="form-control" value="<?=$o['sidebar_border_color']; ?>">
                                    <span class="input-group-addon"><i>&nbsp;&nbsp;&nbsp;</i></span>
                                </div>
                                <small>color of the sidebar border color</small>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="col-md-12">
                    <h4>Content</h4>
                    <hr />
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Content Border Width</label>
                                <input type="text" name="content_border_width" class="slider form-control" data-slider-min="0" data-slider-max="100" data-slider-step="1" data-slider-value="<?=$o['content_border_width'];?>" data-slider-orientation="horizontal" value="<?=$o['content_border_width'];?>" data-slider-selection="before" data-slider-tooltip="show" data-slider-id="red">
                                <small>Current Width : <span id="containerWidthSliderVal"><?=$o['content_border_width']; ?></span></small>

                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Content Border Color</label>
                                <div class="input-group colorpicker-component" id="content_border_color">
                                    <input type="text" name="content_border_color" class="form-control" value="<?=$o['content_border_color']; ?>">
                                    <span class="input-group-addon"><i>&nbsp;&nbsp;&nbsp;</i></span>
                                </div>
                                <small>color of the sidebar border color</small>
                            </div>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Content Body Background Color</label>
                                <div class="input-group colorpicker-component" id="content_background_color_body">
                                    <input type="text" name="content_background_color_body" class="form-control" value="<?=$o['content_background_color_body']; ?>">
                                    <span class="input-group-addon"><i>&nbsp;&nbsp;&nbsp;</i></span>
                                </div>
                                <small>color of the content body background</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Content Body Font Color</label>
                                <div class="input-group colorpicker-component" id="content_font_color_body">
                                    <input type="text" name="content_font_color_body" class="form-control" value="<?=$o['content_font_color_body']; ?>">
                                    <span class="input-group-addon"><i>&nbsp;&nbsp;&nbsp;</i></span>
                                </div>
                                <small>color of the content body fonts</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Content Title Single Size </label>
                                <input type="text" name="content_title_size" class="slider form-control" data-slider-min="1" data-slider-max="100" data-slider-step="1" data-slider-value="<?=$o['content_title_size'];?>" data-slider-orientation="horizontal" value="<?=$o['content_title_size'];?>" data-slider-selection="before" data-slider-tooltip="show" data-slider-id="red">
                                <small>Current Size : <span id="containerWidthSliderVal"><?=$o['content_title_size']; ?></span></small>

                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Content Title Cat/Tag/Author Size </label>
                                <input type="text" name="content_title_cat_size" class="slider form-control" data-slider-min="1" data-slider-max="100" data-slider-step="1" data-slider-value="<?=$o['content_title_cat_size'];?>" data-slider-orientation="horizontal" value="<?=$o['content_title_cat_size'];?>" data-slider-selection="before" data-slider-tooltip="show" data-slider-id="red">
                                <small>Current Size : <span id="containerWidthSliderVal"><?=$o['content_title_cat_size']; ?></span></small>

                            </div>
                        </div>

                    </div>

                    <div class="row">
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Content Title Color</label>
                                <div class="input-group colorpicker-component" id="content_title_color">
                                    <input type="text" name="content_title_color" class="form-control" value="<?=$o['content_title_color']; ?>">
                                    <span class="input-group-addon"><i>&nbsp;&nbsp;&nbsp;</i></span>
                                </div>
                                <small>color of the sidebar border color</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Content Title Color Hover</label>
                                <div class="input-group colorpicker-component" id="content_title_color_hover">
                                    <input type="text" name="content_title_color_hover" class="form-control" value="<?=$o['content_title_color_hover']; ?>">
                                    <span class="input-group-addon"><i>&nbsp;&nbsp;&nbsp;</i></span>
                                </div>
                                <small>color of the content title hover color</small>
                            </div>
                        </div>

                    </div>

                    <div class="row">
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>List Title Color</label>
                                <div class="input-group colorpicker-component" id="list_title_color">
                                    <input type="text" name="list_title_color" class="form-control" value="<?=$o['list_title_color']; ?>">
                                    <span class="input-group-addon"><i>&nbsp;&nbsp;&nbsp;</i></span>
                                </div>
                                <small>color of the sidebar border color</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>List Title Size</label>
                                <input type="text" name="list_title_size" class="slider form-control" data-slider-min="1" data-slider-max="100" data-slider-step="1" data-slider-value="<?=$o['list_title_size'];?>" data-slider-orientation="horizontal" value="<?=$o['list_title_size'];?>" data-slider-selection="before" data-slider-tooltip="show" data-slider-id="red">
                                <small>Current Size : <span id="containerWidthSliderVal"><?=$o['list_title_size']; ?></span></small>

                            </div>
                        </div>

                    </div>

                </div>
            </div>

            <div role="tabpanel" class="tab-pane" id="layout">
                <div class="col-md-6">
                <h4>Global Layout</h4>
                <hr />
                    <div class="form-group">
                        <label>Container Width</label>
                        <input type="text" name="container_width" class="slider form-control" data-slider-min="0" data-slider-max="1300" data-slider-step="1" data-slider-value="<?=$o['container_width'];?>" data-slider-orientation="horizontal" value="<?=$o['container_width'];?>" data-slider-selection="before" data-slider-tooltip="show" data-slider-id="red">
                         <small>Current Width : <span id="containerWidthSliderVal"><?=$o['container_width']; ?></span></small>

                    </div>
                <hr />
                    <div class="form-group">
                        <label>Category/Archive Layout</label>
                        <select name="category_layout" class="form-control">
                            <option value="blog" <?=($o['category_layout'] == 'blog') ? 'selected' : ''; ?>>Blog</option>
                            <option value="magazine" <?=($o['category_layout'] == 'magazine') ? 'selected' : ''; ?>>Magazine</option>
                        </select>
                        <small>category/archive page layout</small>
                    </div>
                <hr />
                </div>
                <div class="col-md-6">


                </div>
            </div>





            <div role="tabpanel" class="tab-pane" id="codes">
                <div class="col-md-6">
                <h4>Adsense Code</h4>
                <hr />
                    <div class="form-group">
                        <label>Adsense Code</label>
                        <textarea name="adsense" class="form-control"><?=$o['adsense']; ?></textarea>
                        <small>Your lovely adsense code.</small>
                    </div>
                <hr />
                </div>
                <div class="col-md-6">
                <h4>Analytics Code</h4>
                <hr />
                    <div class="form-group">
                        <label>Analytics Code</label>
                        <textarea name="analytics" class="form-control"><?=$o['analytics']; ?></textarea>
                        <small>Your analytics tracking code.</small>
                    </div>

                </div>
            </div>


            
        </div>
            
            
        </div>
    </div>
<?php
} else {
    echo '
        <div class="col-md-12">
            <div class="alert alert-danger">
            The <strong>GneeX Options Database</strong> is not installed. Please install it first.
            </div>
            <form method="post">
            <button type="submit" class="btn btn-success" name="installdb">
                Install GneeX Database
            </button>
            </form>
        </div>

        ';
}
?>
            </div>
            <!-- /.box-body -->
<!--            <div class="box-footer">-->
<!---->
<!--            </div>-->
            <!-- /.box-footer-->
        </div>
        <!-- /.box -->

    </section>
</form>
