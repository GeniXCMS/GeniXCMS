<?php

class Infograph
{
    public function __construct()
    {
        Hooks::attach('post_content_before_action', array('Infograph', 'display'));
        Hooks::attach('init', array('Infograph', 'renderParam'));

        AdminMenu::add([
            'id'       => 'infograph',
            'label'    => _('Infograph'),
            'icon'     => 'bi bi-geo-alt',
            'url'      => 'index.php?page=mods&mod=infograph',
            'access'   => 1,
            'position' => 'external',
            'order'    => 20,
        ]);
    }
    public static function show()
    {
        echo 'Infograph Show';
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
        if ($data[0]['mod'] == 'infograph') {

            Mod::inc('frontpage', $data, realpath(__DIR__.'/../layout/'));
        }
    }

    public static function renderParam()
    {
        $vars['bottom'][] = [
            'grouptitle' => 'Infograph',
            'groupname' => 'infograph',
            'fields' => [
                [
                    'title' => 'Show Infograph?',
                    'name' => 'show_infograph',
                    'boxclass' => 'col col-3 mb-2 mt-2',
                    'type' => 'dropdown',
                    'value' => ['no','yes']
                ],
                [
                    'title' => 'Theme Layout',
                    'name' => 'layout_theme',
                    'boxclass' => 'col col-3 mb-2 mt-2',
                    'type' => 'dropdown',
                    'value' => ['Modern Card', 'Classic Compact', 'Minimalist List']
                ],
                [
                    'title' => 'Place Type',
                    'name' => 'place_type',
                    'boxclass' => 'col col-3 mb-2 mt-2',
                    'type' => 'dropdown',
                    'value' => ['Restaurant', 'Hospital', 'School', 'Campus', 'Park', 'Mosque', 'Church', 'Shrine', 'Public Market', 'Office', 
                    'Lake', 'Pet Care', 'Store', 'Veterinary', 'Temple', 'Monument', 'Heritage Site', 'Cemetery', 'Stadium', 'Sport Arena', 
                    'Gymnasium', 'Athletic Track']
                ],
                [
                    'title' => 'Rating Access',
                    'name' => 'rating_access',
                    'boxclass' => 'col col-3 mb-2 mt-2',
                    'type' => 'dropdown',
                    'value' => ['0', '1', '2', '3', '4', '5']
                ],
                [
                    'title' => 'Rating Comfort',
                    'name' => 'rating_comfort',
                    'boxclass' => 'col col-3 mb-2 mt-2',
                    'type' => 'dropdown',
                    'value' => ['0', '1', '2', '3', '4', '5']
                ],
                [
                    'title' => 'Rating Foody',
                    'name' => 'rating_foody',
                    'boxclass' => 'col col-3 mb-2 mt-2',
                    'type' => 'dropdown',
                    'value' => ['0', '1', '2', '3', '4', '5']
                ],
                [
                    'title' => 'Address',
                    'name' => 'address_location',
                    'boxclass' => 'col col-12 mb-2 mt-2',
                    'type' => 'textarea',
                    'value' => ''
                ],
                [
                    'title' => 'Map Latitude',
                    'name' => 'map_latitude',
                    'boxclass' => 'col col-12 mb-2 mt-2',
                    'type' => 'text',
                    'value' => ''
                ],
                [
                    'title' => 'Map Longitude',
                    'name' => 'map_longitude',
                    'boxclass' => 'col col-12 mb-2 mt-2',
                    'type' => 'text',
                    'value' => ''
                ],
                [
                    'title' => 'Facility Wifi',
                    'name' => 'facility_wifi',
                    'boxclass' => 'col col-2 mb-2 mt-2',
                    'type' => 'dropdown',
                    'value' => ['no','yes']
                ],
                [
                    'title' => 'Prayer Room',
                    'name' => 'facility_prayer_room',
                    'boxclass' => 'col col-2 mb-2 mt-2',
                    'type' => 'dropdown',
                    'value' => ['no','yes']
                ],
                [
                    'title' => 'Toilet',
                    'name' => 'facility_toilet',
                    'boxclass' => 'col col-2 mb-2 mt-2',
                    'type' => 'dropdown',
                    'value' => ['no','yes']
                ],
                [
                    'title' => 'Wheelchair',
                    'name' => 'facility_wheelchair',
                    'boxclass' => 'col col-2 mb-2 mt-2',
                    'type' => 'dropdown',
                    'value' => ['no','yes']
                ],
                [
                    'title' => 'Amenities',
                    'name' => 'facility_amenities',
                    'boxclass' => 'col col-2 mb-2 mt-2',
                    'type' => 'dropdown',
                    'value' => ['no','yes']
                ],
                [
                    'title' => 'Electricity',
                    'name' => 'facility_electricity',
                    'boxclass' => 'col col-2 mb-2 mt-2',
                    'type' => 'dropdown',
                    'value' => ['no','yes']
                ],
                [
                    'title' => 'Music',
                    'name' => 'facility_music',
                    'boxclass' => 'col col-2 mb-2 mt-2',
                    'type' => 'dropdown',
                    'value' => ['no','yes']
                ],
                [
                    'title' => 'Entertainment',
                    'name' => 'facility_entertainment',
                    'boxclass' => 'col col-2 mb-2 mt-2',
                    'type' => 'dropdown',
                    'value' => ['no','yes']
                ],
                [
                    'title' => 'Playground',
                    'name' => 'facility_playground',
                    'boxclass' => 'col col-2 mb-2 mt-2',
                    'type' => 'dropdown',
                    'value' => ['no','yes']
                ],
                [
                    'title' => 'Parking Lot',
                    'name' => 'facility_parking',
                    'boxclass' => 'col col-2 mb-2 mt-2',
                    'type' => 'dropdown',
                    'value' => ['no','yes']
                ],
            ]
        ];

        Params::register($vars);
    }

