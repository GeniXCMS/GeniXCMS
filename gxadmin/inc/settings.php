<?php
/**
* GeniXCMS - Content Management System
* 
* PHP Based Content Management System and Framework
*
* @package GeniXCMS
* @since 0.0.1 build date 20150202
* @version 0.0.3
* @link https://github.com/semplon/GeniXCMS
* @link http://genixcms.org
* @author Puguh Wijayanto (www.metalgenix.com)
* @copyright 2014-2015 Puguh Wijayanto
* @license http://www.opensource.org/licenses/mit-license.php MIT
*
*/
if (isset($data['alertgreen'])) {
    # code...
    echo "<div class=\"alert alert-success\" >
    <button type=\"button\" class=\"close\" data-dismiss=\"alert\">
        <span aria-hidden=\"true\">&times;</span>
        <span class=\"sr-only\">Close</span>
    </button>
    ";
    foreach ($data['alertgreen'] as $alert) {
        # code...
        echo "$alert\n";
    }
    echo "</div>";
}
if (isset($data['alertred'])) {
    # code...
    echo "<div class=\"alert alert-danger\" >
    <button type=\"button\" class=\"close\" data-dismiss=\"alert\">
        <span aria-hidden=\"true\">&times;</span>
        <span class=\"sr-only\">Close</span>
    </button>";
    foreach ($data['alertred'] as $alert) {
        # code...
        echo "$alert\n";
    }
    echo "</div>";
}
?>
<form action="" method="POST" enctype="multipart/form-data">
    <div class="row">
        <div class="col-md-12">
            <h1 class="clearfix">
                <div class="pull-left">
                    <i class="fa fa-wrench"></i> <?=SETTINGS;?>
                </div>
                <div class="pull-right">
                    <button type="submit" name="change" class="btn btn-success" value="Change">
                        <span class="glyphicon glyphicon-ok"></span>
                        <?=CHANGE;?>
                    </button>
                    <button type="reset" class="btn btn-danger" value="Cancel">
                        <span class="glyphicon glyphicon-remove"></span>
                        <?=CANCEL;?>
                    </button>
                </div>
            </h1>
            <hr />
        </div>
        <div class="col-sm-12" id="myTab">
            <ul class="nav nav-tabs" role="tablist">
                <li class="active"><a href="#general" role="tab" data-toggle="tab"><?=GENERAL;?></a></li>
                <li><a href="#localization" role="tab" data-toggle="tab"><?=LOCALIZATION;?></a></li>
                <li><a href="#email" role="tab" data-toggle="tab"><?=EMAIL;?></a></li>
                <li><a href="#social" role="tab" data-toggle="tab"><?=SOCIAL;?></a></li>
                <li><a href="#logo" role="tab" data-toggle="tab"><?=LOGO;?></a></li>
                <li><a href="#library" role="tab" data-toggle="tab"><?=LIBRARY;?></a></li>
                <li><a href="#posts" role="tab" data-toggle="tab"><?=POSTS;?></a></li>
                <li><a href="#payment" role="tab" data-toggle="tab"><?=PAYMENT;?></a></li>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content">

                <div class="tab-pane active" id="general">
                    <h3>
                        <?=WEBSITE_DETAIL;?>
                        <hr />
                    </h3>
                    <div class="col-sm-12">
                        <div class="row">
                            <div class="col-sm-6 form-group">
                                <label><?=WEBSITE_NAME;?></label>
                                <input type="text" name="sitename" value="<?=Site::$name;?>" class="form-control">
                                <small><?=WEBSITE_NAME_DESC;?></small>
                            </div>
                            <div class="col-sm-6 form-group">
                                <label><?=WEBSITE_SLOGAN;?></label>
                                <input type="text" name="siteslogan" value="<?=Options::get('siteslogan');?>" class="form-control">
                                <small><?=WEBSITE_SLOGAN_DESC;?></small>
                            </div>
                        </div>
                        
                    </div>

                    <div class="col-sm-12">
                        <div class="row">
                            <div class="col-sm-6 form-group">
                                <label><?=WEBSITE_DOMAIN;?></label>
                                <input type="text" name="sitedomain" value="<?=Site::$domain;?>" class="form-control">
                                <small><?=WEBSITE_DOMAIN_DESC;?></small>
                            </div>
                            <div class="col-sm-6 form-group">
                                <label><?=WEBSITE_URI;?></label>
                                <input type="text" name="siteurl" value="<?=Site::$url;?>" class="form-control">
                                <small><?=WEBSITE_URI_DESC;?></small>
                            </div>
                        </div>
                        
                    </div>

                    <div class="col-sm-12">
                        <div class="row">
                            <div class="col-sm-6 form-group">
                                <label><?=WEBSITE_KEYWORDS;?></label>
                                <input type="text" name="sitekeywords" value="<?=Site::$key;?>" class="form-control">
                                <small><?=WEBSITE_KEYWORDS_DESC;?></small>
                            </div>
                            <div class="col-sm-6 form-group">
                                <label><?=WEBSITE_DESCRIPTION;?></label>
                                <textarea  name="sitedesc" class="form-control"><?=Site::$desc;?></textarea>
                                <small><?=WEBSITE_DESCRIPTION_DESC;?></small>
                            </div>
                        </div>
                        
                    </div>
                    <div class="col-sm-12">
                        <div class="row">
                            <div class="col-sm-6 form-group">
                                <label><?=WEBSITE_EMAIL;?></label>
                                <input type="text" name="siteemail" value="<?=Options::get('siteemail');?>" class="form-control">
                                <small><?=WEBSITE_EMAIL_DESCR;?></small>
                            </div>
                            
                        </div>
                        
                    </div>
                    

                </div>

                <div class="tab-pane" id="localization">
                    <h3>
                        <?=LOCALIZATION;?>
                        <hr />
                    </h3>
                    
                    <div class="col-sm-12">
                        <div class="row">
                            <div class="col-sm-6 form-group">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <label><?=COUNTRY;?></label>
                                        <select name="country_id" class="form-control">
                                            <?=Date::optCountry(Options::get('country_id'));?>
                                        </select>
                                        <small><?=COUNTRY_DESC;?></small>
                                    </div>
                                    <div class="col-sm-6">
                                        <label><?=TIMEZONE;?></label>
                                        <select name="timezone" class="form-control">
                                            <?=Date::optTimeZone(Options::get('timezone'));?>
                                        </select>
                                        <small><?=TIMEZONE_DESC;?></small>
                                    </div>

                                </div>
                            </div>
                            <div class="col-sm-6 form-group">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <label><?=WEBSITE_LANG;?></label>
                                        <select name="system_lang" class="form-control">
                                            <?=Language::optDropdown(Options::get('system_lang'));?>
                                        </select>
                                        <small><?=WEBSITE_LANG_DESC;?></small>
                                    </div>
                                    <div class="col-sm-6">
                                        <label><?=CHARSET;?></label>
                                        <input name="charset" class="form-control" value="<?=Options::get('charset');?>">
                                        <small><?=CHARSET_DESC;?></small>
                                    </div>

                                </div>
                                
                            </div>
                        </div>
                        
                    </div>
                    

                </div>

                <div class="tab-pane" id="email">
              <h3><?=SETTINGS_EMAIL_SETTINGS;?></h3>
              <div class="col-sm-12">
                  <div class="row">
                      <div class="col-sm-6 form-group">
                          <label><?=SETTINGS_EMAIL_MAIL;?></label>
                          <?php
                              if(Options::get('mailtype') == 0){ $o = "selected"; $s = "";}
                              elseif(Options::get('mailtype') == 1 ) {$s = "selected"; $o = "";}
                          ?>
                          <select name="mailtype" class="form-control">
                              <option value="0" <?=$o;?>>Mail</option>
                              <option value="1" <?=$s;?>>SMTP</option>
                          </select>
                          <small><?=SETTINGS_EMAIL_MAIL_DESCR;?></small>
                      </div>
                      <div class="col-sm-6 form-group">
                          <label><?=SETTINGS_EMAIL_SMTP;?></label>
                          <input type="text" name="smtpport" value="<?=Options::get('smtpport');?>" class="form-control">
                              
                          <small><?=SETTINGS_EMAIL_SMTP_DESCR;?></small>
                      </div>
                  </div>
                  
              </div>

              <div class="col-sm-12">
                  <div class="row">
                      <div class="col-sm-6 form-group">
                          <label><?=SETTINGS_EMAIL_MAILSRV;?></label>
                          <input type="text" name="smtphost" value="<?=Options::get('smtphost');?>" class="form-control">
                          <small><?=SETTINGS_EMAIL_MAILSRV_DESCR;?></small>
                      </div>
                      <div class="col-sm-6 form-group">
                          <label><?=SETTINGS_EMAIL_SMTP_USR;?></label>
                          <input type="text" name="smtpuser" value="<?=Options::get('smtpuser');?>" class="form-control">
                          <small><?=SETTINGS_EMAIL_SMTP_USR_DESCR;?></small>
                      </div>
                  </div>
                  
              </div>

              <div class="col-sm-12">
                  <div class="row">
                      <div class="col-sm-6 form-group">
                          <label><?=SETTINGS_EMAIL_SMTP_PWD;?></label>
                          <input type="password" name="smtppass" value="<?=Options::get('smtppass');?>" class="form-control">
                          <small><?=SETTINGS_EMAIL_SMTP_PWD_DESCR;?></small>
                      </div>
                      <div class="col-sm-6 form-group">
                          
                      </div>
                  </div>
                  
              </div>
          </div>
          <div class="tab-pane" id="social">
              <h3><?=SETTINGS_SOCIAL;?></h3>
              <div class="col-sm-12">
                  <div class="row">
                      <div class="col-sm-6 form-group">
                          <label><?=SETTINGS_SOCIAL_FBACC;?></label>
                          <input type="text" name="fbacc" value="<?=Options::get('fbacc');?>" class="form-control">
                          <small><?=SETTINGS_SOCIAL_FBACC_DESCR;?></small>
                      </div>
                      <div class="col-sm-6 form-group">
                          <label><?=SETTINGS_SOCIAL_FBPAGE;?></label>
                          <input type="text" name="fbpage" value="<?=Options::get('fbpage');?>" class="form-control">
                          <small><?=SETTINGS_SOCIAL_FBPAGE_DESCR;?></small>
                      </div>
                  </div>
                  
              </div>

              <div class="col-sm-12">
                  <div class="row">
                      <div class="col-sm-6 form-group">
                          <label><?=SETTINGS_SOCIAL_TWITTER;?></label>
                          <div class="input-group">
                              <span class="input-group-addon">@</span>
                              <input type="text" name="twitter" value="<?=Options::get('twitter');?>" class="form-control">
                          </div>
                          <small><?=SETTINGS_SOCIAL_TWITTER_DESCR;?></small>
                      </div>
                      <div class="col-sm-6 form-group">
                          <label><?=SETTINGS_SOCIAL_LINKEDIN;?></label>
                          <input type="text" name="linkedin" value="<?=Options::get('linkedin');?>" class="form-control">
                          <small><?=SETTINGS_SOCIAL_LINKEDIN_DESCR;?></small>
                      </div>
                  </div>
                  
              </div>

          </div>
          <div class="tab-pane" id="logo">
              <h3><?=SETTINGS_LOGO;?></h3>
              <div class="col-sm-12">
                  <div class="row">
                      <div class="col-sm-6 form-group">
                          <label><?=SETTINGS_LOGO_CURRENT;?></label>
                          <?php
                              $is_logourl = Options::get('is_logourl');
                              $logourl = Options::get('logourl');
                              $logo = Options::get('logo');
                              if($is_logourl == 'on' && $logourl != ''){
                                $logoimg = "<img src=\"".Options::get('logourl')."\" class=\"clearfix\">";
                              }elseif($is_logourl == 'off' && $logo != ''){
                                $logoimg = "<img src=\"".Site::$url.Options::get('logo')."\" class=\"clearfix\">";
                              }else{
                                $logoimg = '';
                              }
                          ?>
                          <div class="col-sm-12 clearfix">
                          <?=$logoimg;?>
                          
                          </div>
                          <small><?=SETTINGS_LOGO_CURRENT_DESCR;?></small>
                          
                      </div>
                  </div>
              </div>

              <div class="col-sm-12">
                  <div class="row">
                      <div class="col-sm-6 form-group">
                          <label><?=SETTINGS_LOGO_BROWSE;?></label>
                          <div class="">
                          <input type="file" name="logo" class="form-control" accept=".job,.png,.gif">
                          <small><?=SETTINGS_LOGO_BROWSE_DESCR;?></small>
                          </div>
                      </div>
                      <div class="col-sm-6 form-group">
                          
                          <label><?=SETTINGS_LOGO_URL;?></label>
                          <div class="input-group">

                              <span class="input-group-addon">
                              <?php if(Options::get('is_logourl') == 'on') { $is_logourl = 'checked'; } 
                              else{ $is_logourl = 'off';} 
                              ?>
                              <input type="checkbox" name="is_logourl" rel="tooltip" 
                              title="Check here if you want to use URL" <?=$is_logourl;?>>
                              </span>
                              <input type="text" name="logourl" value="<?=Options::get('logourl');?>" class="form-control">
                          </div>
                          <small><?=SETTINGS_LOGO_URL_DESCR;?></small>
                      </div>
                      
                  </div>
              </div>
              <div class="col-sm-12">
                  <div class="row">
                      <div class="col-sm-6 form-group">
                          <label><?=SETTINGS_LOGO_FAVICON;?></label>
                          <input type="text" name="siteicon" value="<?=Options::get('siteicon');?>" class="form-control">
                          <small><?=SETTINGS_LOGO_FAVICON_DESCR;?></small>
                      </div>
                  </div>
              </div>

          </div>

          <div class="tab-pane" id="library">
              <h3><?=SETTINGS_LIBRARY;?></h3>
              <div class="col-sm-12">
                  <div class="row">
                      <div class="col-sm-6 form-group">
                          <label><?=SETTINGS_LIBRARY_JQUERY;?></label>
                              <?php if(Options::get('use_jquery') == 'on') { $use_jquery = 'checked'; } 
                              else{ $use_jquery = 'off';} 
                              ?>
                            <div class="input-group">
                            <span class="input-group-addon">
                              <input type="checkbox" name="use_jquery" rel="tooltip"
                              title="Check here if you want to use URL" <?=$use_jquery;?>> 
                            </span>
                              <input type="text" name="jquery_v" rel="tooltip" 
                              class="form-control" placeholder="Jquery Version" 
                              value="<?=Options::get('jquery_v');?>">
                            <span class="input-group-btn">
                              <button type="" name="" rel="tooltip"
                              title="" class="btn btn-default">
                                <?=SETTINGS_LIBRARY_CDN;?>
                              </button> 
                            </span>
                            </div>
                          <small><?=SETTINGS_LIBRARY_JQUERY_DESCR;?></small>
                      </div>
                      <div class="col-sm-6 form-group">
                          <label><?=SETTINGS_LIBRARY_BOOTSTRAP;?></label>
                              <?php 
                                if(Options::get('use_bootstrap') == 'on') { 
                                  $use_bootstrap = 'checked'; 
                                }else{ 
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
                              readonly value="<?=Options::get('bs_v');?>">
                              <span class="input-group-btn">
                                <button type="" name="" rel="tooltip"
                                title="" class="btn btn-default">
                                  <?=SETTINGS_LIBRARY_LOCAL;?>
                                </button> 
                              </span>
                            </div>
                          <small><?=SETTINGS_LIBRARY_BOOTSTRAP_DESCR;?></small>
                      </div>
                      <div class="col-sm-6 form-group">
                          <label><?=SETTINGS_LIBRARY_FAWESOME;?></label>
                              <?php 
                                if(Options::get('use_fontawesome') == 'on') { 
                                    $use_fontawesome = 'checked'; 
                                  }else{ 
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
                              readonly value="<?=Options::get('fontawesome_v');?>">
                              <span class="input-group-btn">
                                <button type="" name="" rel="tooltip"
                                title="" class="btn btn-default">
                                  <?=SETTINGS_LIBRARY_CDN;?>
                                </button> 
                              </span>
                            </div>
                          <small><?=SETTINGS_LIBRARY_FAWESOME_DESCR;?></small>
                      </div>
                      <div class="col-sm-6 form-group">
                          <label><?=SETTINGS_LIBRARY_EDITOR;?></label>
                              <?php 
                                if(Options::get('use_editor') == 'on') { 
                                  $use_editor = 'checked'; 
                                }else{ 
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
                              readonly value="<?=Options::get('editor_v');?>">
                            <span class="input-group-btn">
                              <select name="editor_type" rel="tooltip"
                              title=""
                              class="btn btn-default">
                                <option value="summernote"><?=SETTINGS_LIBRARY_SUMMERNOTE;?></option>
                              </select> 
                            </span>
                          </div>
                          <small><?=SETTINGS_LIBRARY_EDITOR_DESCR;?></small>
                      </div>
                      <div class="col-sm-6 form-group">
                          <label><?=SETTINGS_LIBRARY_BVALIDATOR;?></label>
                              <?php 
                                if(Options::get('use_bsvalidator') == 'on') { 
                                    $use_bsvalidator = 'checked'; 
                                  }else{ 
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
                              readonly value="<?=Options::get('bsvalidator_v');?>">
                              <span class="input-group-btn">
                                <button type="" name="" rel="tooltip"
                                title="" class="btn btn-default">
                                  <?=SETTINGS_LIBRARY_LOCAL;?>
                                </button> 
                              </span>
                            </div>
                          <small><?=SETTINGS_LIBRARY_BVALIDATOR_DESCR;?></small>
                      </div>
                  </div>
              </div>

          </div>


          <div class="tab-pane" id="posts">
              <h3><?=SETTINGS_POSTS;?></h3>
              <div class="col-sm-12">
                  <div class="row">
                      <div class="col-sm-6 form-group">
                          <label><?=SETTINGS_POSTS_PERPAGE;?></label>
                          <input type="number" name="post_perpage" value="<?=Options::get('post_perpage');?>" class="form-control" min='1'>
                          <small><?=SETTINGS_POSTS_PERPAGE_DESCR;?></small>
                          
                      </div>
                      <div class="col-sm-6 form-group">
                          <label><?=SETTINGS_POSTS_PAGINATION;?></label>
                          <?php
                              if(Options::get('pagination')=='number'){
                                  $number = 'selected'; 
                                  $pager = '';
                              }elseif(Options::get('pagination') == 'pager'){
                                  $pager = 'selected';
                                  $number = '';
                              }else{
                                  $pager = '';
                                  $number = '';
                              }
                          ?>
                          <select  name="pagination" class="form-control">
                              <option value="number" <?=$number;?>><?=SETTINGS_POSTS_PAGINATION_NUMBER;?></option>
                              <option value="pager" <?=$pager;?>><?=SETTINGS_POSTS_PAGINATION_PAGER;?></option>
                          </select>
                          <small><?=SETTINGS_POSTS_PAGINATION_DESCR;?> Number : <code>[1][2][3]</code>, Pager : <code>[Prev] [Next]</code> </small>
                      </div>
                      <div class="col-sm-6 form-group">
                          <label><?=SETTINGS_POSTS_PINGER;?></label>
                          <div class="input-group">
                              <span class="input-group-addon"><?=SETTINGS_POSTS_PINGER_HTTP?></span>
                              <textarea name="pinger" class="form-control" style="height: 200px;" ><?=Options::get('pinger');?></textarea>
                          </div>
                          
                          <small><?=SETTINGS_POSTS_PINGER_DESCR?></small>
                          
                      </div>
                  </div>
              </div>

          </div><!-- Posts Tab END -->

          <?php
              $curr = Options::get('currency');
              if ($curr == "USD") {
                  $usd = "SELECTED";
                  $euro = "";
                  $pound = "";
              } elseif ($curr == "EUR") {
                  $euro = "SELECTED";
                  $usd = "";
                  $pound = "";
              } elseif ($curr == "GBP") {
                  $pound = "SELECTED";
                  $usd = "";
                  $euro = "";
              } else {
                  $pound = "";
                  $usd = "";
                  $euro = "";
              }
          ?>

          <!-- Payment Tab Start -->
          <div class="tab-pane" id="payment">
            <h3><?=SETTINGS_PAYMENT?></h3>
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
                          <label for="ppsandbox"><?=SETTINGS_PAYMENT_SENDBOX?></label>
                          <div class="form-group">
                          <?php
                          $ppsandbox = Options::get('ppsandbox');
                            if($ppsandbox == 'on'){
                                $sandbox = "checked";
                                $sandval = 0;
                            }else{
                                $sandbox = "";
                                $sandval = "1";
                            }
                          ?>
                          
                          <input type="checkbox" class="" id="ppsandbox"
                          name="ppsandbox"  <?=$sandbox;?>><?=SETTINGS_PAYMENT_SENDBOX_EN?>
                          </div>
                          <small class="help-block"><?=SETTINGS_PAYMENT_SENDBOX_EN_DESCR?></small>
                      </div>
                      <div class="form-group  col-md-6 clearfix">
                          <label for="ppemail"><?=SETTINGS_PAYMENT_PAYPALAPI_USR?></label>
                          <input type="text" class="form-control" id="ppemail" placeholder="API Username"
                          name="ppuser" value="<?=Options::get('ppuser');?>">
                          <small class="help-block"><?=SETTINGS_PAYMENT_PAYPALAPI_USR_DESCR?></small>
                      </div>
                      <div class="form-group  col-md-6">
                          <label for="ppsecurity"><?=SETTINGS_PAYMENT_PAYPALAPI_PWD?></label>
                          <input type="text" class="form-control" id="ppsecurity" placeholder="PayPal API Password"
                          name="pppass" value="<?=Options::get('pppass');?>">
                          <small class="help-block"><?=SETTINGS_PAYMENT_PAYPALAPI_PWD_DESCR?></small>
                      </div>
                      <div class="form-group  col-md-6 clearfix">
                          <label for="ppsecret"><?=SETTINGS_PAYMENT_PAYPALAPI_SIGN?></label>
                          <input type="text" class="form-control" id="ppsecret" placeholder="PayPal Signature"
                          name="ppsign" value="<?=Options::get('ppsign');?>">
                          <small class="help-block"><?=SETTINGS_PAYMENT_PAYPALAPI_SIGN_DESCR?></small>
                      </div>
                      </div>
                      <div class="form-group col-md-12 clearfix">
                          <span class="alert alert-warning center-block">
                          <?=SETTINGS_PAYMENT_ALERT?>
                          </a>
                          </span>
                      </div>
                  </div>
                  
              </div>
          </div>
          <!-- Payment Tab Stop -->

        </div> <!-- TAB PANE END -->

        


    </div>
</div>
<input type="hidden" name="token" value="<?=TOKEN;?>">
</form>
<script>
    $('#myTab a').click(function (e) {
        e.preventDefault()
        $(this).tab('show')
    })
</script>
