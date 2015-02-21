<?php
/**
* GeniXCMS - Content Management System
* 
* PHP Based Content Management System and Framework
*
* @package GeniXCMS
* @since 0.0.1 build date 20150202
* @version 0.0.1
* @link https://github.com/semplon/GeniXCMS
* @author Puguh Wijayanto (www.metalgenix.com)
* @copyright 2014-2015 Puguh Wijayanto
* @license http://www.opensource.org/licenses/mit-license.php MIT
*
*/?>
<form action="" method="POST" enctype="multipart/form-data">
<div class="row">
    <div class="col-md-12">
        <h1 class="clearfix"><i class="fa fa-wrench"></i> Settings Page
          <div class="pull-right">
            <button type="submit" name="change" class="btn btn-success" value="Change">
            <span class="glyphicon glyphicon-ok"></span>
              Change
            </button>
            <button type="reset" class="btn btn-danger" value="Cancel">
              <span class="glyphicon glyphicon-remove"></span>
              Cancel
            </button>
          </div>
        </h1>
        <hr />
    </div>
    <div class="col-sm-12" id="myTab">
        <ul class="nav nav-tabs" role="tablist">
          <li class="active"><a href="#general" role="tab" data-toggle="tab">General</a></li>
          <li><a href="#email" role="tab" data-toggle="tab">E-Mail</a></li>
          <li><a href="#social" role="tab" data-toggle="tab">Social</a></li>
          <li><a href="#logo" role="tab" data-toggle="tab">Logo</a></li>
          <li><a href="#library" role="tab" data-toggle="tab">Library</a></li>
          <li><a href="#posts" role="tab" data-toggle="tab">Posts</a></li>
          <li><a href="#payment" role="tab" data-toggle="tab">Payment</a></li>
        </ul>
        
        <!-- Tab panes -->
        <div class="tab-content">
       
          <div class="tab-pane active" id="general">
              <h3>Website Details</h3>
              <div class="col-sm-12">
                  <div class="row">
                      <div class="col-sm-6 form-group">
                          <label>Website Name</label>
                          <input type="text" name="sitename" value="<?=Options::get('sitename');?>" class="form-control">
                          <small>Your Website Name, Title</small>
                      </div>
                      <div class="col-sm-6 form-group">
                          <label>Website Slogan</label>
                          <input type="text" name="siteslogan" value="<?=Options::get('siteslogan');?>" class="form-control">
                          <small>Your Website Slogan</small>
                      </div>
                  </div>
                  
              </div>

              <div class="col-sm-12">
                  <div class="row">
                      <div class="col-sm-6 form-group">
                          <label>Website Domain</label>
                          <input type="text" name="sitedomain" value="<?=Options::get('sitedomain');?>" class="form-control">
                          <small>Your Domain, eg: example.org</small>
                      </div>
                      <div class="col-sm-6 form-group">
                          <label>Website URL</label>
                          <input type="text" name="siteurl" value="<?=Options::get('siteurl');?>" class="form-control">
                          <small>Your Website URL, eg: http://www.example.org</small>
                      </div>
                  </div>
                  
              </div>

              <div class="col-sm-12">
                  <div class="row">
                      <div class="col-sm-6 form-group">
                          <label>Website Keywords</label>
                          <input type="text" name="sitekeywords" value="<?=Options::get('sitekeywords');?>" class="form-control">
                          <small>Your Website Keywords, type your website main keywords.</small>
                      </div>
                      <div class="col-sm-6 form-group">
                          <label>Website Description</label>
                          <textarea  name="sitedesc" class="form-control"><?=Options::get('sitedesc');?></textarea>
                          <small>Your Website Description, describe your website.</small>
                      </div>
                  </div>
                  
              </div>
              <div class="col-sm-12">
                  <div class="row">
                      <div class="col-sm-6 form-group">
                          <label>Website E-mail</label>
                          <input type="text" name="siteemail" value="<?=Options::get('siteemail');?>" class="form-control">
                          <small>Your Website email.</small>
                      </div>
                      <div class="col-sm-6 form-group">
                          <label>Timezone</label>
                          <input type="text" name="timezone" value="<?=Options::get('timezone');?>" class="form-control">
                          <small>Your Website Timezone.</small>
                      </div>
                  </div>
                  
              </div>
              

          </div>
          <div class="tab-pane" id="email">
              <h3>Email Settings</h3>
              <div class="col-sm-12">
                  <div class="row">
                      <div class="col-sm-6 form-group">
                          <label>Mail/SMTP</label>
                          <?php
                              if(Options::get('mailtype') == 0){ $o = "selected"; $s = "";}
                              elseif(Options::get('mailtype') == 1 ) {$s = "selected"; $o = "";}
                          ?>
                          <select name="mailtype" class="form-control">
                              <option value="0" <?=$o;?>>Mail</option>
                              <option value="1" <?=$s;?>>SMTP</option>
                          </select>
                          <small>Choose using Mail or SMTP</small>
                      </div>
                      <div class="col-sm-6 form-group">
                          <label>Plain/SSL</label>
                          <?php
                              if(Options::get('smtpssl') == 0){ $o = "selected"; $s = "";}
                              elseif(Options::get('smtpssl') == 1 ) {$s = "selected"; $o = "";}
                          ?>
                          <select name="smtpssl" class="form-control">
                              <option value="0" <?=$o;?>>Plain</option>
                              <option value="1" <?=$s;?>>SSL</option>
                          </select>
                          <small>Use Plain Authentification or SSL</small>
                      </div>
                  </div>
                  
              </div>

              <div class="col-sm-12">
                  <div class="row">
                      <div class="col-sm-6 form-group">
                          <label>SMTP MailServer</label>
                          <input type="text" name="smtphost" value="<?=Options::get('smtphost');?>" class="form-control">
                          <small>Your mailserver, eg: mail.example.org. This will used when using SMTP</small>
                      </div>
                      <div class="col-sm-6 form-group">
                          <label>SMTP Username</label>
                          <input type="text" name="smtpuser" value="<?=Options::get('smtpuser');?>" class="form-control">
                          <small>Your SMTP Username, eg: user@example.org. This will used when using SMTP</small>
                      </div>
                  </div>
                  
              </div>

              <div class="col-sm-12">
                  <div class="row">
                      <div class="col-sm-6 form-group">
                          <label>SMTP Password</label>
                          <input type="password" name="smtppass" value="<?=Options::get('smtppass');?>" class="form-control">
                          <small>Your SMTP Password. This will used when using SMTP</small>
                      </div>
                      <div class="col-sm-6 form-group">
                          
                      </div>
                  </div>
                  
              </div>
          </div>
          <div class="tab-pane" id="social">
              <h3>Social Networking</h3>
              <div class="col-sm-12">
                  <div class="row">
                      <div class="col-sm-6 form-group">
                          <label>Facebook Account</label>
                          <input type="text" name="fbacc" value="<?=Options::get('fbacc');?>" class="form-control">
                          <small>Your Facebook Account</small>
                      </div>
                      <div class="col-sm-6 form-group">
                          <label>Facebook Page</label>
                          <input type="text" name="fbpage" value="<?=Options::get('fbpage');?>" class="form-control">
                          <small>Your Facebook Page</small>
                      </div>
                  </div>
                  
              </div>

              <div class="col-sm-12">
                  <div class="row">
                      <div class="col-sm-6 form-group">
                          <label>Twitter Account</label>
                          <div class="input-group">
                              <span class="input-group-addon">@</span>
                              <input type="text" name="twitter" value="<?=Options::get('twitter');?>" class="form-control">
                          </div>
                          <small>Your Twitter Account</small>
                      </div>
                      <div class="col-sm-6 form-group">
                          <label>LinkedIn Account</label>
                          <input type="text" name="linkedin" value="<?=Options::get('linkedin');?>" class="form-control">
                          <small>Your LinkedIn Account</small>
                      </div>
                  </div>
                  
              </div>

          </div>
          <div class="tab-pane" id="logo">
              <h3>Website Logo</h3>
              <div class="col-sm-12">
                  <div class="row">
                      <div class="col-sm-6 form-group">
                          <label>Current Logo</label>
                          <?php
                              $is_logourl = Options::get('is_logourl');
                              $logourl = Options::get('logourl');
                              $logo = Options::get('logo');
                              if($is_logourl == 'on' && $logourl != ''){
                                $logoimg = "<img src=\"".Options::get('logourl')."\" class=\"clearfix\">";
                              }elseif($is_logourl == 'off' && $logo != ''){
                                $logoimg = "<img src=\"".Options::get('siteurl').Options::get('logo')."\" class=\"clearfix\">";
                              }else{
                                $logoimg = '';
                              }
                          ?>
                          <div class="col-sm-12 clearfix">
                          <?=$logoimg;?>
                          
                          </div>
                          <small>Your Website Logo</small>
                          
                      </div>
                  </div>
              </div>

              <div class="col-sm-12">
                  <div class="row">
                      <div class="col-sm-6 form-group">
                          <label>Browse Image Logo</label>
                          <div class="">
                          <input type="file" name="logo" class="form-control" accept=".job,.png,.gif">
                          <small>Browse images if You want to upload your logo.</small>
                          </div>
                      </div>
                      <div class="col-sm-6 form-group">
                          
                          <label>Use Image URL</label>
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
                          <small>Your Website Logo URL</small>
                      </div>
                      
                  </div>
              </div>
              <div class="col-sm-12">
                  <div class="row">
                      <div class="col-sm-6 form-group">
                          <label>Website Favicon</label>
                          <input type="text" name="siteicon" value="<?=Options::get('siteicon');?>" class="form-control">
                          <small>Your Website Favicon URL</small>
                      </div>
                  </div>
              </div>

          </div>

          <div class="tab-pane" id="library">
              <h3>Enable or Disable Library</h3>
              <div class="col-sm-12">
                  <div class="row">
                      <div class="col-sm-6 form-group">
                          <label>Enable JQuery</label>
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
                                CDN
                              </button> 
                            </span>
                            </div>
                          <small>Check this if you want to use Jquery. Fill the version 
                          of Jquery. Default version is 1.11.1</small>
                      </div>
                      <div class="col-sm-6 form-group">
                          <label>Enable Bootstrap</label>
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
                                  LOCAL
                                </button> 
                              </span>
                            </div>
                          <small>Check this if you want to use Bootstrap. Bootstrap 
                          Version is not available, left it blank</small>
                      </div>
                      <div class="col-sm-6 form-group">
                          <label>Enable Fontawesome</label>
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
                                  CDN
                                </button> 
                              </span>
                            </div>
                          <small>Check this if you want to use Fontawesome. Fontawesome 
                          Version is not available, left it blank</small>
                      </div>
                      <div class="col-sm-6 form-group">
                          <label>Enable Editor</label>
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
                                <option value="summernote">Summernote</option>
                              </select> 
                            </span>
                          </div>
                          <small>Check this if you want to use Editor. Editor Version 
                          is not available, left it blank</small>
                      </div>
                      <div class="col-sm-6 form-group">
                          <label>Enable Bootstrap Validator</label>
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
                                  LOCAL
                                </button> 
                              </span>
                            </div>
                          <small>Check this if you want to use Bootstrap Validator. Bootstrap Validator 
                          Version is not available, left it blank</small>
                      </div>
                  </div>
              </div>

          </div>


          <div class="tab-pane" id="posts">
              <h3>Posts Config</h3>
              <div class="col-sm-12">
                  <div class="row">
                      <div class="col-sm-6 form-group">
                          <label>Post per Page</label>
                          <input type="number" name="post_perpage" value="<?=Options::get('post_perpage');?>" class="form-control" min='1'>
                          <small>Number of Posts to show per page. </small>
                          
                      </div>
                      <div class="col-sm-6 form-group">
                          <label>Pagination Type</label>
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
                              <option value="number" <?=$number;?>>Number</option>
                              <option value="pager" <?=$pager;?>>Pager</option>
                          </select>
                          <small>Default Type of Pagination. Number : <code>[1][2][3]</code>, Pager : <code>[Prev] [Next]</code> </small>
                      </div>
                      <div class="col-sm-6 form-group">
                          <label>Pinger</label>
                          <div class="input-group">
                              <span class="input-group-addon">http://</span>
                              <textarea name="pinger" class="form-control" style="height: 200px;" ><?=Options::get('pinger');?></textarea>
                          </div>
                          
                          <small>Set the Pinger of Search Engine. Use <label>{{domain}}</label> for your domain</small>
                          
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
            <h3>Payment</h3>
            <div class="col-sm-12">
                <h4>PayPal Configuration</h4>
                  <div class="row">
                      <div class="form-group  col-md-12 clearfix">
                          <label for="currency">Currency Symbol</label>
                          <select class="form-control col-md-6" id="currency" name="currency" required>
                              <option value="USD" <?=$usd;?>>$ (USD)</option>
                              <option value="EUR" <?=$euro;?>>&euro; (EUR)</option>
                              <option value="GBP" <?=$pound;?>>&pound; (GBP)</option>
                          </select>
                          <small class="help-block">Pick a Currency, default is USD</small>

                      </div>
                      <div class="form-group  col-md-6 clearfix">
                          <label for="ppsandbox">Enable Sandbox</label>
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
                          name="ppsandbox"  <?=$sandbox;?>> Enable Sandbox ?
                          </div>
                          <small class="help-block">Enable Sandbox</small>
                      </div>
                      <div class="form-group  col-md-6 clearfix">
                          <label for="ppemail">PayPal API Username</label>
                          <input type="text" class="form-control" id="ppemail" placeholder="API Username"
                          name="ppuser" value="<?=Options::get('ppuser');?>">
                          <small class="help-block">Your PayPal API Username</small>
                      </div>
                      <div class="form-group  col-md-6">
                          <label for="ppsecurity">PayPal API Password</label>
                          <input type="text" class="form-control" id="ppsecurity" placeholder="PayPal API Password"
                          name="pppass" value="<?=Options::get('pppass');?>">
                          <small class="help-block">Your API Password</small>
                      </div>
                      <div class="form-group  col-md-6 clearfix">
                          <label for="ppsecret">PayPal Signature</label>
                          <input type="text" class="form-control" id="ppsecret" placeholder="PayPal Signature"
                          name="ppsign" value="<?=Options::get('ppsign');?>">
                          <small class="help-block">Your PayPal Signature</small>
                      </div>
                      </div>
                      <div class="form-group col-md-12 clearfix">
                          <span class="alert alert-warning center-block">
                          Attention, please fill these API Credentials from Your PayPal Account website. See the documentations at  
                          <a href="https://developer.paypal.com/webapps/developer/docs/classic/api/apiCredentials/" target="_blank">
                              https://developer.paypal.com
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
</form>
<script>
$('#myTab a').click(function (e) {
  e.preventDefault()
  $(this).tab('show')
})
</script>