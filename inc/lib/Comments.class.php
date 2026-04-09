<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 * @since 1.0.0 build date 20160830
 * @version 2.1.1
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

class Comments
{
    /**
     * Comments Constructor.
     */
    public function __construct()
    {
    }

    /**
     * Renders the comment submission form.
     * Handles POST submission internally and displays success/error alerts.
     *
     * @return string The rendered HTML form.
     */
    public static function form()
    {
        if (self::isEnable()) {
            Hooks::attach('footer_load_lib', array('Comments', 'validateJsComment'));
            Theme::editor('mini', '200', Hooks::filter('comment_allowed_blocks', ['paragraph', 'quote', 'code', 'ul', 'ol']));

            $html = '<a id="commentform"></a><div class="col-md-12 comments-wrapper clearfix">';
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                error_log("COMMENT POST DETECTED: " . print_r($_POST, true));
            }
            if (isset($_POST['addComment'])) {
                $data_result = self::addComment($_POST);
                if (isset($data_result['alertDanger'])) {
                    $html .= '<div class="alert alert-danger rounded-0 border-0 shadow-sm mb-4"><strong>' . _('Error') . ':</strong><br>' . implode('<br>', $data_result['alertDanger']) . '</div>';
                }
                if (isset($data_result['alertSuccess'])) {
                    $html .= '<div class="alert alert-success rounded-0 border-0 shadow-sm mb-4">' . implode('<br>', $data_result['alertSuccess']) . '</div>';
                }
            }

            $html .= '
            <form action="" method="POST" id="commentForm">
            <div id="cancelreply" style="margin-bottom: 15px;"></div>
            <div class="row">';
            if (null === Session::val('username')) {
                $html .= '
            
                <div class="form-group col-md-6">
                    <label class="">' . _("Name") . '</label>
                    <input type="text" id="name" name="comments-name" class="form-control" required>
                </div>
                <div class="form-group col-md-6">
                    <label class="">' . _("Email") . '</label>
                    <input type="email" id="email" name="comments-email" class="form-control" required>
                </div>
                <div class="form-group col-md-6">
                    <label class="">' . _("Website") . '</label>
                    <input type="text" id="url" name="comments-url" class="form-control">
                </div>';
            } else {
                $html .= '<div class="form-group col-md-12"><i class="fa fa-user"></i> ' . Session::val('username') . '</div>';
            }
            $html .= '
                <div class="form-group col-md-12">
                    <label>' . _("Comments") . '</label>
                    <textarea class="form-control editor" name="comments-msg" id="message" data-blocks=\'' . json_encode(Hooks::filter('comment_allowed_blocks', ['paragraph', 'quote', 'code', 'ul', 'ol'])) . '\'></textarea>
                    <small class="input-help">allowed html tag : <code>&lt;b&gt;&lt;i&gt;&lt;ul&gt;&lt;li&gt;&lt;ol&gt;&lt;u&gt;&lt;s&gt;</code></small>
                </div>
                ';
            if (Options::v('google_captcha_enable') == 'on') {
                $html .= '<div class="form-group col-md-12">';
                $html .= Xaptcha::html();
                $html .= '</div>';
            }

            global $data;
            $current_post_id = (isset($data['posts'][0]) && is_object($data['posts'][0])) ? Typo::int($data['posts'][0]->id) : 0;

            $html .= '
                <div class="col-md-12">
                    <input type="hidden" name="addComment" value="1">
                    <input type="hidden" name="post_id" value="' . $current_post_id . '">
                    <button class="btn btn-success" type="submit" name="submitComment" value="1">
                    ' . _("Send Comments") . '
                    </button>
                    <input type="hidden" name="token" value="' . TOKEN . '">
                    <input type="hidden" name="comments-parent" id="parentComment" value="0">
                </div>
            </div>
            </form>

            </div>';
        } else {
            $html = '';
        }