    public static function display($data)
    {
        // global $data;
        // print_r($data);
        $p = $data[0]['posts'][0];
        if( isset($p->show_infograph) && $p->show_infograph == "yes" ) {
   
            $rating_foody = $p->rating_foody ?? 0;
            $rating_comfort = $p->rating_comfort ?? 0;
            $rating_access = $p->rating_access ?? 0;

            $rating = round( ( $rating_foody + $rating_comfort + $rating_access ) /3 );
            $stars = "";
            for($i=0;$i<$rating; $i++) {
                $stars .= "<i class=\"fa-solid fa-star\"></i>";
            }
            $ratingAccess = "";
            for($i=0;$i<$rating_access; $i++) {
                $ratingAccess .= "<i class=\"fa-solid fa-star text-warning\"></i>";
            }
            for($i=0;$i<5-$rating_access; $i++) {
                $ratingAccess .= "<i class=\"fa-regular fa-star text-warning\"></i>";
            }
            $ratingComfort = "";
            for($i=0;$i<$rating_comfort; $i++) {
                $ratingComfort .= "<i class=\"fa-solid fa-star text-warning\"></i>";
            }
            for($i=0;$i<5-$rating_comfort; $i++) {
                $ratingComfort .= "<i class=\"fa-regular fa-star text-warning\"></i>";
            }
            $ratingFoody = "";
            for($i=0;$i<$rating_foody; $i++) {
                $ratingFoody .= "<i class=\"fa-solid fa-star text-warning\"></i>";
            }
            for($i=0;$i<(5-$rating_foody); $i++) {
                $ratingFoody .= "<i class=\"fa-regular fa-star text-warning\"></i>";
            }
            
            $map_latitude = $p->map_latitude ?? 0;
            $map_longitude = $p->map_longitude ?? 0;
            $mapLocation = "https://maps.google.com/maps?q=".$map_latitude.",".$map_longitude."&hl={$data[0]['website_lang']}&z=14&amp;output=embed";

            $facWifi = isset($p->facility_wifi) && $p->facility_wifi == "yes" ? " <i class=\"fa fa-wifi text-success\" title=\"Wifi\"></i>&nbsp; ":"";
            $facToilet = isset($p->facility_toilet) && $p->facility_toilet == "yes" ? " <i class=\"fa fa-toilet text-success\" title=\"Toilet/WC\"></i>&nbsp; ":"";
            $facPrayer = isset($p->facility_prayer_room) && $p->facility_prayer_room == "yes" ? " <i class=\"fa fa-mosque text-success\" title=\"Muslim Prayer Room\"></i>&nbsp; ":"";
            $facWheel = isset($p->facility_wheelchair) && $p->facility_wheelchair == "yes" ? " <i class=\"fa fa-wheelchair text-success\" title=\"Wheelchair Friendly\"></i>&nbsp; ":"";
            $facAmenity = isset($p->facility_amenities) && $p->facility_amenities == "yes" ? " <i class=\"fa-solid fa-pump-soap text-success\" title=\"Amenities Ready\"></i>&nbsp; ":"";
            $facElectric = isset($p->facility_electricity) && $p->facility_electricity == "yes" ? " <i class=\"fa-solid fa-plug text-success\" title=\"Electricity Ready\"></i>&nbsp; ":"";
            $facMusic = isset($p->facility_music) && $p->facility_music == "yes" ? "<i class=\" fa fa-music text-success\" title=\"Music/Audio Ready\"></i>&nbsp; ":"";
            $facEntertain = isset($p->facility_entertainment) && $p->facility_entertainment == "yes" ? " <i class=\"fa fa-guitar text-success\" title=\"Entertainment Ready\"></i>&nbsp; ":"";
            $facPlayground = isset($p->facility_playground) && $p->facility_playground == "yes" ? " <i class=\"fa fa-child-reaching text-success\" title=\"Children Playground Ready\"></i>&nbsp; ":"";
            $facParking = isset($p->facility_parking) && $p->facility_parking == "yes" ? " <i class=\"fa-solid fa-square-parking text-success\" title=\"Parking Facility\"></i>&nbsp; ":"";

            $ratingColor = $rating < 3 ? "#ef4444" : ($rating > 3 ? "#10b981" : "#f59e0b");
            $ratingBg = $rating < 3 ? "rgba(239, 68, 68, 0.1)" : ($rating > 3 ? "rgba(16, 185, 129, 0.1)" : "rgba(245, 158, 11, 0.1)");
            
            // Build facilities as nice pills
            $facList = [];
            $facDefs = [
                'facility_wifi' => ['icon' => 'fa-wifi', 'label' => 'WiFi'],
                'facility_toilet' => ['icon' => 'fa-toilet', 'label' => 'Toilet/WC'],
                'facility_prayer_room' => ['icon' => 'fa-mosque', 'label' => 'Prayer Room'],
                'facility_wheelchair' => ['icon' => 'fa-wheelchair', 'label' => 'Wheelchair'],
                'facility_amenities' => ['icon' => 'fa-solid fa-pump-soap', 'label' => 'Amenities'],
                'facility_electricity' => ['icon' => 'fa-solid fa-plug', 'label' => 'Electricity'],
                'facility_music' => ['icon' => 'fa-music', 'label' => 'Music/Audio'],
                'facility_entertainment' => ['icon' => 'fa-guitar', 'label' => 'Entertainment'],
                'facility_playground' => ['icon' => 'fa-child-reaching', 'label' => 'Playground'],
                'facility_parking' => ['icon' => 'fa-solid fa-square-parking', 'label' => 'Parking'],
            ];

            foreach ($facDefs as $fk => $fd) {
                if (isset($p->$fk) && $p->$fk == 'yes') {
                    $facList[] = '<span style="display:inline-flex;align-items:center;gap:6px;padding:6px 12px;background:#f1f5f9;color:#475569;border-radius:50px;font-size:12px;font-weight:700;"><i class="'.$fd['icon'].'" style="color:#2563eb;"></i> '.$fd['label'].'</span>';
                }
            }
            $facText = empty($facList) ? '<span style="color:#94a3b8;font-size:13px;font-style:italic;">No facilities listed</span>' : implode(' ', $facList);

            $themeSelection = $p->layout_theme ?? 'Modern Card';
            
            if ($themeSelection == 'Classic Compact') {
                $html = '
                <div style="border:1px solid #cbd5e1;background:#f8fafc;padding:20px;margin-bottom:30px;font-family:\'Outfit\',sans-serif;color:#334155;">
                    <div style="border-bottom:2px solid #e2e8f0;padding-bottom:15px;margin-bottom:15px;display:flex;justify-content:space-between;align-items:center;">
                        <div>
                            <span style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:1px;">'.($p->place_type ?? 'Place').'</span>
                            <h3 style="margin:0;font-size:22px;font-weight:700;color:#0f172a;">'.($p->title ?? 'Infograph').'</h3>
                        </div>
                        <div style="text-align:right;">
                            <div style="font-size:26px;font-weight:800;color:'.$ratingColor.';line-height:1;">'.$rating.' <small style="font-size:14px;color:#94a3b8;">/ 5</small></div>
                            <div style="color:#f59e0b;font-size:14px;">'.$stars.'</div>
                        </div>
                    </div>
                    
                    <div style="display:flex;flex-wrap:wrap;gap:20px;">
                        <div style="flex:1;min-width:250px;">
                            <p style="margin:0 0 15px;font-size:14px;display:flex;gap:8px;"><i class="fa-solid fa-map-pin" style="color:#2563eb;margin-top:4px;"></i> <span>'.(!empty($p->address_location) ? nl2br(htmlspecialchars($p->address_location)) : 'Address not specified').'</span></p>
                            
                            <div style="font-size:14px;background:#fff;border:1px solid #e2e8f0;padding:15px;">
                                <div style="display:flex;justify-content:space-between;margin-bottom:8px;border-bottom:1px dashed #e2e8f0;padding-bottom:8px;">
                                    <strong><i class="fa-solid fa-car text-muted"></i> Access</strong> <span style="color:#f59e0b;">'.$ratingAccess.'</span>
                                </div>
                                <div style="display:flex;justify-content:space-between;margin-bottom:8px;border-bottom:1px dashed #e2e8f0;padding-bottom:8px;">
                                    <strong><i class="fa-regular fa-face-smile text-muted"></i> Comfort</strong> <span style="color:#f59e0b;">'.$ratingComfort.'</span>
                                </div>
                                <div style="display:flex;justify-content:space-between;">
                                    <strong><i class="fa-solid fa-mug-hot text-muted"></i> Food/Bev</strong> <span style="color:#f59e0b;">'.$ratingFoody.'</span>
                                </div>
                            </div>
                            
                            <div style="margin-top:15px;">
                                <strong style="font-size:13px;text-transform:uppercase;display:block;margin-bottom:10px;color:#64748b;">Available Facilities</strong>
                                <div style="display:flex;flex-wrap:wrap;gap:8px;">
                                    '.(empty($facList) ? '<em style="font-size:13px;">No facilities</em>' : implode(' ', $facList)).'
                                </div>
                            </div>
                        </div>
                        
                        <div style="width:100%;max-width:350px;">
                            <div style="border:3px solid #fff;box-shadow:0 2px 10px rgba(0,0,0,0.1);height:220px;">
                                <iframe title="'.($p->title ?? '').'" src="'.$mapLocation.'" style="width:100%;height:100%;border:0;" allowfullscreen="no" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                            </div>
                        </div>
                    </div>
                </div>';
            } elseif ($themeSelection == 'Minimalist List') {
                $html = '
                <div style="margin-bottom:30px;font-family:\'Inter\',sans-serif;padding-left:15px;border-left:4px solid '.$ratingColor.';">
                    <div style="display:flex;align-items:baseline;gap:12px;margin-bottom:5px;">
                        <h3 style="margin:0;font-size:20px;font-weight:700;color:#1e293b;">'.($p->title ?? 'Infograph').'</h3>
                        <span style="font-size:13px;padding:2px 8px;background:#f1f5f9;border-radius:4px;color:#475569;">'.($p->place_type ?? 'Place').'</span>
                        <span style="margin-left:auto;color:#f59e0b;font-size:15px;">'.$stars.' <span style="color:#64748b;font-size:14px;font-weight:600;margin-left:5px;">('.$rating.'/5)</span></span>
                    </div>
                    
                    <p style="color:#64748b;font-size:14px;margin:0 0 15px;"><i class="fa-solid fa-location-dot me-1"></i> '.(!empty($p->address_location) ? htmlspecialchars(str_replace("\n", ", ", $p->address_location)) : 'Unknown Location').'</p>
                    
                    <div style="display:flex;flex-wrap:wrap;gap:30px;margin-bottom:15px;">
                        <div>
                            <div style="font-size:12px;font-weight:700;text-transform:uppercase;color:#94a3b8;margin-bottom:8px;">Scores</div>
                            <div style="font-size:13px;line-height:1.8;">
                                <div><span style="display:inline-block;width:70px;color:#475569;">Access:</span> <span style="color:#f59e0b;">'.$ratingAccess.'</span></div>
                                <div><span style="display:inline-block;width:70px;color:#475569;">Comfort:</span> <span style="color:#f59e0b;">'.$ratingComfort.'</span></div>
                                <div><span style="display:inline-block;width:70px;color:#475569;">Foody:</span> <span style="color:#f59e0b;">'.$ratingFoody.'</span></div>
                            </div>
                        </div>
                        <div style="flex:1;">
                            <div style="font-size:12px;font-weight:700;text-transform:uppercase;color:#94a3b8;margin-bottom:8px;">Features</div>
                            <div style="display:flex;flex-wrap:wrap;gap:6px;">
                                '.(empty($facList) ? '<span style="color:#94a3b8;font-size:13px;">None</span>' : implode(' ', $facList)).'
                            </div>
                        </div>
                    </div>
                    
                    <div style="height:150px;max-width:100%;border-radius:8px;overflow:hidden;border:1px solid #e2e8f0;">
                         <iframe title="'.($p->title ?? '').'" src="'.$mapLocation.'" style="width:100%;height:100%;border:0;" allowfullscreen="no" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>';
            } else {
                // Default Modern Card Theme
                $html = '
                <div style="background:#fff;border:1px solid #e2e8f0;border-radius:16px;overflow:hidden;margin-bottom:30px;box-shadow:0 10px 25px -5px rgba(0,0,0,0.05);font-family:\'Inter\',sans-serif;">
                    <!-- Header -->
                    <div style="padding:24px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:15px;background:linear-gradient(to right, #ffffff, #f8fafc);">
                        <div>
                            <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
                                <span style="background:#eff6ff;color:#2563eb;padding:4px 10px;border-radius:8px;font-size:11px;font-weight:800;letter-spacing:0.05em;text-transform:uppercase;">
                                    <i class="fa-solid fa-location-dot"></i> '.($p->place_type ?? 'Place').'
                                </span>
                            </div>
                            <h3 style="margin:0;font-size:24px;font-weight:800;color:#0f172a;">'.($p->title ?? 'Infograph').'</h3>
                            <p style="margin:8px 0 0;color:#64748b;font-size:13.5px;display:flex;align-items:flex-start;gap:6px;"><i class="fa-solid fa-map-pin text-primary mt-1"></i> <span>'.(!empty($p->address_location) ? nl2br(htmlspecialchars($p->address_location)) : 'Address not specified').'</span></p>
                        </div>
                        <div style="background:'.$ratingBg.';border:2px solid '.$ratingColor.';border-radius:16px;padding:15px 25px;text-align:center;min-width:140px;">
                            <div style="font-size:11px;font-weight:800;color:'.$ratingColor.';text-transform:uppercase;letter-spacing:0.1em;margin-bottom:5px;">Overall</div>
                            <div style="font-size:32px;font-weight:900;color:'.$ratingColor.';line-height:1;">'.$rating.'<span style="font-size:18px;opacity:0.6;">/5</span></div>
                            <div style="color:#f59e0b;font-size:12px;margin-top:5px;letter-spacing:2px;">'.$stars.'</div>
                        </div>
                    </div>

                    <!-- Body Grid -->
                    <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(300px, 1fr));">
                        
                        <!-- Left: Ratings Detail & Facilities -->
                        <div style="padding:24px;border-right:1px solid #f1f5f9;">
                            
                            <h4 style="font-size:14px;font-weight:800;color:#334155;text-transform:uppercase;margin:0 0 15px;letter-spacing:0.05em;display:flex;align-items:center;gap:8px;"><i class="fa-solid fa-star-half-stroke text-primary"></i> Detailed Rating</h4>
                            
                            <div style="display:flex;flex-direction:column;gap:12px;margin-bottom:25px;">
                                <div style="display:flex;justify-content:space-between;align-items:center;font-size:13.5px;font-weight:600;color:#475569;">
                                    <span><i class="fa-solid fa-car text-muted me-2"></i> Access</span>
                                    <div style="color:#f59e0b;letter-spacing:2px;">'.$ratingAccess.'</div>
                                </div>
                                <div style="display:flex;justify-content:space-between;align-items:center;font-size:13.5px;font-weight:600;color:#475569;">
                                    <span><i class="fa-regular fa-face-smile text-muted me-2"></i> Comfort</span>
                                    <div style="color:#f59e0b;letter-spacing:2px;">'.$ratingComfort.'</div>
                                </div>
                                <div style="display:flex;justify-content:space-between;align-items:center;font-size:13.5px;font-weight:600;color:#475569;">
                                    <span><i class="fa-solid fa-mug-hot text-muted me-2"></i> Food & Bev</span>
                                    <div style="color:#f59e0b;letter-spacing:2px;">'.$ratingFoody.'</div>
                                </div>
                            </div>

                            <h4 style="font-size:14px;font-weight:800;color:#334155;text-transform:uppercase;margin:0 0 15px;letter-spacing:0.05em;display:flex;align-items:center;gap:8px;"><i class="fa fa-layer-group text-primary"></i> Facilities</h4>
                            <div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:-8px;">
                                '.$facText.'
                            </div>
                        </div>

                        <!-- Right: Map -->
                        <div style="background:#f8fafc;padding:24px;">
                            <h4 style="font-size:14px;font-weight:800;color:#334155;text-transform:uppercase;margin:0 0 15px;letter-spacing:0.05em;display:flex;align-items:center;gap:8px;"><i class="fa-regular fa-map text-primary"></i> Map Location</h4>
                            <div style="border-radius:12px;overflow:hidden;border:1px solid #e2e8f0;box-shadow:0 4px 6px -1px rgba(0,0,0,0.05);height:calc(100% - 35px);min-height:220px;">
                                <iframe title="'.($p->title ?? '').'" src="'.$mapLocation.'" style="width:100%;height:100%;border:0;" allowfullscreen="no" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                            </div>
                        </div>

                    </div>
                </div>';
            }
            $html .= '<script type="application/ld+json">
{
    "@context": "https://schema.org/",
    "@type": "'.($p->place_type ?? 'Place').'",
    "name": "'.($p->title ?? '').'",
    "review": {
        "@type": "Review",
        "reviewRating": {
            "@type": "Rating",
            "ratingValue": '.$rating.',
            "worstRating": 1,
            "bestRating": 5
        },
        "author": {
          "@type": "Person",
          "name": "'.($p->author ?? '').'"
        }
    },
    "geo": {
        "@type": "GeoCoordinates",
        "latitude": '.$map_latitude.',
        "longitude": '.$map_longitude.'
    },
    "url": "'.Site::canonical().'"
}
</script>';
            return $html;
        }

    }
}
