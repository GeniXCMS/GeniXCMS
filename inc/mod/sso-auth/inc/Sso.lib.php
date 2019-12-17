<?php

class Sso
{
    public function __construct()
    {

        Hooks::attach('footer_load_lib', array('Sso', 'loadlib'));
        Hooks::attach('login_form_footer', array('Sso', 'showFBLogin'));
    }

    public static function loadlib()
    {
        $url = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https" : "http") . "://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        echo '<script>
        // This is called with the results from from FB.getLoginStatus().
  function statusChangeCallback(response) {
    console.log(\'statusChangeCallback\');
    console.log(response);

    if (response.status === \'connected\') {
      // Logged into your app and Facebook.
      testAPI();
    
    } else {
        // The person is not logged into your app or we are unable to tell.
        document.getElementById(\'status\').innerHTML = \'Please log \' +
        \'into this app.\';

    }
  }


  function checkLoginState() {
    FB.getLoginStatus(function(response) {
      statusChangeCallback(response);
    });
  }


  function testAPI() {
    console.log(\'Welcome!  Fetching your information.... \');
    FB.api(\'/me\', function(response) {
        console.log(response);
      console.log(\'Successful login for: \' + response.name);
      document.getElementById(\'status\').innerHTML =
        \'Thanks for logging in, \' + response.name + \'!\';
    });
  }

  function loginFB() {
    FB.login(function(response) {
        if (response.authResponse) {
         console.log(\'Welcome!  Fetching your information.... \');
         FB.api(\'/me\', function(response) {
           console.log(\'Good to see you, \' + response.name + \'.\');
         });
        } else {
         console.log(\'User cancelled login or did not fully authorize.\');
        }
    });
  }
  window.fbAsyncInit = function() {
    FB.init({
      appId      : \'422479467810457\',
      cookie     : true,
      xfbml      : true,
      version    : \'v2.10\'
    });
    FB.AppEvents.logPageView();
    FB.getLoginStatus(
      function(response) {
        statusChangeCallback(response);
      }
    );
    FB.Event.subscribe(\'auth.login\', function(r){
         console.log(r.status);
         if ( r.status === \'connected\'){
              window.location.href = \''.$url.'\'
         }
      });
  };

  (function(d, s, id){
     var js, fjs = d.getElementsByTagName(s)[0];
     if (d.getElementById(id)) {return;}
     js = d.createElement(s); js.id = id;
     js.src = "//connect.facebook.net/en_US/sdk.js";
     fjs.parentNode.insertBefore(js, fjs);
   }(document, \'script\', \'facebook-jssdk\'));
</script>';
    }

    public static function showFBLogin()
    {
        echo '<div class="clearfix">&nbsp;</div>
        <div class="fb-login-button" data-max-rows="1" data-size="large" data-button-type="login_with" data-show-faces="false" data-auto-logout-link="false" data-use-continue-as="false" scope="email"></div>
        <div id="status">
        </div>';
    }

    public static function page($data)
    {
        // global $data;
        // if (SMART_URL) {
        //     $data = $data[0];
        // } else {
        //     $data = $_GET;
        // }
        // print_r($data[0]);
        if ($data[0]['mod'] == 'page') {

            Mod::inc('frontpage', $data, realpath(__DIR__.'/../layout/'));
        }
    }
}
