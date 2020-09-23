<?php
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.1 build date 20150202
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
?>
<form action="" method="POST" enctype="multipart/form-data">

        <div class="col-md-12">
            <?=Hooks::run('admin_page_notif_action', $data);?>
        </div>
        <section class="content-header">
            <h1 class="clearfix">
                <div class="pull-left">
                    <i class="fa fa-wrench"></i> <?=SETTINGS;?>
                </div>
                <div class="pull-right">
                    <button type="submit" name="change" class="btn btn-success btn-sm" value="Change">
                        <span class="glyphicon glyphicon-ok"></span>
                        <span class="hidden-xs hidden-sm"><?=CHANGE;?></span>
                    </button>
                    <button type="reset" class="btn btn-danger btn-sm" value="Cancel">
                        <span class="glyphicon glyphicon-remove"></span>
                        <span class="hidden-xs hidden-sm"><?=CANCEL;?></span>
                    </button>
                </div>
            </h1>
        </section>
        <section class="content" id="myTab">

                    <div class="nav-tabs-custom">
            <ul class="nav nav-tabs" role="tablist">
                <li class="active"><a href="#general" role="tab" data-toggle="tab"><?=GENERAL;?></a></li>
                <li><a href="#localization" role="tab" data-toggle="tab"><?=LOCALIZATION;?></a></li>
                <li><a href="#email" role="tab" data-toggle="tab"><?=EMAIL;?></a></li>
                <li><a href="#social" role="tab" data-toggle="tab"><?=SOCIAL;?></a></li>
                <li><a href="#logo" role="tab" data-toggle="tab"><?=LOGO;?></a></li>
                <li><a href="#library" role="tab" data-toggle="tab"><?=LIBRARY;?></a></li>
                <li><a href="#posts" role="tab" data-toggle="tab"><?=POSTS;?></a></li>
                <li><a href="#payment" role="tab" data-toggle="tab"><?=PAYMENT;?></a></li>
                <li><a href="#security" role="tab" data-toggle="tab"><?=SECURITY;?></a></li>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
                <!-- Tab Pane General -->
                <div class="tab-pane active clearfix" id="general">
                    <h3>
                        <?=WEBSITE_DETAIL;?>
                        <hr />
                    </h3>
                    <div class="col-sm-12">
                        <div class="row">
                            <div class="col-sm-6 form-group">
                                <label><?=WEBSITE_NAME;?></label>
                                <input type="text" name="sitename" value="<?=Options::v('sitename');?>" class="form-control">
                                <small class="help-block"><?=WEBSITE_NAME_DESC;?></small>
                            </div>
                            <div class="col-sm-6 form-group">
                                <label><?=WEBSITE_SLOGAN;?></label>
                                <input type="text" name="siteslogan" value="<?=Options::v('siteslogan');?>" class="form-control">
                                <small class="help-block"><?=WEBSITE_SLOGAN_DESC;?></small>
                            </div>
                        </div>

                    </div>

                    <div class="col-sm-12">
                        <div class="row">
                            <div class="col-sm-6 form-group">
                                <label><?=WEBSITE_DOMAIN;?></label>
                                <input type="text" name="sitedomain" value="<?=Options::v('sitedomain');?>" class="form-control">
                                <small class="help-block"><?=WEBSITE_DOMAIN_DESC;?></small>
                            </div>
                            <div class="col-sm-6 form-group">
                                <label><?=WEBSITE_URI;?></label>
                                <input type="text" name="siteurl" value="<?=Options::v('siteurl');?>" class="form-control">
                                <small class="help-block"><?=WEBSITE_URI_DESC;?></small>
                            </div>
                        </div>

                    </div>

                    <div class="col-sm-12">
                        <div class="row">
                            <div class="col-sm-6 form-group">
                                <label><?=WEBSITE_KEYWORDS;?></label>
                                <input type="text" name="sitekeywords" value="<?=Options::v('sitekeywords');?>" class="form-control">
                                <small class="help-block"><?=WEBSITE_KEYWORDS_DESC;?></small>
                            </div>
                            <div class="col-sm-6 form-group">
                                <label><?=WEBSITE_DESCRIPTION;?></label>
                                <textarea  name="sitedesc" class="form-control"><?=Options::v('sitedesc');?></textarea>
                                <small class="help-block"><?=WEBSITE_DESCRIPTION_DESC;?></small>
                            </div>
                        </div>

                    </div>
                    <div class="col-sm-12">
                        <div class="row">
                            <div class="col-sm-6 form-group">
                                <label><?=WEBSITE_EMAIL;?></label>
                                <input type="text" name="siteemail" value="<?=Options::v('siteemail');?>" class="form-control">
                                <small class="help-block"><?=WEBSITE_EMAIL_DESCR;?></small>
                            </div>

                        </div>

                    </div>


                </div><!-- Tab Pane General End -->

                <!-- Tab Pane Localization -->
                <div class="tab-pane clearfix" id="localization">
                    <h3>
                        <?=LOCALIZATION;?>
                        <hr />
                    </h3>

                    <div class="col-sm-12">
                        <div class="row">
                            <div class="col-sm-12 col-md-6 form-group">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <label><?=COUNTRY;?></label>
                                        <select name="country_id" class="form-control">
                                            <?=Date::optCountry(Options::v('country_id'));?>
                                        </select>
                                        <small class="help-block"><?=COUNTRY_DESC;?></small>
                                    </div>
                                    <div class="col-sm-6">
                                        <label><?=TIMEZONE;?></label>
                                        <select name="timezone" class="form-control">
                                            <?=Date::optTimeZone(Options::v('timezone'));?>
                                        </select>
                                        <small class="help-block"><?=TIMEZONE_DESC;?></small>
                                    </div>

                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6 form-group">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <label><?=WEBSITE_LANG;?></label>
                                        <select name="system_lang" class="form-control">
                                            <?=Language::optDropdown(Options::v('system_lang'));?>
                                        </select>
                                        <small class="help-block"><?=WEBSITE_LANG_DESC;?></small>
                                    </div>
                                    <div class="col-sm-6">
                                        <label><?=CHARSET;?></label>
                                        <input name="charset" class="form-control" value="<?=Options::v('charset');?>">
                                        <small class="help-block"><?=CHARSET_DESC;?></small>
                                    </div>

                                </div>

                            </div>
                        </div>

                    </div>


                </div><!-- Tab Pane Localization END -->

                <!-- Tab Pane Email -->
                <div class="tab-pane clearfix" id="email">
                    <h3><?=SETTINGS_EMAIL_SETTINGS;?><hr /></h3>
                    <div class="col-sm-12">
                        <div class="row">
                            <div class="col-sm-6 form-group">
                                <label><?=SETTINGS_EMAIL_MAIL;?></label>
                                <?php
                                if (Options::v('mailtype') == 0) {
                                    $o = 'selected';
                                    $s = '';
                                } elseif (Options::v('mailtype') == 1) {
                                    $s = 'selected';
                                    $o = '';
                                }
                                ?>
                                <select name="mailtype" class="form-control">
                                    <option value="0" <?=$o;?>>Mail</option>
                                    <option value="1" <?=$s;?>>SMTP</option>
                                </select>
                                <small class="help-block"><?=SETTINGS_EMAIL_MAIL_DESCR;?></small>
                            </div>
                            <div class="col-sm-6 form-group">
                                <label><?=SETTINGS_EMAIL_SMTP;?></label>
                                <input type="text" name="smtpport" value="<?=Options::v('smtpport');?>" class="form-control">

                                <small class="help-block"><?=SETTINGS_EMAIL_SMTP_DESCR;?></small>
                            </div>
                        </div>

                    </div>

                    <div class="col-sm-12">
                        <div class="row">
                            <div class="col-sm-6 form-group">
                                <label><?=SETTINGS_EMAIL_MAILSRV;?></label>
                                <input type="text" name="smtphost" value="<?=Options::v('smtphost');?>" class="form-control">
                                <small class="help-block"><?=SETTINGS_EMAIL_MAILSRV_DESCR;?></small>
                            </div>
                            <div class="col-sm-6 form-group">
                                <label><?=SETTINGS_EMAIL_SMTP_USR;?></label>
                                <input type="text" name="smtpuser" value="<?=Options::v('smtpuser');?>" class="form-control">
                                <small class="help-block"><?=SETTINGS_EMAIL_SMTP_USR_DESCR;?></small>
                            </div>
                        </div>

                    </div>

                    <div class="col-sm-12">
                        <div class="row">
                            <div class="col-sm-6 form-group">
                                <label><?=SETTINGS_EMAIL_SMTP_PWD;?></label>
                                <input type="password" name="smtppass" value="<?=Options::v('smtppass');?>" class="form-control">
                                <small class="help-block"><?=SETTINGS_EMAIL_SMTP_PWD_DESCR;?></small>
                            </div>
                            <div class="col-sm-6 form-group">

                            </div>
                        </div>

                    </div>
                </div><!-- Tab Pane Email END -->

                <!-- Tab Pane Social -->
                <div class="tab-pane clearfix" id="social">
                    <h3><?=SETTINGS_SOCIAL;?><hr /></h3>
                    <div class="col-sm-12">
                        <div class="row">
                            <div class="col-sm-6 form-group">
                                <label><?=SETTINGS_SOCIAL_FBACC;?></label>
                                <input type="text" name="fbacc" value="<?=Options::v('fbacc');?>" class="form-control">
                                <small class="help-block"><?=SETTINGS_SOCIAL_FBACC_DESCR;?></small>
                            </div>
                            <div class="col-sm-6 form-group">
                                <label><?=SETTINGS_SOCIAL_FBPAGE;?></label>
                                <input type="text" name="fbpage" value="<?=Options::v('fbpage');?>" class="form-control">
                                <small class="help-block"><?=SETTINGS_SOCIAL_FBPAGE_DESCR;?></small>
                            </div>
                        </div>

                    </div>

                    <div class="col-sm-12">
                        <div class="row">
                            <div class="col-sm-6 form-group">
                                <label><?=SETTINGS_SOCIAL_TWITTER;?></label>
                                <div class="input-group">
                                    <span class="input-group-addon">@</span>
                                    <input type="text" name="twitter" value="<?=Options::v('twitter');?>" class="form-control">
                                </div>
                                <small class="help-block"><?=SETTINGS_SOCIAL_TWITTER_DESCR;?></small>
                            </div>
                            <div class="col-sm-6 form-group">
                                <label><?=SETTINGS_SOCIAL_LINKEDIN;?></label>
                                <input type="text" name="linkedin" value="<?=Options::v('linkedin');?>" class="form-control">
                                <small class="help-block"><?=SETTINGS_SOCIAL_LINKEDIN_DESCR;?></small>
                            </div>
                        </div>

                    </div>

                </div><!-- Tab Pane Social END -->

                <!-- Tab Pane Logo -->
                <div class="tab-pane clearfix" id="logo">
                    <h3><?=SETTINGS_LOGO;?><hr /></h3>
                    <div class="col-sm-12">
                        <div class="row">
                            <div class="col-sm-6 form-group">
                                <label><?=SETTINGS_LOGO_CURRENT;?></label>
                                <?php
                                $is_logourl = Options::v('is_logourl');
                                $logourl = Options::v('logourl');
                                $logo = Options::v('logo');
                                if ($is_logourl == 'on' && $logourl != '') {
                                    $logoimg = '<img src="'.Options::v('logourl').'" class="clearfix">';
                                } elseif ($is_logourl == 'off' && $logo != '') {
                                    $logoimg = '<img src="'.Site::$url.Options::v('logo').'" class="clearfix">';
                                } else {
                                    $logoimg = '';
                                }
                                ?>
                                <div class="col-sm-12 clearfix">
                                    <?=$logoimg;?>

                                </div>
                                <small class="help-block"><?=SETTINGS_LOGO_CURRENT_DESCR;?></small>

                            </div>
                        </div>
                    </div>

                    <div class="col-sm-12">
                        <div class="row">
                            <div class="col-sm-6 form-group">
                                <label><?=SETTINGS_LOGO_BROWSE;?></label>
                                <div class="">
                                    <input type="file" name="logo" class="form-control" accept=".job,.png,.gif">
                                    <small class="help-block"><?=SETTINGS_LOGO_BROWSE_DESCR;?></small>
                                </div>
                            </div>
                            <div class="col-sm-6 form-group">

                                <label><?=SETTINGS_LOGO_URL;?></label>
                                <div class="input-group">

                                    <span class="input-group-addon">
                                        <?php if (Options::v('is_logourl') == 'on') {
                                            $is_logourl = 'checked';
} else {
    $is_logourl = 'off';
}
                                        ?>
                                        <input type="checkbox" name="is_logourl" rel="tooltip"
                                        title="Check here if you want to use URL" <?=$is_logourl;?>>
                                    </span>
                                    <input type="text" name="logourl" value="<?=Options::v('logourl');?>" class="form-control">
                                </div>
                                <small class="help-block"><?=SETTINGS_LOGO_URL_DESCR;?></small>
                            </div>

                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="row">
                            <div class="col-sm-6 form-group">
                                <label><?=SETTINGS_LOGO_FAVICON;?></label>
                                <input type="text" name="siteicon" value="<?=Options::v('siteicon');?>" class="form-control">
                                <small class="help-block"><?=SETTINGS_LOGO_FAVICON_DESCR;?></small>
                            </div>
                        </div>
                    </div>

                </div><!-- Tab Pane Logo END -->

                <!-- Tab Pane Library -->
                <div class="tab-pane clearfix" id="library">
                    <h3><?=SETTINGS_LIBRARY;?><hr /></h3>
                    <div class="col-sm-12">
                        <div class="row">
                            <div class="col-sm-6 form-group">
                                <label><?=SETTINGS_LIBRARY_CDNURL;?></label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-link"></i>
                                    </span>
                                    <input type="text" name="cdn_url" rel="tooltip"
                                    class="form-control" placeholder="CDN Url"
                                    value="<?=Options::v('cdn_url');?>">
                                </div>
                                <small class="help-block"><?=SETTINGS_LIBRARY_CDNURL_DESCR;?></small>
                            </div>

                            <div class="col-sm-6 form-group">
                                <label><?=SETTINGS_LIBRARY_JQUERY;?></label>
                                <?php if (Options::v('use_jquery') == 'on') {
                                    $use_jquery = 'checked';
} else {
    $use_jquery = 'off';
}
                                ?>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <input type="checkbox" name="use_jquery" rel="tooltip"
                                        title="Check here if you want to use URL" <?=$use_jquery;?>>
                                    </span>
                                    <input type="text" name="jquery_v" rel="tooltip"
                                    class="form-control" placeholder="Jquery Version"
                                    value="<?=Options::v('jquery_v');?>">
                                    <span class="input-group-btn">
                                        <button type="" name="" rel="tooltip"
                                        title="" class="btn btn-default">
                                            <?=SETTINGS_LIBRARY_CDN;?>
                                        </button>
                                    </span>
                                </div>
                                <small class="help-block"><?=SETTINGS_LIBRARY_JQUERY_DESCR;?></small>
                            </div>
                            
                            <div class="col-sm-6 form-group">
                                <label><?=SETTINGS_LIBRARY_BOOTSTRAP;?></label>
                                <?php
                                if (Options::v('use_bootstrap') == 'on') {
                                    $use_bootstrap = 'checked';
                                } else {
                                    $use_bootstrap = 'off';
                                }
                                ?>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <input type="checkbox" name="use_bootstrap" rel="tooltip"
                                        title="Check here if you want to use URL" <?=$use_bootstrap;?>>
                                    </span>
                                    <input type="text" name="bs_v" rel="tooltip"
                                    class="form-control disable" placeholder="Bootstrap Version"
                                    readonly value="<?=Options::v('bs_v');?>">
                                    <span class="input-group-btn">
                                        <button type="" name="" rel="tooltip"
                                        title="" class="btn btn-default">
                                            <?=SETTINGS_LIBRARY_LOCAL;?>
                                        </button>
                                    </span>
                                </div>
                                <small class="help-block"><?=SETTINGS_LIBRARY_BOOTSTRAP_DESCR;?></small>
                            </div>
                            <div class="col-sm-6 form-group">
                                <label><?=SETTINGS_LIBRARY_FAWESOME;?></label>
                                <?php
                                if (Options::v('use_fontawesome') == 'on') {
                                    $use_fontawesome = 'checked';
                                } else {
                                    $use_fontawesome = 'off';
                                }
                                ?>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <input type="checkbox" name="use_fontawesome" rel="tooltip"
                                        title="Check here if you want to use URL" <?=$use_fontawesome;?>>
                                    </span>
                                    <input type="text" name="fontawesome_v" rel="tooltip"
                                    class="form-control disable" placeholder="Fontawesome Version"
                                    readonly value="<?=Options::v('fontawesome_v');?>">
                                    <span class="input-group-btn">
                                        <button type="" name="" rel="tooltip"
                                        title="" class="btn btn-default">
                                            <?=SETTINGS_LIBRARY_CDN;?>
                                        </button>
                                    </span>
                                </div>
                                <small class="help-block"><?=SETTINGS_LIBRARY_FAWESOME_DESCR;?></small>
                            </div>
                            <div class="col-sm-6 form-group">
                                <label><?=SETTINGS_LIBRARY_EDITOR;?></label>
                                <?php
                                if (Options::v('use_editor') == 'on') {
                                    $use_editor = 'checked';
                                } else {
                                    $use_editor = 'off';
                                }
                                ?>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <input type="checkbox" name="use_editor" rel="tooltip"
                                        title="Check here if you want to use Editor" <?=$use_editor;?>>
                                    </span>
                                    <input type="text" name="editor_v" rel="tooltip"
                                    class="form-control disable" placeholder="Editor Version"
                                    readonly value="<?=Options::v('editor_v');?>">
                                    <span class="input-group-btn">
                                        <select name="editor_type" rel="tooltip"
                                        title=""
                                        class="btn btn-default">
                                            <option value="summernote"><?=SETTINGS_LIBRARY_SUMMERNOTE;?></option>
                                        </select>
                                    </span>
                                </div>
                                <small class="help-block"><?=SETTINGS_LIBRARY_EDITOR_DESCR;?></small>
                            </div>
                            <div class="col-sm-6 form-group">
                                <label><?=SETTINGS_LIBRARY_BVALIDATOR;?></label>
                                <?php
                                if (Options::v('use_bsvalidator') == 'on') {
                                    $use_bsvalidator = 'checked';
                                } else {
                                    $use_bsvalidator = 'off';
                                }
                                ?>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <input type="checkbox" name="use_bsvalidator" rel="tooltip"
                                        title="Check here if you want to use URL" <?=$use_bsvalidator;?>>
                                    </span>
                                    <input type="text" name="bsvalidator_v" rel="tooltip"
                                    class="form-control disable" placeholder="Bootstrap Validator Version"
                                    readonly value="<?=Options::v('bsvalidator_v');?>">
                                    <span class="input-group-btn">
                                        <button type="" name="" rel="tooltip"
                                        title="" class="btn btn-default">
                                            <?=SETTINGS_LIBRARY_LOCAL;?>
                                        </button>
                                    </span>
                                </div>
                                <small class="help-block"><?=SETTINGS_LIBRARY_BVALIDATOR_DESCR;?></small>
                            </div>
                        </div>
                    </div>

                </div><!-- Tab Pane Library END -->


                <div class="tab-pane clearfix" id="posts">
                    <h3><?=SETTINGS_POSTS;?><hr /></h3>
                    <div class="col-sm-12">
                        <div class="row">
                            <div class="col-sm-6 form-group">
                                <label><?=SETTINGS_POSTS_PERPAGE;?></label>
                                <input type="number" name="post_perpage" value="<?=Options::v('post_perpage');?>" class="form-control" min='1'>
                                <small class="help-block"><?=SETTINGS_POSTS_PERPAGE_DESCR;?></small>

                            </div>
                            <div class="col-sm-6 form-group">
                                <label><?=SETTINGS_POSTS_PAGINATION;?></label>
                                <?php
                                if (Options::v('pagination') == 'number') {
                                    $number = 'selected';
                                    $pager = '';
                                } elseif (Options::v('pagination') == 'pager') {
                                    $pager = 'selected';
                                    $number = '';
                                } else {
                                    $pager = '';
                                    $number = '';
                                }
                                ?>
                                <select  name="pagination" class="form-control">
                                    <option value="number" <?=$number;
