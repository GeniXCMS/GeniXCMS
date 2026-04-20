<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');

/**
 * UserProfile Class
 *
 * Manages the frontend user profile system including extensible section menus
 * that can be registered by themes, modules, or developers.
 *
 * Usage (register a custom profile section from a module):
 * <code>
 *   UserProfile::registerSection('orders', [
 *       'label'      => 'My Orders',
 *       'icon'       => 'shopping_bag',         // material-symbols icon name
 *       'callback'   => 'MyModule::profileTab', // static callable
 *       'min_group'  => 6,                       // 0=Admin … 6=General Member
 *       'own_only'   => false,                   // true = only visible to profile owner
 *       'order'      => 10,
 *   ]);
 * </code>
 *
 * @since 2.3.0
 * @version 1.0.0
 */
class UserProfile
{
    /** @var array Registered profile sections */
    private static $sections = [];

    /** @var array Registered dropdown menu items */
    private static $dropdownItems = [];

    /**
     * Register a new user dropdown menu item.
     *
     * @param string $key    Unique menu identifier
     * @param array  $config Configuration array (label, url, icon, min_group, order, etc)
     * @return void
     */
    public static function registerDropdownItem(string $key, array $config): void
    {
        $defaults = [
            'label'     => ucfirst($key),
            'url'       => '#',
            'icon'      => 'chevron_right',
            'min_group' => 6,
            'order'     => 99,
            'divider'   => false,
            'danger'    => false,
        ];

        self::$dropdownItems[$key] = array_merge($defaults, $config);
    }

    /** @var bool Flag to check if core defaults are registered */
    private static $defaultsRegistered = false;

    /**
     * Get all registered dropdown menu items filtered by the active user's permissions.
     *
     * @return array
     */
    public static function getDropdownItems(): array
    {
        $viewerGroup = (int) (Session::val('group') ?? 7);
        $isLoggedIn  = User::isLoggedin();
        $username    = Session::val('username');

        // Populate system defaults if not already done
        if (!self::$defaultsRegistered && $isLoggedIn) {
            self::$defaultsRegistered = true;
            if (!isset(self::$dropdownItems['profile'])) {
            self::registerDropdownItem('profile', [
                'label' => 'My Profile',
                'url'   => Url::user($username),
                'icon'  => 'person',
                'order' => 10
            ]);
            self::registerDropdownItem('settings', [
                'label' => 'Account Settings',
                'url'   => Url::user($username, 'settings'),
                'icon'  => 'settings',
                'order' => 20
            ]);
            self::registerDropdownItem('div_sys', [
                'divider' => true,
                'order'   => 50
            ]);
            self::registerDropdownItem('dashboard', [
                'label'     => 'Admin Dashboard',
                'url'       => Site::$url . 'gxadmin/',
                'icon'      => 'dashboard',
                'min_group' => 4, // Admins & Managers
                'order'     => 60
            ]);
            self::registerDropdownItem('logout', [
                'label'  => 'Logout',
                'url'    => Url::login() . '?act=logout',
                'icon'   => 'logout',
                'danger' => true,
                'order'  => 999
            ]);
        }
    }

        $items = self::$dropdownItems;
        $items = Hooks::filter('user_profile_dropdown', $items);

        $filtered = [];
        foreach ((array) $items as $key => $cfg) {
            if (!$isLoggedIn) continue; // safety net
            
            if (isset($cfg['min_group']) && $cfg['min_group'] < 6) {
                if ($viewerGroup > $cfg['min_group']) continue;
            }
            $filtered[$key] = $cfg;
        }

        uasort($filtered, fn($a, $b) => ($a['order'] ?? 99) <=> ($b['order'] ?? 99));

        return $filtered;
    }

    /**
     * Register a new profile section.
     *
     * @param string $key    Unique section identifier (used in URL slug).
     * @param array  $config Section configuration array.
     * @return void
     */
    public static function registerSection(string $key, array $config): void
    {
        $defaults = [
            'label'     => ucfirst($key),
            'icon'      => 'person',
            'callback'  => null,
            'min_group' => 6,       // accessible to General Members and up
            'own_only'  => false,   // visible to everyone who has access
            'order'     => 99,
        ];

        self::$sections[$key] = array_merge($defaults, $config);
    }

