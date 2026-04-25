<?php

class Sso
{
    public function __construct()
    {
        Hooks::attach('init', array('Sso', 'handleCallbacks'));
        Hooks::attach('footer_load_lib', array('Sso', 'loadlib'));
        Hooks::attach('login_form_footer', array('Sso', 'showSSOButtons'));
    }

    public static function handleCallbacks()
    {
        // 1. Google & Apple direct POST credentials
        if (isset($_POST['credential']) && isset($_POST['g_csrf_token'])) {
            self::verifyGoogle($_POST['credential']);
        }

        // 2. OAuth 2.0 Initialization Redirects
        if (isset($_GET['sso_login'])) {
            $provider = Typo::cleanX($_GET['sso_login']);
            $redirect_uri = urlencode(Site::$url . 'index.php?sso_handler=callback&provider=' . $provider);
            
            if ($provider == 'github') {
                $clientId = Options::v('sso_github_client_id');
                header("Location: https://github.com/login/oauth/authorize?client_id={$clientId}&redirect_uri={$redirect_uri}&scope=user:email");
                exit;
            } elseif ($provider == 'facebook') {
                $clientId = Options::v('sso_fb_app_id');
                header("Location: https://www.facebook.com/v17.0/dialog/oauth?client_id={$clientId}&redirect_uri={$redirect_uri}&scope=email,public_profile");
                exit;
            } elseif ($provider == 'x') {
                $clientId = Options::v('sso_x_client_id');
                $verifier = bin2hex(random_bytes(32));
                Session::set(['x_verifier' => $verifier]);
                $challenge = rtrim(strtr(base64_encode(hash('sha256', $verifier, true)), '+/', '-_'), '=');
                header("Location: https://twitter.com/i/oauth2/authorize?response_type=code&client_id={$clientId}&redirect_uri={$redirect_uri}&scope=users.read&state=state&code_challenge={$challenge}&code_challenge_method=S256");
                exit;
            } elseif ($provider == 'apple') {
                $clientId = Options::v('sso_apple_client_id');
                header("Location: https://appleid.apple.com/auth/authorize?client_id={$clientId}&redirect_uri={$redirect_uri}&response_type=code%20id_token&scope=name%20email&response_mode=form_post");
                exit;
            }
        }

        // 3. Apple OIDC POST Form Callback
        if (isset($_GET['sso_handler']) && $_GET['sso_handler'] == 'callback' && isset($_GET['provider']) && $_GET['provider'] == 'apple' && isset($_POST['id_token'])) {
            $parts = explode('.', $_POST['id_token']);
            if (count($parts) === 3) {
                // Decode JWT Payload
                $payload = json_decode(base64_decode(strtr($parts[1], '-_', '+/')), true);
                if (isset($payload['email'])) {
                    // Apple conditionally sends user object on first auth only via POST
                    $fname = 'Apple'; $lname = 'User';
                    if (isset($_POST['user'])) {
                        $applUser = json_decode($_POST['user'], true);
                        $fname = $applUser['name']['firstName'] ?? 'Apple';
                        $lname = $applUser['name']['lastName'] ?? 'User';
                    }
                    self::provisionAndLogin($payload['email'], $fname, $lname);
                }
            }
            Session::set(['alertDanger' => [_('Apple authentication failed.')]]);
            header("Location: " . Url::login());
            exit;
        }

        // 4. Standard OAuth 2.0 Authorization Code Callbacks
        if (isset($_GET['sso_handler']) && $_GET['sso_handler'] == 'callback' && isset($_GET['code'])) {
            $code = Typo::cleanX($_GET['code']);
            $provider = Typo::cleanX($_GET['provider'] ?? '');
            $redirect_uri = Site::$url . 'index.php?sso_handler=callback&provider=' . $provider;
            
            if ($provider == 'github') {
                $clientId = Options::v('sso_github_client_id');
                $clientSecret = Options::v('sso_github_secret');
                
                $response = self::curlPost('https://github.com/login/oauth/access_token', [
                    'client_id' => $clientId, 'client_secret' => $clientSecret, 'code' => $code, 'redirect_uri' => $redirect_uri
                ], ['Accept: application/json']);
                
                $data = json_decode($response, true);
                if (isset($data['access_token'])) {
                    $user_req = self::curlGet('https://api.github.com/user', ['Authorization: token ' . $data['access_token'], 'User-Agent: GeniXCMS']);
                    $user_data = json_decode($user_req, true);
                    $email_req = self::curlGet('https://api.github.com/user/emails', ['Authorization: token ' . $data['access_token'], 'User-Agent: GeniXCMS']);
                    $emails = json_decode($email_req, true);
                    $email = $emails[0]['email'] ?? ($user_data['email'] ?? null);
                    if ($email) {
                        self::provisionAndLogin($email, $user_data['name'] ?? $user_data['login'], '');
                    }
                }
            } elseif ($provider == 'facebook') {
                $clientId = Options::v('sso_fb_app_id');
                $clientSecret = Options::v('sso_fb_secret'); // ensure this is set if querying
                // Simple implicit token exchange for FB
                $response = self::curlGet("https://graph.facebook.com/v17.0/oauth/access_token?client_id={$clientId}&redirect_uri=".urlencode($redirect_uri)."&client_secret={$clientSecret}&code={$code}");
                $data = json_decode($response, true);
                if (isset($data['access_token'])) {
                    $user_req = self::curlGet("https://graph.facebook.com/me?fields=id,name,email&access_token=" . $data['access_token']);
                    $user_data = json_decode($user_req, true);
                    if (isset($user_data['email'])) {
                        self::provisionAndLogin($user_data['email'], $user_data['name'] ?? '', '');
                    }
                }
            } elseif ($provider == 'x') {
                $clientId = Options::v('sso_x_client_id');
                $clientSecret = Options::v('sso_x_secret');
                $verifier = $_SESSION['gxsess']['val']['x_verifier'] ?? '';
                
                $response = self::curlPost('https://api.twitter.com/2/oauth2/token', [
                    'client_id' => $clientId,
                    'code' => $code, 
                    'grant_type' => 'authorization_code',
                    'redirect_uri' => $redirect_uri, 
                    'code_verifier' => $verifier
                ], ['Authorization: Basic ' . base64_encode($clientId . ':' . $clientSecret)]);
                
                $data = json_decode($response, true);
                if (isset($data['access_token'])) {
                    $user_req = self::curlGet('https://api.twitter.com/2/users/me?user.fields=name,username', ['Authorization: Bearer ' . $data['access_token']]);
                    $user_data = json_decode($user_req, true);
                    if (isset($user_data['data']['username'])) {
                        // Twitter API v2 requires Elevated access or specific scopes to fetch email. 
                        // To ensure seamless login, map username to a specialized local email handle if email isn't provided.
                        $email = $user_data['data']['username'] . '@twitter.local';
                        self::provisionAndLogin($email, $user_data['data']['name'] ?? '', '');
                    }
                }
            }
            Session::set(['alertDanger' => [_('SSO authentication failed.')]]);
            header("Location: " . Url::login());
            exit;
        }
    }

