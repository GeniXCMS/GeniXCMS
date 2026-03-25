<?php


class Archives
{

    public function __construct()
    {
        self::generate();
    }

    public static function list($max,$type = 'post') 
    {
        $dates = Typo::Xclean(Options::v('archives_list'));
        $dates = json_decode($dates, true);
        
        $html = "<ul class='list-unstyled mb-0'>";        
        foreach( $dates[$type] as $k => $v ) {
            // print_r($k);
            foreach( $v as $k2 => $v2 ) {
                $monthName = Date::monthName($v2);
                $html .= "<li><a href=\"".Url::archive($v2, $k)."\">{$monthName} {$k}</a></li>";
            }
            
        }
        $html .= "</ul>";

        return $html;
    }

    public static function validate($month, $year)
    {
        $date = $year."-".$month;
        $q = Db::result("SELECT `id` FROM `posts` WHERE `date` LIKE '%{$date}%' ");
        if( Db::$num_rows > 0 ) {
            return true;
        } else {
            return false;
        }
    }

    public static function generate($type = 'post')
    {
        if( TRUE === Options::isExist('archives_last_update') ) {
            $last_update = Options::v('archives_last_update');
            $last_update = $last_update != "" ? $last_update: 0;
            $now = time();
            $margin = $now - $last_update;
            if( $margin > 3600*24 ) {
                self::doUpdate($type);
            }
        } else {
            self::optUpdate($type);
        }
    }

    public static function doUpdate($type)
    {
        $data = self::fetchData($type);
        self::optUpdate($data);

    }

    public static function fetchData($type)
    {
        $archives = Db::result("SELECT Month(`date`) as `month`, Year(`date`) as `year` 
        FROM `posts` WHERE `type` = '{$type}' GROUP BY Month(`date`), Year(`date`) 
        ORDER BY Year(`date`) DESC, Month(`date`) DESC ");
        $year = date("Y");
        $archive_dates[$type][$year] = [];
        foreach($archives as $k => $v) {
            $archive_dates[$type][$v->year] = [$v->month];
        }
        return json_encode($archive_dates);
    }

    

    public static function optUpdate($data)
    {
        if( TRUE === Options::isExist('archives_list')) {
            $archives_list = Options::v('archives_list');
            $arch_db = json_decode(Typo::Xclean($archives_list), true);
            $arch_res = json_decode($data, true);
            $arch_res = is_array($arch_db) ? array_merge($arch_db, $arch_res): $arch_res;
            $arch_fin = json_encode($arch_res);
            $time = time();

            Options::update('archives_list', $arch_fin);
            Options::update('archives_last_update', $time);

        } else {
            $arch = json_encode($data);
            $time = time();
            Options::insert([ 
                'archives_list' => $arch, 
                'archives_last_update' => $time
            ]);
        }
    }
}