    /**
     * Get all registered sections, optionally filtered for the current viewer
     * viewing a specific profile owner.
     *
     * @param string $profileUsername  The username whose profile is being viewed.
     * @return array Ordered and access-filtered section list.
     */
    public static function getSections(string $profileUsername): array
    {
        $viewerUsername = Session::val('username') ?: '';
        $viewerGroup    = (int) (Session::val('group') ?? 7);
        $isOwnProfile   = ($viewerUsername !== '' && $viewerUsername === $profileUsername);
        $isLoggedIn     = User::isLoggedin();

        $sections = self::$sections;

        // Allow modules/themes to add sections dynamically via hook
        $sections = Hooks::filter('user_profile_sections', $sections, [
            'profile_user'   => $profileUsername,
            'viewer_user'    => $viewerUsername,
            'is_own_profile' => $isOwnProfile,
        ]);

        // Filter by access
        $filtered = [];
        foreach ((array) $sections as $key => $cfg) {
            // own_only sections are only visible to the profile owner
            if ($cfg['own_only'] && !$isOwnProfile) {
                continue;
            }
            // min_group check (lower group number = higher privilege)
            // If min_group is 6 (General Member) it's publicly visible
            // For min_group < 6, user must be logged in and have appropriate group
            if ($cfg['min_group'] < 6) {
                if (!$isLoggedIn) continue;
                if ($viewerGroup > $cfg['min_group']) continue;
            }
            $filtered[$key] = $cfg;
        }

        // Sort by order
        uasort($filtered, fn($a, $b) => ($a['order'] ?? 99) <=> ($b['order'] ?? 99));

        return $filtered;
    }

    /**
     * Render the content for a specific profile section.
     *
     * @param string $section         Section key.
     * @param string $profileUsername The profile owner's username.
     * @param object $userRow         User database row object.
     * @return string  HTML content, or empty string if no callback.
     */
    public static function renderSection(string $section, string $profileUsername, object $userRow): string
    {
        // Allow hook to intercept and render custom section HTML
        $hookResult = Hooks::filter('user_profile_section_' . $section, '', [
            'username' => $profileUsername,
            'user'     => $userRow,
        ]);
        if ($hookResult !== '') {
            return $hookResult;
        }

        // Fall back to registered callback
        $cfg = self::$sections[$section] ?? null;
        if (!$cfg || !$cfg['callback']) {
            return '';
        }

        $callback = $cfg['callback'];

        if (is_callable($callback)) {
            return (string) call_user_func($callback, $profileUsername, $userRow);
        }

        // String 'Class::method' form
        if (is_string($callback) && str_contains($callback, '::')) {
            [$class, $method] = explode('::', $callback, 2);
            if (class_exists($class) && method_exists($class, $method)) {
                return (string) $class::$method($profileUsername, $userRow);
            }
        }

        return '';
    }

    /**
     * Retrieve a user's full public data (safe fields only).
     *
     * @param string $username Username (userid).
     * @return object|null
     */
    public static function getPublicData(string $username): ?object
    {
        // Use no explicit SELECT to avoid reserved keywords (e.g. `group` in SQLite/MySQL).
        $user = Query::table('user')
            ->where('userid', $username)
            ->first();

        return $user ?: null;
    }

    /**
     * Retrieve full user detail data (merged with user_detail table).
     *
     * @param string $username Username (userid).
     * @return object|null
     */
    public static function getDetailData(string $username): ?object
    {
        // Use user.* to avoid reserved-keyword conflicts (e.g. `group`). 
        // The same pattern is used in User::userdata(). Sensitive fields
        // (passwd) are deliberately never templated or displayed.
        $result = Query::table('user')
            ->select('user.*, ud.fname, ud.lname, ud.sex, ud.addr, ud.city, ud.state, ud.country, ud.postcode, ud.avatar')
            ->join('user_detail as ud', 'user.userid', '=', 'ud.userid')
            ->where('user.userid', $username)
            ->first();

        return $result ?: null;
    }