?>><?=SETTINGS_POSTS_PAGINATION_NUMBER;?></option>
                                    <option value="pager" <?=$pager;
?>><?=SETTINGS_POSTS_PAGINATION_PAGER;?></option>
                                </select>
                                <small class="help-block"><?=SETTINGS_POSTS_PAGINATION_DESCR;?> Number : <code>[1][2][3]</code>, Pager : <code>[Prev] [Next]</code> </small>
                            </div>
                            <div class="col-sm-6 form-group">
                                <label><?=SETTINGS_POSTS_PINGER;?></label>
                                <div class="input-group">
                                    <?php
                                    $pinger_enable = Options::v('pinger_enable');
                                    if ($pinger_enable == 'on') {
                                        $pinger_enable_val = 'checked';
                                    } else {
                                        $pinger_enable_val = 'off';
                                    }
                                    ?>
                                    <div class="input-group-addon">
                                        <input type="checkbox" name="pinger_enable"  <?=$pinger_enable_val;?>> 
                                    </div>
                                    <span class="form-control">Enable pinger ?</span>
                                </div>
                                <div class="clearfix">&nbsp;</div>
                                <div class="input-group">
                                    <span class="input-group-addon"><?=SETTINGS_POSTS_PINGER_HTTP?></span>
                                    <textarea name="pinger" class="form-control" style="height: 200px;" ><?=Options::v('pinger');?></textarea>
                                </div>

                                <small class="help-block"><?=SETTINGS_POSTS_PINGER_DESCR?></small>

                            </div>
                        </div>
                    </div>

                </div><!-- Posts Tab END -->

                <?php
                $curr = Options::v('currency');
                if ($curr == 'USD') {
                    $usd = 'SELECTED';
                    $euro = '';
                    $pound = '';
                } elseif ($curr == 'EUR') {
                    $euro = 'SELECTED';
                    $usd = '';
                    $pound = '';
                } elseif ($curr == 'GBP') {
                    $pound = 'SELECTED';
                    $usd = '';
                    $euro = '';
                } else {
                    $pound = '';
                    $usd = '';
                    $euro = '';
                }
                ?>

                <!-- Payment Tab Start -->
                <div class="tab-pane clearfix" id="payment">
                    <h3><?=SETTINGS_PAYMENT?><hr /></h3>
                    <div class="col-sm-12">
                        <h4><?=SETTINGS_PAYMENT_PAYPAL_CONF?></h4>
                        <div class="row">
                            <div class="form-group  col-md-12 clearfix">
                                <label for="currency"><?=SETTINGS_PAYMENT_PAYPAL_CSYMB?></label>
                                <select class="form-control col-md-6" id="currency" name="currency" required>
                                    <option value="USD" <?=$usd;?>>$ (USD)</option>
                                    <option value="EUR" <?=$euro;?>>&euro; (EUR)</option>
                                    <option value="GBP" <?=$pound;?>>&pound; (GBP)</option>
                                </select>
                                <small class="help-block"><?=SETTINGS_PAYMENT_PAYPAL_CSYMB_DESCR?></small>

                            </div>
                            <div class="form-group  col-md-6 clearfix">
                                <label for="ppsandbox"><?=SETTINGS_PAYMENT_SANDBOX?></label>
                                <div class="form-group">
                                    <?php
                                    $ppsandbox = Options::v('ppsandbox');
                                    if ($ppsandbox == 'on') {
                                        $sandbox = 'checked';
                                        $sandval = 0;
                                    } else {
                                        $sandbox = '';
                                        $sandval = '1';
                                    }
                                    ?>

                                    <input type="checkbox" class="" id="ppsandbox"
                                    name="ppsandbox"  <?=$sandbox;?>> <?=SETTINGS_PAYMENT_SANDBOX_EN?>
                                </div>
                                <small class="help-block"><?=SETTINGS_PAYMENT_SANDBOX_EN_DESCR?></small>
                            </div>
                            <div class="form-group  col-md-6 clearfix">
                                <label for="ppemail"><?=SETTINGS_PAYMENT_PAYPALAPI_USR?></label>
                                <input type="text" class="form-control" id="ppemail" placeholder="API Username"
                                name="ppuser" value="<?=Options::v('ppuser');?>">
                                <small class="help-block"><?=SETTINGS_PAYMENT_PAYPALAPI_USR_DESCR?></small>
                            </div>
                            <div class="form-group  col-md-6">
                                <label for="ppsecurity"><?=SETTINGS_PAYMENT_PAYPALAPI_PWD?></label>
                                <input type="text" class="form-control" id="ppsecurity" placeholder="PayPal API Password"
                                name="pppass" value="<?=Options::v('pppass');?>">
                                <small class="help-block"><?=SETTINGS_PAYMENT_PAYPALAPI_PWD_DESCR?></small>
                            </div>
                            <div class="form-group  col-md-6 clearfix">
                                <label for="ppsecret"><?=SETTINGS_PAYMENT_PAYPALAPI_SIGN?></label>
                                <input type="text" class="form-control" id="ppsecret" placeholder="PayPal Signature"
                                name="ppsign" value="<?=Options::v('ppsign');?>">
                                <small class="help-block"><?=SETTINGS_PAYMENT_PAYPALAPI_SIGN_DESCR?></small>
                            </div>
                        </div>
                        <div class="form-group col-md-12 clearfix">
                            <span class="alert alert-warning center-block">
                                <?=SETTINGS_PAYMENT_ALERT?>
                            </span>
                        </div>
                    </div>

                </div><!-- Payment Tab Stop -->

                <!-- Security Tab Start -->
                <div class="tab-pane clearfix" id="security">
                    <h3>Security
                    <hr />
                    </h3>

                    <div class="col-md-12">
                        <h4><?=GOOGLE_RECAPTCHA?></h4>
                    </div>

                    <div class="col-md-6">
                        <label><?=GOOGLE_RECAPTCHA?></label>
                        <?php
                        $google_captcha_enable = Options::v('google_captcha_enable');
                        if ($google_captcha_enable == 'on') {
                            $enable_captcha = 'checked';
                        } else {
                            $enable_captcha = '';
                        }
                        ?>
                        <div class="form-group">
                            <input type="checkbox" class="" id="google_captcha_enable"
                            name="google_captcha_enable"  <?=$enable_captcha;?>>
                            <?=GOOGLE_RECAPTCHA_ENABLE?>
                            <small class="help-block"><?=GOOGLE_RECAPTCHA_ENABLE_DESCR?></small>
                        </div>

                    </div>
                    <div class="col-md-6">
                        <label><?=GOOGLE_RECAPTCHA_LANG?></label>
                        <div class="form-group">
                            <input class="form-control" type="text" name="google_captcha_lang"
                            value="<?=Options::v('google_captcha_lang');?>">
                            <small class="help-block"><?=GOOGLE_RECAPTCHA_LANG_DESCR?></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label><?=GOOGLE_RECAPTCHA_SITEKEY?></label>
                        <div class="form-group">
                            <input class="form-control" type="text" name="google_captcha_sitekey"
                            value="<?=Options::v('google_captcha_sitekey');?>">
                            <small class="help-block"><?=GOOGLE_RECAPTCHA_SITEKEY_DESCR?></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label><?=GOOGLE_RECAPTCHA_SECRET?></label>
                        <div class="form-group">
                            <input class="form-control" type="text" name="google_captcha_secret"
                            value="<?=Options::v('google_captcha_secret');?>">
                            <small class="help-block"><?=GOOGLE_RECAPTCHA_SECRET_DESCR?></small>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="alert alert-warning">
                            <?=GOOGLE_RECAPTCHA_INFO;?>

                        </div>
                    </div>
                </div><!-- Security Tab Stop -->
            </div>
                    </div>

        </section> <!-- TAB PANE END -->


<input type="hidden" name="token" value="<?=TOKEN;?>">
</form>
<div class="clearfix"></div>
<script>
    $('#myTab a').click(function (e) {
        e.preventDefault()
        $(this).tab('show')
    })
</script>