    private static function verifyGoogle($jwt)
    {
        $opts = ["http" => ["method" => "GET", "header" => "User-Agent: GeniXCMS SSO\r\n"]];
        $context = stream_context_create($opts);
        $response = @file_get_contents("https://oauth2.googleapis.com/tokeninfo?id_token=" . urlencode($jwt), false, $context);
        
        if ($response) {
            $data = json_decode($response, true);
            if (isset($data['email']) && isset($data['email_verified']) && $data['email_verified'] == 'true') {
                self::provisionAndLogin($data['email'], $data['given_name'] ?? '', $data['family_name'] ?? '');
            }
        }
        Session::set(['alertDanger' => [_('Google Sign-In failed.')]]);
    }

    private static function provisionAndLogin($email, $fname, $lname)
    {
        $usr = Query::table('user')->where('email', $email)->first();
        if ($usr) {
            if ($usr->status == '1') {
                Session::set_session(['username' => $usr->userid, 'loggedin' => true, 'group' => $usr->group, 'rememberme' => true]);
                Hooks::run('user_login_action');
                header("Location: " . Site::$url);
                exit;
            } else {
                Session::set(['alertDanger' => [_('Your account is not active. Please contact support.')]]);
            }
        } else {
            // Auto-provision
            $base_userid = explode('@', $email)[0];
            $userid = $base_userid . rand(100, 999);
            User::create([
                'user' => ['userid' => $userid, 'passwd' => User::randpass(User::generatePass()), 'email' => $email, 'group' => '6', 'join_date' => date('Y-m-d H:i:s'), 'status' => '1'],
                'detail' => ['fname' => $fname, 'lname' => $lname, 'userid' => $userid]
            ]);
            $usr = Query::table('user')->where('email', $email)->first();
            Session::set_session(['username' => $usr->userid, 'loggedin' => true, 'group' => $usr->group, 'rememberme' => true]);
            Hooks::run('user_login_action');
            header("Location: " . Site::$url);
            exit;
        }
        header("Location: " . Url::login());
        exit;
    }

