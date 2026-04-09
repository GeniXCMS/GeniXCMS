<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');

/**
 * Newsletter Module - Core Library
 * Manages subscribers, campaigns, templates, and send logs.
 */
class Newsletter
{
    // -----------------------------------------------------------------------
    // INSTALLER
    // -----------------------------------------------------------------------

    /**
     * Create required tables if they do not exist.
     */
    public static function install(): void
    {
        Db::connect(); // ensure connection is established
        $pdo = Db::$pdo;

        $pdo->exec("CREATE TABLE IF NOT EXISTS `newsletter_subscribers` (
            `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `email`      VARCHAR(191) NOT NULL,
            `name`       VARCHAR(100) DEFAULT '',
            `status`     TINYINT(1) DEFAULT 1 COMMENT '1=active,0=unsubscribed',
            `token`      VARCHAR(64)  DEFAULT NULL COMMENT 'unsubscribe token',
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY `uq_email` (`email`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        $pdo->exec("CREATE TABLE IF NOT EXISTS `newsletter_campaigns` (
            `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `subject`    VARCHAR(255) NOT NULL,
            `body`       LONGTEXT NOT NULL,
            `type`       ENUM('html','text') NOT NULL DEFAULT 'html',
            `recipient`  VARCHAR(50) NOT NULL DEFAULT 'all' COMMENT 'all|subscribers|group_0|group_4',
            `status`     ENUM('draft','sending','sent') NOT NULL DEFAULT 'draft',
            `sent_count` INT UNSIGNED NOT NULL DEFAULT 0,
            `fail_count` INT UNSIGNED NOT NULL DEFAULT 0,
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `sent_at`    DATETIME DEFAULT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        $pdo->exec("CREATE TABLE IF NOT EXISTS `newsletter_logs` (
            `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `campaign_id` INT UNSIGNED NOT NULL,
            `email`       VARCHAR(191) NOT NULL,
            `status`      ENUM('sent','failed') NOT NULL DEFAULT 'sent',
            `error`       TEXT DEFAULT NULL,
            `sent_at`     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            KEY `idx_campaign` (`campaign_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    }

    // -----------------------------------------------------------------------
    // SUBSCRIBERS
    // -----------------------------------------------------------------------

    public static function subscriberList(int $offset = 0, int $limit = 20, string $search = ''): array
    {
        $q = Query::table('newsletter_subscribers')->orderBy('created_at', 'DESC')->limit($limit, $offset);
        if ($search !== '') {
            $q->whereRaw('(`email` LIKE ? OR `name` LIKE ?)', ["%{$search}%", "%{$search}%"]);
        }
        return $q->get();
    }

    public static function subscriberCount(string $search = ''): int
    {
        $q = Query::table('newsletter_subscribers');
        if ($search !== '') {
            $q->whereRaw('(`email` LIKE ? OR `name` LIKE ?)', ["%{$search}%", "%{$search}%"]);
        }
        return $q->count();
    }

    public static function subscriberAdd(string $email, string $name = ''): bool
    {
        $email = strtolower(trim($email));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return false;
        $exists = Query::table('newsletter_subscribers')->where('email', $email)->count();
        if ($exists) return false;
        $token = bin2hex(random_bytes(16));
        Query::table('newsletter_subscribers')->insert([
            'email'      => $email,
            'name'       => $name,
            'status'     => 1,
            'token'      => $token,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        return true;
    }

    public static function subscriberDelete(int $id): void
    {
        Query::table('newsletter_subscribers')->where('id', $id)->delete();
    }

    public static function subscriberToggle(int $id): void
    {
        $sub = Query::table('newsletter_subscribers')->where('id', $id)->first();
        if ($sub) {
            $newStatus = $sub->status == 1 ? 0 : 1;
            Query::table('newsletter_subscribers')->where('id', $id)->update(['status' => $newStatus]);
        }
    }

    public static function importSubscribers(string $csv): array
    {
        $lines  = preg_split('/\r\n|\r|\n/', trim($csv));
        $added  = 0;
        $errors = [];
        foreach ($lines as $line) {
            $parts = str_getcsv($line);
            $email = trim($parts[0] ?? '');
            $name  = trim($parts[1] ?? '');
            if ($email === '') continue;
            if (self::subscriberAdd($email, $name)) {
                $added++;
            } else {
                $errors[] = $email;
            }
        }
        return ['added' => $added, 'errors' => $errors];
    }

    // -----------------------------------------------------------------------
    // CAMPAIGNS
    // -----------------------------------------------------------------------

    public static function campaignList(int $offset = 0, int $limit = 20): array
    {
        return Query::table('newsletter_campaigns')->orderBy('created_at', 'DESC')->limit($limit, $offset)->get();
    }

    public static function campaignCount(): int
    {
        return Query::table('newsletter_campaigns')->count();
    }

    public static function campaignGet(int $id): ?object
    {
        return Query::table('newsletter_campaigns')->where('id', $id)->first();
    }

    public static function campaignSave(array $data): int
    {
        Query::table('newsletter_campaigns')->insert([
            'subject'    => $data['subject'],
            'body'       => $data['body'],
            'type'       => $data['type'] ?? 'html',
            'recipient'  => $data['recipient'] ?? 'all',
            'status'     => 'draft',
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        return (int) Db::$last_id;
    }

    public static function campaignUpdate(int $id, array $data): void
    {
        Query::table('newsletter_campaigns')->where('id', $id)->update([
            'subject'   => $data['subject'],
            'body'      => $data['body'],
            'type'      => $data['type'] ?? 'html',
            'recipient' => $data['recipient'] ?? 'all',
        ]);
    }

    public static function campaignDelete(int $id): void
    {
        Query::table('newsletter_campaigns')->where('id', $id)->delete();
        Query::table('newsletter_logs')->where('campaign_id', $id)->delete();
    }

    // -----------------------------------------------------------------------
    // SENDING
    // -----------------------------------------------------------------------

    public static function getRecipientsForCampaign(string $recipient): array
    {
        $recipients = [];

        // Site members
        if ($recipient === 'all' || str_starts_with($recipient, 'group_')) {
            $q = Query::table('user');
            if (str_starts_with($recipient, 'group_')) {
                $group = (int) substr($recipient, 6);
                $q->where('group', $group);
            }
            foreach ($q->get() as $u) {
                if (!is_object($u)) continue;
                // create a temporary pseudo-token for users for unsubscription logic or just a hash
                $token = md5($u->email . $u->pass);
                $recipients[] = ['email' => $u->email, 'name' => $u->userid, 'token' => $token];
            }
        }

        // Newsletter subscribers
        if ($recipient === 'all' || $recipient === 'subscribers') {
            foreach (Query::table('newsletter_subscribers')->where('status', 1)->get() as $s) {
                if (!is_object($s)) continue;
                // avoid duplicate emails already added from users
                $exists = false;
                foreach ($recipients as $r) {
                    if (strtolower($r['email']) === strtolower($s->email)) { $exists = true; break; }
                }
                if (!$exists) {
                    $recipients[] = ['email' => $s->email, 'name' => $s->name ?: $s->email, 'token' => $s->token];
                }
            }
        }

        return $recipients;
    }

    public static function sendCampaign(int $campaignId): array
    {
        $campaign = self::campaignGet($campaignId);
        if (!$campaign) return ['sent' => 0, 'failed' => 0, 'errors' => ['Campaign not found']];

        // Mark as sending
        Query::table('newsletter_campaigns')->where('id', $campaignId)->update(['status' => 'sending']);

        $recipients = self::getRecipientsForCampaign($campaign->recipient);
        $sent = 0; $failed = 0; $errors = [];

        $baseMsg = $campaign->body;
        $baseMsg = str_replace('{{sitename}}', Site::$name, $baseMsg);
        $baseMsg = str_replace('{{siteurl}}',  Site::$url,  $baseMsg);
        $baseMsg = str_replace('{{sitemail}}', Site::$email, $baseMsg);

        foreach ($recipients as $r) {
            $msg = str_replace('{{name}}',   $r['name'],  $baseMsg);
            $msg = str_replace('{{email}}',  $r['email'], $msg);
            
            $unsubUrl = Site::$url . '/?newsletter_unsubscribe=' . urlencode($r['email']) . '&token=' . $r['token'];
            $msg = str_replace('{{unsubscribe}}', $unsubUrl, $msg);

            if ($campaign->type === 'text') {
                $msg = strip_tags(str_replace(['<br>', '</p><p>', '&nbsp;'], ["\r\n", "\r\n", ' '], $msg));
            }

            $result = Mail::send([
                'to'      => $r['email'],
                'to_name' => $r['name'],
                'subject' => $campaign->subject,
                'message' => $msg,
                'msgtype' => $campaign->type,
            ]);

            if ($result === null || $result === '') {
                $sent++;
                Query::table('newsletter_logs')->insert([
                    'campaign_id' => $campaignId,
                    'email'       => $r['email'],
                    'status'      => 'sent',
                    'sent_at'     => date('Y-m-d H:i:s'),
                ]);
            } else {
                $failed++;
                $errors[] = $r['email'] . ': ' . $result;
                Query::table('newsletter_logs')->insert([
                    'campaign_id' => $campaignId,
                    'email'       => $r['email'],
                    'status'      => 'failed',
                    'error'       => $result,
                    'sent_at'     => date('Y-m-d H:i:s'),
                ]);
            }
            usleep(200000); // 200ms throttle
        }

        // Mark as sent
        Query::table('newsletter_campaigns')->where('id', $campaignId)->update([
            'status'     => 'sent',
            'sent_count' => $sent,
            'fail_count' => $failed,
            'sent_at'    => date('Y-m-d H:i:s'),
        ]);

        return ['sent' => $sent, 'failed' => $failed, 'errors' => $errors];
    }

    // -----------------------------------------------------------------------
    // LOGS
    // -----------------------------------------------------------------------

    public static function campaignLogs(int $campaignId): array
    {
        return Query::table('newsletter_logs')->where('campaign_id', $campaignId)->orderBy('sent_at', 'DESC')->get();
    }

    // -----------------------------------------------------------------------
    // STATS
    // -----------------------------------------------------------------------

    public static function stats(): array
    {
        return [
            'total_subscribers'  => Query::table('newsletter_subscribers')->count(),
            'active_subscribers' => Query::table('newsletter_subscribers')->where('status', 1)->count(),
            'total_campaigns'    => Query::table('newsletter_campaigns')->count(),
            'sent_campaigns'     => Query::table('newsletter_campaigns')->where('status', 'sent')->count(),
        ];
    }
}