        return $html;
    }

    /**
     * Processes and validates a new comment submission.
     * Checks for spam, flooding, and mandatory fields before inserting into the database.
     *
     * @param array $vars POST data containing comment details.
     * @return array Global data array updated with success/error alerts.
     */
    public static function addComment($vars)
    {
        global $data;

        unset($vars['addComment']);
        $token = Typo::cleanX($vars['token']);
        if (!isset($vars['token']) && !Token::validate($token)) {
            $alertDanger[] = _("Token not exist, or your time has expired. Please refresh your browser to get a new token.");
        }

        $msg_raw = $vars['comments-msg'] ?? '';
        $msg_clean = trim($msg_raw);
        if (empty($msg_clean) || $msg_clean == '<p><br></p>') {
            $alertDanger[] = _('Nothing to send.');
        }

        if ((!isset($vars['comments-name']) || null == $vars['comments-name']) && null === Session::val('username')) {
            $alertDanger[] = _('Who are you ?');
        }
        if ((!isset($vars['comments-email']) || null == $vars['comments-email']) && null === Session::val('username')) {
            $alertDanger[] = _('How we contact you ?');
        }
        if (self::checkSpamWord($_POST['comments-msg'], self::spamWord())) {
            $alertDanger[] = _('Spam Word Detected!!');
        }
        if (self::checkLastComment()) {
            $alertDanger[] = _('You are commenting too fast!!');
        }

        if (Xaptcha::isEnable()) {
            if (!isset($vars['g-recaptcha-response']) || $vars['g-recaptcha-response'] == '') {
                $alertDanger[] = _('Please insert the Captcha');
            }
            if (!Xaptcha::verify($vars['g-recaptcha-response'])) {
                $alertDanger[] = _('Your Captcha is not correct.');
            }
        }

        if (!isset($alertDanger)) {
            $date = date('Y-m-d H:i:s');
            $email = (null !== Session::val('username')) ? User::email(User::id(Session::val('username'))) : Typo::cleanX($vars['comments-email'] ?? '');
            $url = (null !== Session::val('username')) ? '' : Typo::cleanX($vars['comments-url'] ?? '');
            $name = (null !== Session::val('username')) ? Session::val('username') : Typo::cleanX($vars['comments-name'] ?? '');
            $comment = Typo::filterXSS(
                Typo::url2link(
                    Typo::p2br(
                        Typo::strip($vars['comments-msg'], '<br><p><pre><code><b><i><ul><li><ol><u><s>')
                    )
                )
            );
            $post_id = (isset($data['posts'][0]) && is_object($data['posts'][0])) ? Typo::int($data['posts'][0]->id) : (isset($_POST['post_id']) && is_numeric($_POST['post_id']) ? Typo::int($_POST['post_id']) : 0);
            $parent = Typo::int($vars['comments-parent']);
            $status = (null !== Session::val('username')) ? '1' : '2';
            $type = Posts::type($post_id);
            $userid = (null !== Session::val('username')) ? Session::val('username') : '';
            $insert_data = array(
                'date' => $date,
                'userid' => $userid,
                'name' => $name,
                'email' => $email,
                'url' => $url,
                'comment' => $comment,
                'post_id' => $post_id,
                'parent' => $parent,
                'status' => $status,
                'type' => $type,
                'ipaddress' => $_SERVER['REMOTE_ADDR'],
            );
            if (Query::table('comments')->insert($insert_data)) {
                $data['alertSuccess'][] = _('Comments Sent.');
                $sess = array(
                    'lastcomment' => time(),
                );
                Session::set($sess);
            } else {
                $data['alertDanger'][] = _('Trouble sending Comment. DB Error: ' . (Db::$last_error ?? 'Unknown'));
            }
        } else {
            $data['alertDanger'] = $alertDanger;
        }

        return $data;
    }

    /**
     * Recursively retrieves and renders a list of comments for a specific post.
     * Handles nested replies (threading).
     *
     * @param array $vars {
     *     @type int $post_id The ID of the post.
     *     @type int $parent  The parent comment ID for threading.
     *     @type int $max     Maximum comments to show.
     *     @type int $offset  Pagination offset.
     * }
     * @return string Rendered HTML list of comments.
     */
    public static function listC($vars)
    {
        global $data;
        $offset = Typo::int($vars['offset'] ?? 0);
        $max = Typo::int($vars['max'] ?? 10);
        $parent = Typo::int($vars['parent'] ?? 0);

        if (isset($vars['post_id'])) {
            $post_id = Typo::int($vars['post_id']);
        } else {
            $post_id = (isset($data['posts'][0]) && is_object($data['posts'][0])) ? $data['posts'][0]->id : 0;
        }
        if ($post_id == 0)
            return '';

        $cmn = Query::table('comments')
            ->where('post_id', $post_id)
            ->where('status', '1')
            ->where('parent', $parent)
            ->orderBy('date', ($parent > 0) ? 'ASC' : 'DESC')
            ->limit($max, $offset)
            ->get();

        $html = '<div class="row">';
        if (!empty($cmn)) {
            foreach ($cmn as $c => $v) {
                $url = isset($v->url) ? $v->url : '#';
                $avatar = Image::getGravatar($v->email, 60);
                $html .= '
						    <div class="d-flex">
                                <div class="flex-shrink-0">
						      <a href="' . $url . '">
						        <img class="media-object img-thumbnail" src="' . $avatar . '" alt="' . $v->name . '">
						      </a>
                              
						      <div class="text-center">
						      <a href="#commentform" onclick="setParent(' . $v->id . ')" class="badge text-bg-primary" style="width: 100%"><i class="bi bi-reply"></i> reply</a>
						      </div>
                              </div>
						    
						    <div class="flex-grow-1 ms-3">
						      <h4 class="">' . $v->name . '<small class="meta-desc float-end">' . Date::format($v->date, 'd M Y H:i:s T') . '</small></h4>
						      ' . $v->comment;
                $html .= '<div class="">&nbsp;</div>';
                $vars = array(
                    'offset' => 0,
                    'max' => 10,
                    'parent' => $v->id,
                    'post_id' => $post_id,
                );
                $html .= self::listC($vars);
                $html .= '
						      <hr />
						    </div>
                            </div>
						    
						';
            }
        }

        $html .= '
        </div>';

        return $html;
    }

    /**
     * Main entry point to display a paginated list of comments.
     *
     * @param array $vars Configuration including 'max' per page.
     * @return string Rendered HTML output including pagination.
     */
    public static function showList($vars)
    {
        if (self::isEnable()) {
            global $data;
            $html = '';
            $max = Typo::int($vars['max']);
            if (isset($_GET['paging']) && isset($_GET['comments'])) {
                $paging = Typo::int($_GET['paging']);
                $offset = ($paging - 1) * $max;
            } else {
                $paging = 1;
                $offset = 0;
            }
            $vars['offset'] = $offset;

            if (isset($vars['post_id'])) {
                $post_id = Typo::int($vars['post_id']);
            } else {
                $post_id = (isset($data['posts'][0]) && is_object($data['posts'][0])) ? $data['posts'][0]->id : 0;
            }

            $html .= self::listC($vars);
            if ($post_id == 0)
                return '';
            $where = "AND `post_id` = '{$post_id}' AND `status` = '1' AND `parent` = '0' ";
            $page = array(
                'paging' => $paging,
                'table' => 'comments',
                'where' => "`type` = 'post' " . $where,
                'max' => $max,
                'url' => (SMART_URL) ? Url::post($post_id) . '?comments=yes' : Url::post($post_id) . '&comments=yes',
                'type' => 'number',
            );
            $html .= "<div class='col-sm-12'>" . Paging::create($page) . "</div>";
        } else {
            $html = '';
        }

        return $html;
    }

    /**
     * Echos the JavaScript necessary for comment reply handling (Reply to parent).
     */
    public static function validateJsComment()
    {
        $script = "
        <script type=\"text/javascript\">
        function setParent(id) {
            var p = document.getElementById('parentComment');
            if (p) p.value = id;
            var cr = document.getElementById('cancelreply');
            if (cr) cr.innerHTML = '<a href=\"#commentform\" onclick=\"removeCancel()\" class=\"badge bg-danger text-decoration-none\" ><i class=\"bi bi-x-square\"></i> Cancel Reply</a>';
        }
        function removeCancel() {
            setParent(0);
            var cr = document.getElementById('cancelreply');
            if (cr) cr.innerHTML = '';
        }
        </script>
        ";

        echo Site::minifyJS($script);
    }

    /**
     * Sets a comment status to Published (1).
     *
     * @param int $id Comment ID.
     */
    public static function publish($id)
    {
        $id = Typo::int($id);
        Query::table('comments')
            ->where('id', $id)
            ->update(array('status' => '1'));
    }

    /**
     * Sets a comment status to Unpublished (0).
     *
     * @param int $id Comment ID.
     */
    public static function unpublish($id)
    {
        $id = Typo::int($id);
        Query::table('comments')
            ->where('id', $id)
            ->update(array('status' => '0'));
    }

    /**
     * Sets a comment status to Pending (2).
     *
     * @param int $id Comment ID.
     */
    public static function pending($id)
    {
        $id = Typo::int($id);
        Query::table('comments')
            ->where('id', $id)
            ->update(array('status' => '2'));
    }

    /**
     * Permanently deletes a comment by its ID.
     *
     * @param int $id Comment ID.
     * @return bool|string Result status or error message.
     */
    public static function delete($id)
    {
        $id = Typo::int($id);
        try {
            $del = Query::table('comments')
                ->where('id', $id)
                ->delete();
            return ($del) ? true : false;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Deletes all comments associated with a specific post.
     *
     * @param int $post_id
     * @return bool
     */
    public static function deleteWithPost($post_id)
    {
        $post_id = Typo::int($post_id);
        $var = array(
            'table' => 'comments',
            'where' => array(
                'post_id' => $post_id,
            ),
        );
        return Db::delete($var);
    }

    /**
     * Checks if any comments exist for a specific post.
     *
     * @param int $id Post ID.
     * @return bool
     */
    public static function postExist($id)
    {
        $id = Typo::int($id);
        $var = sprintf("SELECT * FROM `comments` WHERE `post_id` = '%d'", $id);
        Db::result($var);
        if (Db::$num_rows > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Scans a string for prohibited spam words.
     *
     * @param string $vars  The string to check.
     * @param array  $spams List of spam words.
     * @return bool         True if spam is detected.
     */
    public static function checkSpamWord($vars, $spams)
    {
        $spam = false;
        if (is_array($spams)) {
            foreach ($spams as $s) {
                $s = trim($s);
                if (empty($s))
                    continue; // Prevent PHP 8 empty needle returning 0
                if (stripos($vars, $s) !== false) {
                    $spam = true;
                    break;
                }
            }
        }

        return $spam;
    }

    /**
     * Prevents comment flooding by checking the time since the last comment from the session.
     *
     * @param int $delay Allowed delay in seconds (default 60).
     * @return bool      True if the user is commenting too fast.
     */
    public static function checkLastComment($delay = '60')
    {
        $last = Session::val('lastcomment');

        if ($last != '') {
            $now = time();

            $spare = $now - $last;

            if ($spare > $delay) {
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    /**
     * Checks if the comment system is globally enabled in settings.
     *
     * @return bool
     */
    public static function isEnable()
    {
        $enable = Options::v('comments_enable');
        if ($enable == 'on') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * $vars = array(
     *     'type'    => '',
     *     'num'     => '',
     *     'post_id' => ''
     * );
     *
     */

    /**
     * Retrieves a list of recent comments across the site.
     *
     * @param array|string $vars Optional configuration (num, type, post_id).
     * @return string      Rendered HTML list of recent comments.
     */
    public static function recent($vars = '')
    {
        $postID = isset($vars['post_id']) ? " AND `post_id` = '" . Typo::int($vars['post_id']) . "'" : '';
        $type = isset($vars['type']) ? Typo::cleanX($vars['type']) : 'post';
        $num = isset($vars['num']) ? Typo::int($vars['num']) : '10';
        $sql = "SELECT * FROM `comments`
                WHERE `type` = '{$type}' {$postID} AND `status` = '1'
                ORDER BY `date` DESC LIMIT {$num}";
        $comments = Db::result($sql);

        if (isset($comments['error'])) {
            $html = _('No Comments found.');
        } else {
            $html = "<ol class='list-unstyled'>";
            foreach ($comments as $key => $value) {
                $comment = substr(Typo::strip($value->comment), 0, 30);
                $author = !empty($value->userid) ? $value->userid : $value->name;
                $date = Date::format($value->date, "d M Y");
                $html .= "<li><a href='" . Url::$type($value->post_id) . "'>{$comment}</a> <br/><small>&nbsp;<i>by {$author} on {$date}</i></small></li>";
            }
            $html .= "</ol>";
        }

        return $html;
    }

    /**
     * Compiles a comprehensive list of spam words from hardcoded data and database settings.
     *
     * @return array Unified list of spam words.
     */
    public static function spamWord()
    {
        $badWord = array(
            'Reverses aging',
            '\'Hidden\' assets',
            'stop snoring',
            'Free investment',
            'Dig up dirt on friends',
            'Stock disclaimer statement',
            'Multi level marketing',
            'Compare rates',
            'Cable converter',
            'Claims you can be removed from the list',
            'Removes wrinkles',
            'Compete for your business',
            'Free grant money',
            'Auto email removal',
            'Collect child support',
            'Amazing stuff',
            'Tells you it’s an ad',
            'Claims to be in accordance with some spam law',
            'Search engine listings',
            'Credit bureaus',
            'No investment',
            'Serious cash',
            'Act Now!',
            'Affordable',
            'Apply Online',
            'Auto email removal',
            'Best price',
            'Big bucks',
            'Billion dollars',
            'Brand new pager',
            'Call free',
            'Call now',
            'Cancel at any time',
            'Cannot be combined with any other offer',
            'Cards accepted',
            'Cash bonus',
            'Casino',
            'Cents on the dollar',
            'Check or money order',
            'Click here',
            'Click to remove',
            'Confidentially on all orders',
            'Congratulations',
            'Consolidate debt and credit',
            'Consolidate your debt',
            'Copy accurately',
            'Copy DVDs',
            'Credit bureaus',
            'Credit card offers',
            'Cures baldness',
            'Diagnostics',
            'Dig up dirt on friends',
            'Direct email',
            'Direct marketing',
            'Do it today',
            'Don’t delete',
            'Don’t hesitate',
            'Double your',
            'Drastically reduced',
            'Earn $',
            'Earn extra cash',
            'Earn per week',
            'Easy terms',
            'Eliminate bad credit',
            'Eliminate debt',
            'Email harvest',
            'Email marketing',
            'Expect to earn',
            'Explode your business',
            'Extra income',
            'F r e e',
            'Fantastic deal',
            'Fast cash',
            'Financial freedom',
            'Financially independent',
            'For instant access',
            'For just $',
            'For Only',
            'For you',
            'Full refund',
            'Get it now',
            'Get out of debt',
            'Get paid',
            'Get started now',
            'Give it away',
            'Giving away',
            'Great offer',
            'Have you been turned down?',
            'Hidden assets',
            'Hidden charges',
            'Home based',
            'Homebased business',
            'Income from home',
            'Incredible deal',
            'Info you requested',
            'Information you requested',
            'Investment decision',
            'It’s effective',
            'Join millions',
            'Join millions of Americans',
            'Laser printer',
            'Lose weight',
            'Lose weight spam',
            'Lower interest rate',
            'Lower monthly payment',
            'Lower your mortgage rate',
            'Lowest insurance rates',
            'Lowest price',
            'Lotto',
            'Luxury car',
            'Mail in order form',
            'Maintained',
            'Make $',
            'Make money',
            'Marketing solutions',
            'Meet singles',
            'Message contains',
            'Million dollars',
            'Mortgage rates',
            'New customers only',
            'No age restrictions',
            'No catch',
            'No claim forms',
            'No credit check',
            'No disappointment',
            'No experience',
            'No fees',
            'No gimmick',
            'No hidden',
            'No inventory',
            'No obligation',
            'Not intended',
            'Notspam',
            'Offer expires',
            'Once in lifetime',
            'One hundred percent free',
            'One hundred percent guaranteed',
            'One time mailing',
            'Online degree',
            'Online marketing',
            'Online pharmacy',
            'Order today',
            'Orders shipped by',
            'Outstanding values',
            'Pennies a day',
            'Per day',
            'Per week',
            'Please read',
            'Potential earnings',
            'Pre-approved',
            'Print out and fax',
            'Priority mail',
            'Produced and sent out',
            'Promise you',
            'Refinance home',
            'Satisfaction guaranteed',
            'Search engines',
            'Shopping spree',
            'Sign up free today',
            'Special promotion',
            'Stainless steel',
            'Stock alert',
            'Stock disclaimer statement',
            'Stock pick',
            'Stop snoring',
            'Terms and conditions',
            'The best rates',
            'The following form',
            'They keep your money — no refund!',
            'They\'re just giving it away',
            'This isn\'t junk',
            'This isn\'t spam',
            'Time limited',
            'Undisclosed recipient',
            'University diplomas',
            'Unsecured credit',
            'Unsecured debt',
            'Unsolicited',
            'Vacation offers',
            'Valium',
            'Viagra',
            'v i a g r a',
            'v1agra',
            'Vicodin',
            'Visit our website',
            'We hate spam',
            'We honor all',
            'Web traffic',
            'Weekend getaway',
            'Weight loss',
            'What are you waiting for?',
            'While supplies last',
            'While you sleep',
            'Who really wins?',
            'Why pay more?',
            'Will not believe your eyes',
            'Work at home',
            'Work from home',
            'Xanax',
            'You are a winner!',
            'You have been selected',
            'You’re a Winner!',
            'As seen on',
            'Buy direct',
            'Buying judgments',
            'Order status',
            'Orders shipped by',
            'Dig up dirt on friends',
            'Meet singles',
            'Score with babes',
            'Additional Income',
            'Be your own boss',
            'Compete for your business',
            'Double your',
            'Earn $',
            'Earn extra cash',
            'Expect to earn ',
            'Extra income',
            'Home based',
            'Home employment',
            'Homebased business',
            'Income from home',
            'Make $',
            'Make money',
            'Money making',
            'Online biz opportunity',
            'Online degree',
            'Opportunity ',
            'Potential earnings ',
            'University diplomas',
            'While you sleep ',
            'Work at home ',
            'Work from home',
            'Beneficiary',
            'Best price',
            'Big bucks ',
            'Cash bonus ',
            'Cashcashcash',
            'Cents on the dollar ',
            'Compare rates',
            'Credit bureaus ',
            'Easy terms ',
            'F r e e',
            'Fast cash',
            'For just $XXX',
            'Hidden assets ',
            'hidden charges',
            'Incredible deal',
            'Lowest price ',
            'Million dollars',
            'Money back',
            'Mortgage rates ',
            'One hundred percent free ',
            'Only $ ',
            'Pennies a day ',
            'Pure profit',
            'Save $',
            'Save big money',
            'Save up to',
            'Serious cash',
            'Subject to credit',
            'They keep your money -- no refund! ',
            'Unsecured credit',
            'Unsecured debt',
            'Why pay more? ',
            'Accept Credit Cards',
            'Cards accepted',
            'Check or money order ',
            'Credit card offers ',
            'Explode your business',
            'Full refund ',
            'Investment decision ',
            'No credit check ',
            'No hidden Costs',
            'No investment',
            'Requires initial investment',
            'Sent in compliance',
            'Stock alert ',
            'Stock disclaimer statement ',
            'Stock pick ',
            'Avoid bankruptcy',
            'Calling creditors',
            'Collect child support',
            'Consolidate debt and credit',
            'Consolidate your debt',
            'Eliminate bad credit ',
            'Eliminate debt',
            'Financially independent',
            'Get out of debt',
            'Get paid ',
            'Lower interest rate',
            'Lower monthly payment ',
            'Lower your mortgage rate',
            'Lowest insurance rates',
            'Pre-approved',
            'Refinance home ',
            'Social security number ',
            'Auto email removal ',
            'Click below',
            'Click here',
            'Click to remove ',
            'Direct email ',
            'Direct marketing ',
            'Email harvest ',
            'Email marketing ',
            'Increase sales ',
            'Increase traffic ',
            'Increase your sales',
            'Internet market',
            'Internet marketing',
            'Marketing solutions ',
            'Month trial offer',
            'More Internet Traffic',
            'Multi level marketing',
            'One time mailing ',
            'Online marketing',
            'Removal instructions',
            'Search engine listings',
            'Search engines',
            'The following form',
            't junk ',
            't spam ',
            'Visit our website',
            'We hate spam ',
            'Web traffic',
            'Will not believe your eyes ',
            'Cures baldness ',
            'Diagnostics',
            'Fast Viagra delivery',
            'Human growth hormone',
            'Life Insurance',
            'Lose weight',
            'Lose weight spam ',
            'No medical exams',
            'Online pharmacy ',
            'Removes wrinkles',
            'Reverses aging',
            'Stop snoring ',
            'Weight loss',
            '#1',
            '100% free',
            '100% Satisfied',
            '4U',
            '50% off',
            'Billion dollars ',
            'Join millions',
            'Join millions of Americans ',
            'One hundred percent guaranteed ',
            'Being a member',
            'Billing address ',
            'Cannot be combined with any other offer ',
            'Confidentially on all orders ',
            'Financial freedom ',
            'Gift certificate',
            'Giving away',
            'Have you been turned down? ',
            'If only it were that easy',
            'Important information regarding',
            'In accordance with laws',
            'Long distance phone offer',
            'Mail in order form ',
            'Message contains',
            'Reserves the right',
            'Stuff on sale',
            'They’re just giving it away',
            'You are a winner!',
            'You have been selected ',
            'Print form signature',
            'See for yourself',
            'Free cell phone ',
            'Free consultation ',
            'Free DVD ',
            'Free gift',
            'Free grant money',
            'Free hosting',
            'Free installation ',
            'Free Instant',
            'Free investment ',
            'Free leads ',
            'Free membership ',
            'Free money ',
            'Free offer ',
            'Free preview ',
            'Free priority mail ',
            'Free quote ',
            'Free sample ',
            'Free trial ',
            'Free website ',
            'All natural',
            'All new',
            'Risk free',
            'limited time',
            'Order now',
            'Supplies are limited',
            'Take action now',
            'Addresses on CD',
            'New domain extensions',
        );

        $optSpamword = Options::v('spamwords');

        $spam = explode(PHP_EOL, $optSpamword);
        $result = array_merge($badWord, $spam);

        // print_r($result);

        return $result;
    }
}