    private static function curlPost($url, $data, $headers = [])
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $res = curl_exec($ch);
        unset($ch);
        return $res;
    }

    private static function curlGet($url, $headers = [])
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if (!empty($headers)) curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $res = curl_exec($ch);
        unset($ch);
        return $res;
    }

    public static function loadlib()
    {
        if (Options::v('sso_google_client_id')) echo '<script src="https://accounts.google.com/gsi/client" async deferred></script>';
    }

    public static function showSSOButtons()
    {
        $gClientId = Options::v('sso_google_client_id');
        $ghClientId = Options::v('sso_github_client_id');
        $fbClientId = Options::v('sso_fb_app_id');
        $xClientId = Options::v('sso_x_client_id');
        $appleClientId = Options::v('sso_apple_client_id');
        
        if (empty($gClientId) && empty($ghClientId) && empty($fbClientId) && empty($xClientId) && empty($appleClientId)) return;
        
        echo '<div class="mt-4 mb-4 text-center position-relative">
            <hr class="text-muted opacity-25">
            <span class="text-muted small fw-bold px-3 bg-white position-absolute top-50 start-50 translate-middle" style="letter-spacing: 1px;">OR CONTINUE WITH</span>
        </div>
        <div class="d-flex flex-column gap-2 align-items-center">';

        if ($gClientId) {
            $currentUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            echo '<div id="g_id_onload" data-client_id="' . htmlspecialchars($gClientId) . '" data-context="signin" data-ux_mode="popup" data-login_uri="' . htmlspecialchars($currentUrl) . '" data-auto_prompt="false"></div>';
            echo '<div class="g_id_signin" data-type="standard" data-shape="pill" data-theme="outline" data-text="continue_with" data-size="large" data-logo_alignment="center"></div>';
        }
        
        if ($fbClientId) {
            echo '<a href="index.php?sso_login=facebook" class="btn btn-primary rounded-pill w-100 fw-bold d-flex align-items-center justify-content-center" style="max-width: 250px; background: #1877f2; border:none;"><i class="bi bi-facebook me-2 fs-5"></i> Continue with Facebook</a>';
        }

        if ($ghClientId) {
            echo '<a href="index.php?sso_login=github" class="btn btn-dark rounded-pill w-100 fw-bold d-flex align-items-center justify-content-center" style="max-width: 250px;"><i class="bi bi-github me-2 fs-5"></i> Continue with GitHub</a>';
        }

        if ($xClientId) {
            echo '<a href="index.php?sso_login=x" class="btn rounded-pill w-100 fw-bold d-flex align-items-center justify-content-center text-white" style="max-width: 250px; background: #000; border:none;"><i class="bi bi-twitter-x me-2 fs-5"></i> Continue with X</a>';
        }
        
        if ($appleClientId) {
            echo '<a href="index.php?sso_login=apple" class="btn rounded-pill w-100 fw-bold d-flex align-items-center justify-content-center text-dark border" style="max-width: 250px; background: #fff;"><i class="bi bi-apple me-2 fs-5"></i> Continue with Apple</a>';
        }

        echo '</div>';
    }

    public static function page($data) {}
}