    /**
     * Get gravatar URL for a user, or fallback to default avatar.
     *
     * @param string $email User email.
     * @param int    $size  Image size in px.
     * @return string
     */
    public static function avatar(string $email, int $size = 100): string
    {
        if (class_exists('Image') && method_exists('Image', 'getGravatar')) {
            return Image::getGravatar($email, $size);
        }
        return 'https://www.gravatar.com/avatar/' . md5(strtolower(trim($email))) . '?s=' . $size . '&d=identicon';
    }

    /**
     * Process profile settings form submission.
     *
     * @param string $username The owner's username.
     * @return array ['success' => bool, 'message' => string]
     */
    public static function handleSettingsPost(string $username): array
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['profile_save'])) {
            return ['success' => false, 'message' => ''];
        }

        // Security: only the owner or admin can save
        $viewerUsername = Session::val('username') ?: '';
        $viewerGroup    = (int) (Session::val('group') ?? 7);
        if ($viewerUsername !== $username && $viewerGroup > 0) {
            return ['success' => false, 'message' => _('Access denied.')];
        }

        $userId = User::id($username);
        if (!$userId) {
            return ['success' => false, 'message' => _('User not found.')];
        }

        $fname = Typo::cleanX($_POST['fname'] ?? '');
        $lname = Typo::cleanX($_POST['lname'] ?? '');
        $city  = Typo::cleanX($_POST['city'] ?? '');
        $addr  = Typo::cleanX($_POST['addr'] ?? '');

        // Email change (only own profile)
        $message = _('Profile saved successfully.');
        if ($viewerUsername === $username) {
            $newEmail = Typo::cleanX($_POST['email'] ?? '');
            if ($newEmail !== '' && filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
                if (User::isEmail($newEmail, $userId)) {
                    Query::table('user')->where('id', $userId)->update(['email' => $newEmail]);
                } else {
                    $message = _('Email already taken. Other details saved.');
                }
            }

            // Password change
            $newPass  = $_POST['new_password'] ?? '';
            $confPass = $_POST['confirm_password'] ?? '';
            if ($newPass !== '' && $newPass === $confPass && strlen($newPass) >= 6) {
                $hashed = User::randpass(['userid' => $username, 'passwd' => $newPass]);
                Query::table('user')->where('id', $userId)->update(['passwd' => $hashed]);
            }
        }

        // Update user_detail
        $detailId = User::idDetail($username);
        $detailData = ['fname' => $fname, 'lname' => $lname, 'city' => $city, 'addr' => $addr];

        if ($detailId) {
            Query::table('user_detail')->where('userid', $username)->update($detailData);
        } else {
            Query::table('user_detail')->insert(array_merge($detailData, ['userid' => $username]));
        }

        Hooks::run('user_profile_settings_save_action', [
            'username' => $username,
            'post'     => $_POST,
        ]);

        return ['success' => true, 'message' => $message];
    }
    /**
     * Render the complete User Profile page as an HTML string.
     *
     * This is the primary rendering method. Themes either call this directly,
     * or build their own UI using `getSections()`, `getDetailData()`, etc.
     *
     * Usage in any Latte template (one line):
     * <code>
     *   {!UserProfile::renderPage($data)}
     * </code>
     *
     * @param array $data Controller data array (from user.control.php).
     * @return string Complete HTML for the profile content area.
     */
    public static function renderPage(array $data): string
    {
        $u           = $data['profile_user'] ?? null;
        $username    = $data['profile_username'] ?? '';
        $section     = $data['profile_section'] ?? 'profile';
        $sections    = $data['profile_sections'] ?? [];
        $isOwn       = $data['is_own_profile'] ?? false;
        $saveResult  = $data['save_result'] ?? ['success' => false, 'message' => ''];

        // Guard: if user object is missing, return a graceful error
        if (!$u) {
            return '<div style="padding:2.5rem;text-align:center;color:#6b7280;background:#f9fafb;border-radius:16px;">' . _('Profile not available.') . '</div>';
        }

        $avatarUrl   = self::avatar($u->email ?? '', 120);
        $displayName = trim(($u->fname ?? '') . ' ' . ($u->lname ?? ''));
        if (empty($displayName)) $displayName = htmlspecialchars($username);
        $groupName   = User::$group[$u->group ?? 6] ?? 'Member';
        $joinDate    = Date::format($u->join_date ?? '', 'F Y');

        $out = '';

        $out = '<div class="gx-user-profile-wrap">';

        // --- Notification bar ---
        if ($saveResult['success']) {
            $out .= '<div class="gx-alert gx-alert-success"><span>&#10003;</span> ' . htmlspecialchars($saveResult['message']) . '</div>';
        } elseif ($saveResult['message'] !== '') {
            $out .= '<div class="gx-alert gx-alert-danger"><span>&#9888;</span> ' . htmlspecialchars($saveResult['message']) . '</div>';
        }

        // --- Layout wrapper ---
        $out .= '<div class="gx-row">';

        // --- Sidebar ---
        $out .= '<aside class="gx-col-12 gx-md-col-3">';
        
        // Avatar + name card
        $out .= '<div class="gx-card gx-rounded gx-text-center gx-mb-3">'
              . '<div class="gx-d-flex gx-justify-center gx-mb-3">'
              . '<img src="' . $avatarUrl . '" class="gx-rounded-full gx-shadow-sm" style="width:96px;height:96px;border:4px solid #fff;object-fit:cover;" alt="">'
              . '</div>'
              . '<h1 class="gx-h5 gx-mb-1">' . $displayName . '</h1>'
              . '<div class="gx-text-muted gx-text-sm gx-fw-medium">@' . htmlspecialchars($username) . '</div>'
              . '<div class="gx-badge gx-badge-primary gx-mt-3">' . htmlspecialchars($groupName) . '</div>'
              . '</div>';

        // Nav links
        $out .= '<nav class="gx-list-group gx-rounded gx-mb-4">';
        foreach ($sections as $secKey => $secCfg) {
            $activeClass = ($section === $secKey) ? ' gx-bg-soft gx-text-primary' : '';
            $label  = htmlspecialchars($secCfg['label']);
            $href   = Url::user($username, $secKey);
            $out .= '<a href="' . $href . '" class="gx-list-group-item' . $activeClass . '">'
                  . '<span class="gx-mr-2">&#9679;</span> ' . $label . '</a>';
        }
        $out .= '</nav>';
        $out .= '</aside>';

        // --- Main panel ---
        $out .= '<div class="gx-col-12 gx-md-col-9">';

        if ($section === 'profile') {
            $out .= self::_renderProfileSection($data, $u, $username, $displayName, $groupName, $joinDate, $isOwn);
        } elseif ($section === 'settings') {
            $out .= self::_renderSettingsSection($data, $u, $username);
        } else {
            $sectionContent = $data['section_content'] ?? '';
            $sectionLabel   = htmlspecialchars($sections[$section]['label'] ?? ucfirst($section));
            $out .= '<div class="gx-card gx-rounded gx-p-4">'
                  . '<h2 class="gx-h4 gx-mb-4">' . $sectionLabel . '</h2>';
            if ($sectionContent) {
                $out .= $sectionContent;
            } else {
                $out .= '<div class="gx-text-center gx-p-5 gx-bg-soft gx-rounded" style="border:2px dashed var(--gx-border);">'
                      . '<div class="gx-h2 gx-text-muted gx-mb-3">&#128193;</div>'
                      . '<p class="gx-fw-bold gx-text-muted gx-m-0">' . _('No content available for this section.') . '</p>'
                      . '</div>';
            }
            $out .= '</div>';
        }

        $out .= '</div>'; // main panel
        $out .= '</div>'; // flex wrapper
        $out .= '</div>'; // gx-profile-wrap

        return $out;
    }

    // -------------------------------------------------------------------------
    // Private helpers for renderPage()
    // -------------------------------------------------------------------------

    private static function _renderProfileSection(array $data, $u, $username, $displayName, $groupName, $joinDate, $isOwn): string
    {
        $city = htmlspecialchars($u->city ?? '');
        $country = htmlspecialchars($u->country ?? '');

        $html = '<div class="gx-card gx-rounded gx-p-4">';
        $html .= '<div class="gx-d-flex gx-justify-between gx-items-center gx-mb-4">'
               . '<h2 class="gx-h4 gx-m-0">' . _('Profile Overview') . '</h2>';
        if ($isOwn) {
            $html .= '<a href="' . Url::user($username, 'settings') . '" class="gx-text-sm gx-fw-bold gx-text-primary gx-d-flex gx-items-center gx-gap-1">&#9998; ' . _('Edit Profile') . '</a>';
        }
        $html .= '</div>';

        $fields = [
            _('Full Name')     => $displayName,
            _('Username')      => '@' . htmlspecialchars($username),
            _('Role')          => htmlspecialchars($groupName),
            _('Member Since')  => htmlspecialchars($joinDate),
        ];
        if ($city)    $fields[_('City')]    = $city;
        if ($country) $fields[_('Country')] = $country;

        $html .= '<div class="gx-bg-soft gx-p-4 gx-rounded-lg gx-border">';
        $html .= '<div class="gx-row" style="margin-bottom: -1rem;">';
        foreach ($fields as $label => $value) {
            $html .= '<div class="gx-col-12 gx-md-col-6 gx-mb-3">'
                   . '<div class="gx-label gx-mb-1" style="font-size: 0.75rem; letter-spacing: 0.05em; text-transform: uppercase;">' . $label . '</div>'
                   . '<div class="gx-fw-bold gx-text-md gx-text-dark">' . $value . '</div>'
                   . '</div>';
        }
        $html .= '</div></div>';

        // Recent posts
        $posts = Query::table('posts')->where('author', $username)->where('status', '1')
                     ->orderBy('date', 'DESC')->limit(5)->get();
        if (!empty($posts)) {
            $html .= '<h3 class="gx-h5 gx-mt-5 gx-mb-3 gx-d-flex gx-items-center gx-gap-2"><span class="gx-text-primary">&#9997;</span> ' . _('Recent Posts') . '</h3>';
            $html .= '<div class="gx-list-group">';
            foreach ($posts as $p) {
                $img = Posts::getPostImage($p->id);
                $html .= '<a href="' . Url::post($p->id) . '" class="gx-list-group-item gx-d-flex gx-items-center gx-gap-3 gx-p-3">';
                if ($img) {
                    $html .= '<img src="' . Url::thumb($img, 'crop', '100x100') . '" class="gx-rounded" style="width:50px;height:50px;object-fit:cover;flex-shrink:0;" alt="">';
                }
                $typeBadge = '<span class="gx-badge gx-badge-outline gx-text-xs gx-ml-2">' . htmlspecialchars($p->type) . '</span>';
                $html .= '<div>'
                       . '<div class="gx-fw-bold gx-text-dark gx-d-flex gx-items-center">' . htmlspecialchars($p->title) . $typeBadge . '</div>'
                       . '<div class="gx-text-muted gx-text-xs">&#128197; ' . Date::format($p->date, 'd M Y') . '</div>'
                       . '</div></a>';
            }
            $html .= '</div>';
        }

        // Hook: allow modules to append content to profile view
        $extra = Hooks::filter('user_profile_overview_extra', '', ['username' => $username, 'user' => $u]);
        if ($extra) $html .= '<div style="margin-top:2.5rem;padding-top:2.5rem;border-top:1px solid #f3f4f6;">' . $extra . '</div>';

        $html .= '</div>';
        return $html;
    }

    public static function _renderSettingsSection(array $data, $u, string $username): string
    {
        $actionUrl = Url::user($username, 'settings');
        $html = '<div class="gx-card gx-rounded gx-p-4">';
        $html .= '<h2 class="gx-h4 gx-mb-4">' . _('Account Settings') . '</h2>';
        $html .= '<form method="post" action="' . $actionUrl . '">';
        $html .= '<input type="hidden" name="profile_save" value="1">';


        $groupStyle = 'display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:1.5rem;margin-bottom:1.5rem;';

        $html .= '<fieldset class="gx-mb-4 gx-border-0 gx-p-0">'
               . '<legend class="gx-text-sm gx-fw-bold gx-text-uppercase gx-text-primary gx-mb-3 gx-border-bottom gx-d-block gx-p-2">' . _('Personal Information') . '</legend>'
               . '<div class="gx-row">'
               . '<div class="gx-col-12 gx-md-col-6"><div class="gx-form-group"><label class="gx-label">' . _('First Name') . '</label><input type="text" name="fname" value="' . htmlspecialchars($u->fname ?? '') . '" class="gx-input gx-rounded" placeholder="' . _('First name') . '"></div></div>'
               . '<div class="gx-col-12 gx-md-col-6"><div class="gx-form-group"><label class="gx-label">' . _('Last Name') . '</label><input type="text" name="lname" value="' . htmlspecialchars($u->lname ?? '') . '" class="gx-input gx-rounded" placeholder="' . _('Last name') . '"></div></div>'
               . '<div class="gx-col-12 gx-md-col-6"><div class="gx-form-group"><label class="gx-label">' . _('City') . '</label><input type="text" name="city" value="' . htmlspecialchars($u->city ?? '') . '" class="gx-input gx-rounded" placeholder="' . _('Your city') . '"></div></div>'
               . '<div class="gx-col-12 gx-md-col-6"><div class="gx-form-group"><label class="gx-label">' . _('Address') . '</label><input type="text" name="addr" value="' . htmlspecialchars($u->addr ?? '') . '" class="gx-input gx-rounded" placeholder="' . _('Your address') . '"></div></div>'
               . '</div></fieldset>';


        $html .= '<fieldset class="gx-mb-4 gx-border-0 gx-p-0">'
               . '<legend class="gx-text-sm gx-fw-bold gx-text-uppercase gx-text-primary gx-mb-3 gx-border-bottom gx-d-block gx-p-2">' . _('Account & Security') . '</legend>'
               . '<div class="gx-row">'
               . '<div class="gx-col-12"><div class="gx-form-group"><label class="gx-label">' . _('Email Address') . '</label><input type="email" name="email" value="' . htmlspecialchars($u->email ?? '') . '" class="gx-input gx-rounded" style="max-width:500px;"><div class="gx-text-muted gx-text-xs gx-mt-1">' . _('Leave blank to keep current email.') . '</div></div></div>'
               . '<div class="gx-col-12 gx-md-col-6"><div class="gx-form-group"><label class="gx-label">' . _('New Password') . '</label><input type="password" name="new_password" class="gx-input gx-rounded" autocomplete="new-password"><div class="gx-text-muted gx-text-xs gx-mt-1">' . _('Min. 6 characters.') . '</div></div></div>'
               . '<div class="gx-col-12 gx-md-col-6"><div class="gx-form-group"><label class="gx-label">' . _('Confirm Password') . '</label><input type="password" name="confirm_password" class="gx-input gx-rounded" autocomplete="new-password"></div></div>'
               . '</div></fieldset>';


        // Extra fields from modules/hooks
        $extra = Hooks::filter('user_profile_settings_fields', '', ['username' => $username, 'user' => $u]);
        if ($extra) $html .= '<div style="margin-bottom:2.5rem;">' . $extra . '</div>';

        $html .= '<div class="gx-d-flex gx-gap-3 gx-items-center gx-mt-4 gx-pt-4 gx-border-top">'
               . '<button type="submit" class="gx-btn gx-btn-primary gx-px-4">' . _('Save Changes') . '</button>'
               . '<a href="' . Url::user($username) . '" class="gx-text-muted gx-fw-bold gx-text-sm">' . _('Cancel') . '</a>'
               . '</div>';

        $html .= '</form></div>';
        return $html;
    }
}

/* End of file UserProfile.class.php */
/* Location: ./inc/lib/UserProfile.class.php */
