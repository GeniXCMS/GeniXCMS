<?php
$schema = [
    'header' => [
        'title' => 'Xiomers Settings',
        'subtitle' => 'Configure your store currency, shipping origin, and general logic.',
        'icon' => 'bi bi-gear-wide-connected',
        'button' => [
            'type' => 'button',
            'name' => 'nixomers_settings_save',
            'label' => 'Update Settings',
            'icon' => 'bi bi-save',
            'form' => 'nixSettingsForm',
            'class' => 'btn btn-primary fw-bold rounded-pill shadow-sm px-4'
        ]
    ],
    'tab_mode' => 'js',
    'tab_style' => 'modern',
    'card_wrapper' => false,
    'tabs' => [
        'general' => [
            'label' => 'General Store',
            'icon' => 'bi bi-gear',
            'content' => [
                [
                    'type' => 'card',
                    'title' => 'Financial & Regional Settings',
                    'icon' => 'bi bi-currency-exchange',
                    'body_elements' => [
                        [
                            'type' => 'row',
                            'items' => [
                                [
                                    'width' => 6,
                                    'content' => [
                                        'type' => 'input',
                                        'name' => 'currency',
                                        'label' => 'Currency Symbol',
                                        'value' => (Options::v('nixomers_currency') ?: 'IDR'),
                                        'help' => 'E.g: IDR, Rp, USD, $'
                                    ]
                                ],
                                [
                                    'width' => 6,
                                    'content' => [
                                        'type' => 'input',
                                        'name' => 'tax_rate',
                                        'label' => 'Tax Rate / VAT (%)',
                                        'input_type' => 'number',
                                        'value' => (Options::v('nixomers_tax') ?: '0'),
                                        'help' => 'Value in percentage (%)'
                                    ]
                                ]
                            ]
                        ],
                        [
                            'type' => 'select',
                            'name' => 'format',
                            'label' => 'Currency Format',
                            'options' => [
                                'dot' => '1.234,56 (Dot as thousands, Comma as decimal)',
                                'comma' => '1,234.56 (Comma as thousands, Dot as decimal)',
                                'space' => '1 234,56 (Space as thousands)',
                                'none' => '1234.56 (No separators)'
                            ],
                            'selected' => (Options::v('nixomers_format') ?: 'dot'),
                            'help' => 'Choose how prices are displayed to customers'
                        ],
                        [
                            'type' => 'select',
                            'name' => 'framework',
                            'label' => 'Frontend Layout Framework',
                            'options' => [
                                'bootstrap' => 'Bootstrap Framework',
                                'tailwindcss' => 'TailwindCSS Framework'
                            ],
                            'selected' => (Options::v('nixomers_framework') ?: 'bootstrap'),
                            'help' => 'Choose which CSS framework to use for the frontend layout'
                        ]
                    ]
                ],
                [
                    'type' => 'card',
                    'title' => 'Store Identity',
                    'icon' => 'bi bi-shop',
                    'subtitle' => 'Main store information used for shipping and invoices',
                    'body_elements' => [
                        [
                            'type' => 'row',
                            'items' => [
                                [
                                    'width' => 6,
                                    'content' => [
                                        'type' => 'input',
                                        'name' => 'nix_store_name',
                                        'label' => 'Store Name',
                                        'value' => (Options::v('nix_store_name') ?: Options::v('sitename')),
                                        'help' => 'Main store name'
                                    ]
                                ],
                                [
                                    'width' => 6,
                                    'content' => [
                                        'type' => 'input',
                                        'name' => 'nix_store_phone',
                                        'label' => 'Store Phone',
                                        'value' => (Options::v('nix_store_phone') ?: Options::v('sitephone')),
                                        'help' => 'Main contact number'
                                    ]
                                ]
                            ]
                        ],
                        [
                            'type' => 'textarea',
                            'name' => 'nix_store_address',
                            'label' => 'Store Full Address',
                            'value' => (Options::v('nix_store_address') ?: Options::v('siteaddress')),
                            'rows' => 3,
                            'help' => 'Complete address for legal and shipping purposes'
                        ]
                    ]
                ]
            ]
        ],
        // Tab 3: Shipping
        'shipping' => [
            'label' => 'Shipping Origin',
            'icon' => 'bi bi-truck',
            'content' => [
                [
                    'type' => 'card',
                    'title' => 'Warehouse / Shipping Origin',
                    'icon' => 'bi bi-geo-alt',
                    'subtitle' => 'Define where your treasures are shipped from',
                    'body_elements' => [
                        [
                            'type' => 'select',
                            'name' => 'orig_country',
                            'label' => 'Origin Country',
                            'id' => 'orig_country',
                            'options' => [
                                'Indonesia' => 'Indonesia',
                                'United States' => 'United States',
                                'Japan' => 'Japan'
                            ],
                            'selected' => (Options::v('nix_orig_country') ?: 'Indonesia')
                        ],
                        [
                            'type' => 'raw',
                            'html' => '<div id="indonesia_shipping_settings" style="display: none;">'
                        ],
                        [
                            'type' => 'row',
                            'class' => 'mt-4 pt-3 border-top',
                            'items' => [
                                [
                                    'width' => 12,
                                    'content' => [
                                        'type' => 'select',
                                        'name' => 'shipping_engine',
                                        'id' => 'shipping_engine',
                                        'label' => 'Primary Shipping Engine',
                                        'options' => [
                                            'kiriminaja' => 'KiriminAja (District Level)',
                                            'apicoid' => 'API.CO.ID (Village Level)'
                                        ],
                                        'selected' => (Options::v('nix_shipping_engine') ?: 'kiriminaja'),
                                        'help' => 'Choose which service to use for local shipping calculations'
                                    ]
                                ]
                            ]
                        ],
                        [
                            'type' => 'raw',
                            'html' => '<div id="kiriminaja_settings_box">'
                        ],
                        [
                            'type' => 'row',
                            'items' => [
                                [
                                    'width' => 8,
                                    'content' => [
                                        'type' => 'input',
                                        'name' => 'kiriminaja_token',
                                        'label' => 'Kiriminaja API Token',
                                        'value' => (Options::v('nix_kiriminaja_token') ?: ''),
                                        'help' => 'Get your token from KiriminAja dashboard'
                                    ]
                                ],
                                [
                                    'width' => 4,
                                    'content' => [
                                        'type' => 'select',
                                        'name' => 'kiriminaja_mode',
                                        'label' => 'API Mode',
                                        'options' => [
                                            'sandbox' => 'Staging / Sandbox',
                                            'live' => 'Production / Live'
                                        ],
                                        'selected' => (Options::v('nix_kiriminaja_mode') ?: 'sandbox')
                                    ]
                                ]
                            ]
                        ],
                        [
                            'type' => 'row',
                            'class' => 'mt-3',
                            'items' => [
                                [
                                    'width' => 4,
                                    'content' => [
                                        'type' => 'input',
                                        'name' => 'kiriminaja_origin_id',
                                        'label' => 'Kiriminaja Origin District ID',
                                        'id' => 'kiriminaja_origin_id',
                                        'value' => (Options::v('nix_kiriminaja_origin_id') ?: ''),
                                        'help' => 'Numeric ID for origin district'
                                    ]
                                ],
                                [
                                    'width' => 8,
                                    'content' => [
                                        'type' => 'raw',
                                        'html' => '
                                                <label class="form-label d-block">&nbsp;</label>
                                                <div class="input-group">
                                                    <input type="text" id="search_district_input" class="form-control rounded-start-4 bg-light" placeholder="Search District Name...">
                                                    <button class="btn btn-secondary rounded-end-4" type="button" id="btn_search_district">Search ID</button>
                                                </div>
                                                <div id="district_search_results" class="mt-2 small text-muted" style="max-height: 150px; overflow-y: auto;"></div>
                                                '
                                    ]
                                ]
                            ]
                        ],
                        [
                            'type' => 'raw',
                            'html' => '</div><div id="apicoid_settings_box">'
                        ],
                        [
                            'type' => 'row',
                            'items' => [
                                [
                                    'width' => 12,
                                    'content' => [
                                        'type' => 'input',
                                        'name' => 'apicoid_token',
                                        'id' => 'apicoid_token',
                                        'label' => 'API.CO.ID Token',
                                        'value' => (Options::v('nix_apicoid_token') ?: ''),
                                        'help' => 'Get your token from api.co.id dashboard'
                                    ]
                                ]
                            ]
                        ],
                        [
                            'type' => 'row',
                            'class' => 'mt-3 mb-0',
                            'items' => [
                                [
                                    'width' => 6,
                                    'content' => [
                                        'type' => 'select',
                                        'name' => 'orig_province',
                                        'id' => 'orig_province',
                                        'label' => 'Origin Province',
                                        'options' => ['' => 'Select Province...']
                                    ]
                                ],
                                [
                                    'width' => 6,
                                    'content' => [
                                        'type' => 'select',
                                        'name' => 'orig_city',
                                        'id' => 'orig_city',
                                        'label' => 'Origin City/Regency',
                                        'options' => ['' => 'Select City...']
                                    ]
                                ]
                            ]
                        ],
                        [
                            'type' => 'row',
                            'class' => 'mt-0',
                            'items' => [
                                [
                                    'width' => 6,
                                    'content' => [
                                        'type' => 'select',
                                        'name' => 'orig_district',
                                        'id' => 'orig_district',
                                        'label' => 'Origin District',
                                        'options' => ['' => 'Select District...']
                                    ]
                                ],
                                [
                                    'width' => 6,
                                    'content' => [
                                        'type' => 'select',
                                        'name' => 'orig_village',
                                        'id' => 'orig_village',
                                        'label' => 'Origin Village (Destination ID)',
                                        'options' => [(Options::v('nix_orig_village') ?: '') => (Options::v('nix_orig_village') ?: 'Select Village...')],
                                        'selected' => (Options::v('nix_orig_village') ?: '')
                                    ]
                                ]
                            ]
                        ],
                        [
                            'type' => 'row',
                            'class' => 'mt-4',
                            'items' => [
                                [
                                    'width' => 12,
                                    'content' => [
                                        'type' => 'raw',
                                        'html' => '
                                                <label class="form-label d-block fw-bold border-bottom pb-2 mb-3"><i class="bi bi-check2-all me-1"></i> Enabled Couriers</label>
                                                <div class="row g-3">
                                                ' . (function () {
                                            $all = [
                                                'JNE' => 'JNE Express',
                                                'JNECARGO' => 'JNE Cargo',
                                                'SICEPAT' => 'SiCepat Express',
                                                'SICEPATCARGO' => 'SiCepat Cargo',
                                                'NINJA' => 'Ninja Express',
                                                'LION' => 'Lion Parcel',
                                                'SAP' => 'SAP Express',
                                                'SAPLITE' => 'SAP Lite',
                                                'SAPCARGO' => 'SAP Cargo',
                                                'JNT' => 'J&T Express',
                                                'ANTERAJA' => 'Anteraja',
                                                'IDEXPRESS' => 'ID Express',
                                                'SENTRAL' => 'Sentral Cargo',
                                                'TIKI' => 'TIKI',
                                                'POS' => 'POS Indonesia'
                                            ];
                                            $enabled = explode(',', Options::v('nix_enabled_couriers') ?: '');
                                            if (empty(Options::v('nix_enabled_couriers'))) {
                                                $enabled = array_keys($all); // Default all enabled
                                            }
                                            $html = '';
                                            foreach ($all as $k => $v) {
                                                $checked = in_array($k, $enabled) ? 'checked' : '';
                                                $html .= '
                                                        <div class="col-md-4 col-lg-3">
                                                            <div class="form-check form-switch p-2 border rounded-3 bg-white">
                                                                <input class="form-check-input ms-0 me-2" type="checkbox" name="enabled_couriers[]" value="' . $k . '" id="chk_' . $k . '" ' . $checked . '>
                                                                <label class="form-check-label small fw-bold" for="chk_' . $k . '">' . $v . '</label>
                                                            </div>
                                                        </div>';
                                            }
                                            return $html;
                                        })() . '
                                                </div>
                                                <div class="alert alert-info mt-3 py-2 small border-0">
                                                    <i class="bi bi-info-circle me-1"></i> Only selected couriers will be displayed to customers during checkout.
                                                </div>
                                                '
                                    ]
                                ]
                            ]
                        ],
                        [
                            'type' => 'raw',
                            'html' => '</div></div>'
                        ],
                    ]
                ]
            ]
        ],
        // Tab 2: Code Patterns
        'patterns' => [
            'label' => 'ID Patterns',
            'icon' => 'bi bi-hash',
            'content' => [
                [
                    'type' => 'card',
                    'title' => 'Automation & ID Patterns',
                    'icon' => 'bi bi-regex',
                    'subtitle' => 'Define prefixes and date-based formats for automatic generation',
                    'body_elements' => [
                        [
                            'type' => 'heading',
                            'text' => 'Invoice Numbering',
                            'class' => 'h6 fw-bold mb-3 border-bottom pb-2'
                        ],
                        [
                            'type' => 'row',
                            'items' => [
                                [
                                    'width' => 4,
                                    'content' => [
                                        'type' => 'input',
                                        'name' => 'invoice_prefix',
                                        'label' => 'Prefix',
                                        'value' => (Options::v('nixomers_invoice_prefix') ?: 'INV'),
                                        'help' => 'E.g: INV'
                                    ]
                                ],
                                [
                                    'width' => 8,
                                    'content' => [
                                        'type' => 'input',
                                        'name' => 'invoice_format',
                                        'label' => 'Invoice Format Pattern',
                                        'value' => (Options::v('nixomers_invoice_format') ?: '{PREFIX}-{YYYY}{MM}{DD}-{ID}'),
                                        'help' => 'Available: {PREFIX}, {YYYY}, {YY}, {MM}, {DD}, {ID}'
                                    ]
                                ]
                            ]
                        ],
                        [
                            'type' => 'heading',
                            'text' => 'Order Code Numbering',
                            'class' => 'h6 fw-bold mb-3 mt-4 border-bottom pb-2'
                        ],
                        [
                            'type' => 'row',
                            'items' => [
                                [
                                    'width' => 4,
                                    'content' => [
                                        'type' => 'input',
                                        'name' => 'order_prefix',
                                        'label' => 'Prefix',
                                        'value' => (Options::v('nixomers_order_prefix') ?: 'ORD'),
                                        'help' => 'E.g: ORD'
                                    ]
                                ],
                                [
                                    'width' => 8,
                                    'content' => [
                                        'type' => 'input',
                                        'name' => 'order_format',
                                        'label' => 'Order Format Pattern',
                                        'value' => (Options::v('nixomers_order_format') ?: '{PREFIX}-{YYYY}{MM}{DD}-{ID}'),
                                        'help' => 'Available: {PREFIX}, {YYYY}, {YY}, {MM}, {DD}, {ID}'
                                    ]
                                ]
                            ]
                        ],
                        [
                            'type' => 'heading',
                            'text' => 'Transaction Code Numbering',
                            'class' => 'h6 fw-bold mb-3 mt-4 border-bottom pb-2'
                        ],
                        [
                            'type' => 'row',
                            'items' => [
                                [
                                    'width' => 4,
                                    'content' => [
                                        'type' => 'input',
                                        'name' => 'trx_prefix',
                                        'label' => 'Prefix',
                                        'value' => (Options::v('nixomers_trx_prefix') ?: 'TRX'),
                                        'help' => 'E.g: TRX'
                                    ]
                                ],
                                [
                                    'width' => 8,
                                    'content' => [
                                        'type' => 'input',
                                        'name' => 'trx_format',
                                        'label' => 'Transaction Format Pattern',
                                        'value' => (Options::v('nixomers_trx_format') ?: '{PREFIX}-{ID}'),
                                        'help' => 'Available: {PREFIX}, {ID} (Autogenerated Hash)'
                                    ]
                                ]
                            ]
                        ],
                        [
                            'type' => 'heading',
                            'text' => 'Product SKU Coding',
                            'class' => 'h6 fw-bold mb-3 mt-4 border-bottom pb-2'
                        ],
                        [
                            'type' => 'row',
                            'items' => [
                                [
                                    'width' => 4,
                                    'content' => [
                                        'type' => 'input',
                                        'name' => 'product_prefix',
                                        'label' => 'Prefix',
                                        'value' => (Options::v('nixomers_product_prefix') ?: 'PRD'),
                                        'help' => 'E.g: PRD'
                                    ]
                                ],
                                [
                                    'width' => 8,
                                    'content' => [
                                        'type' => 'input',
                                        'name' => 'product_format',
                                        'label' => 'SKU Format Pattern',
                                        'value' => (Options::v('nixomers_product_format') ?: '{PREFIX}-{ID}'),
                                        'help' => 'Available: {PREFIX}, {ID}'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ],
        // Tab 4: Label Settings
        'label_settings' => [
            'label' => 'Shipping Label',
            'icon' => 'bi bi-printer',
            'content' => [
                [
                    'type' => 'card',
                    'title' => 'Shipping Label Layout',
                    'icon' => 'bi bi-card-checklist',
                    'subtitle' => 'Configure how your shipping labels will look when printed',
                    'body_elements' => [
                        [
                            'type' => 'input',
                            'name' => 'nix_label_header',
                            'label' => 'Label Header Text',
                            'value' => (Options::v('nix_label_header') ?: 'SHIPPING'),
                            'help' => 'Title at the very top of the label'
                        ],
                        [
                            'type' => 'row',
                            'items' => [
                                [
                                    'width' => 4,
                                    'content' => [
                                        'type' => 'select',
                                        'name' => 'nix_label_show_store_name',
                                        'label' => 'Show Store Name',
                                        'options' => ['yes' => 'Show', 'no' => 'Hide'],
                                        'selected' => (Options::v('nix_label_show_store_name') ?: 'yes')
                                    ]
                                ],
                                [
                                    'width' => 4,
                                    'content' => [
                                        'type' => 'select',
                                        'name' => 'nix_label_show_store_phone',
                                        'label' => 'Show Store Phone',
                                        'options' => ['yes' => 'Show', 'no' => 'Hide'],
                                        'selected' => (Options::v('nix_label_show_store_phone') ?: 'yes')
                                    ]
                                ],
                                [
                                    'width' => 4,
                                    'content' => [
                                        'type' => 'select',
                                        'name' => 'nix_label_show_store_address',
                                        'label' => 'Show Store Address',
                                        'options' => ['yes' => 'Show', 'no' => 'Hide'],
                                        'selected' => (Options::v('nix_label_show_store_address') ?: 'yes')
                                    ]
                                ]
                            ]
                        ],
                        [
                            'type' => 'row',
                            'items' => [
                                [
                                    'width' => 4,
                                    'content' => [
                                        'type' => 'select',
                                        'name' => 'nix_label_show_logo',
                                        'label' => 'Display Header',
                                        'options' => [
                                            'text' => 'Store Name (Text)',
                                            'logo' => 'Site Logo (Image)'
                                        ],
                                        'selected' => (Options::v('nix_label_show_logo') ?: 'text'),
                                        'help' => 'Choose what to show in the header'
                                    ]
                                ],
                                [
                                    'width' => 4,
                                    'content' => [
                                        'type' => 'select',
                                        'name' => 'nix_label_show_items',
                                        'label' => 'Show Product List',
                                        'options' => [
                                            'yes' => 'Yes, show items',
                                            'no' => 'No, hide items'
                                        ],
                                        'selected' => (Options::v('nix_label_show_items') ?: 'yes'),
                                        'help' => 'Include item list for packing'
                                    ]
                                ],
                                [
                                    'width' => 4,
                                    'content' => [
                                        'type' => 'select',
                                        'name' => 'nix_label_show_sender',
                                        'label' => 'Show Sender Info',
                                        'options' => [
                                            'yes' => 'Yes, show sender',
                                            'no' => 'No, hide (Dropship Mode)'
                                        ],
                                        'selected' => (Options::v('nix_label_show_sender') ?: 'yes'),
                                        'help' => 'Hide sender to give full width to receiver'
                                    ]
                                ]
                            ]
                        ],
                        [
                            'type' => 'row',
                            'class' => 'mt-3 pt-3 border-top',
                            'items' => [
                                [
                                    'width' => 6,
                                    'content' => [
                                        'type' => 'select',
                                        'name' => 'nix_label_show_order_barcode',
                                        'label' => 'Show Order ID Barcode',
                                        'options' => [
                                            'yes' => 'Yes, show barcode',
                                            'no' => 'No, hide'
                                        ],
                                        'selected' => (Options::v('nix_label_show_order_barcode') ?: 'yes'),
                                        'help' => 'Display barcode of the order ID for scanning'
                                    ]
                                ],
                                [
                                    'width' => 6,
                                    'content' => [
                                        'type' => 'select',
                                        'name' => 'nix_label_show_package_barcode',
                                        'label' => 'Show Packaging Barcode',
                                        'options' => [
                                            'yes' => 'Yes, show barcode',
                                            'no' => 'No, hide'
                                        ],
                                        'selected' => (Options::v('nix_label_show_package_barcode') ?: 'no'),
                                        'help' => 'Display barcode of the packaging/sorting code'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ],
        // Tab: Notifications
        'notifications' => [
            'label' => 'Notifications',
            'icon' => 'bi bi-megaphone',
            'content' => (function () {
                // Gather registered providers via hooks
                $emailProviders = Hooks::filter('nix_notification_email_providers', [
                    'none' => 'Disabled',
                    'native' => 'PHP Native Mail'
                ]);
                $waProviders = Hooks::filter('nix_notification_wa_providers', [
                    'none' => 'Disabled'
                ]);

                $activeEmail = Options::v('nix_notif_email_active') ?: 'none';
                $activeWA = Options::v('nix_notif_wa_active') ?: 'none';

                return [
                    [
                        'type' => 'row',
                        'items' => [
                            [
                                'width' => 6,
                                'content' => [
                                    'type' => 'card',
                                    'title' => 'Email Notification',
                                    'icon' => 'bi bi-envelope',
                                    'body_elements' => [
                                        [
                                            'type' => 'select',
                                            'name' => 'nix_notif_email_active',
                                            'id' => 'nix_notif_email_active',
                                            'label' => 'Active Email Provider',
                                            'options' => $emailProviders,
                                            'selected' => $activeEmail,
                                            'help' => 'Select service to send order emails'
                                        ],
                                        [
                                            'type' => 'html',
                                            'html' => (function () use ($emailProviders, $activeEmail) {
                                                $out = '<div class="mt-3 p-3 bg-light rounded border border-dashed">';
                                                foreach ($emailProviders as $id => $label) {
                                                    $display = ($id === $activeEmail) ? 'block' : 'none';
                                                    $out .= '<div class="notif-settings-email" id="notif_email_' . $id . '" style="display:' . $display . '">';
                                                    $out .= Hooks::filter('nix_notification_settings_email_' . $id, '<p class="text-muted small mb-0"><i class="bi bi-info-circle"></i> Service "' . $label . '" has no additional settings.</p>');
                                                    $out .= '</div>';
                                                }
                                                $out .= '</div>';
                                                return $out;
                                            })()
                                        ]
                                    ]
                                ]
                            ],
                            [
                                'width' => 6,
                                'content' => [
                                    'type' => 'card',
                                    'title' => 'WhatsApp Notification',
                                    'icon' => 'bi bi-whatsapp',
                                    'body_elements' => [
                                        [
                                            'type' => 'select',
                                            'name' => 'nix_notif_wa_active',
                                            'id' => 'nix_notif_wa_active',
                                            'label' => 'Active WhatsApp Provider',
                                            'options' => $waProviders,
                                            'selected' => $activeWA,
                                            'help' => 'Select service to send WA notifications'
                                        ],
                                        [
                                            'type' => 'html',
                                            'html' => (function () use ($waProviders, $activeWA) {
                                                $out = '<div class="mt-3 p-3 bg-light rounded border border-dashed">';
                                                foreach ($waProviders as $id => $label) {
                                                    $display = ($id === $activeWA) ? 'block' : 'none';
                                                    $out .= '<div class="notif-settings-wa" id="notif_wa_' . $id . '" style="display:' . $display . '">';
                                                    $out .= Hooks::filter('nix_notification_settings_wa_' . $id, '<p class="text-muted small mb-0"><i class="bi bi-info-circle"></i> Service "' . $label . '" has no additional settings.</p>');
                                                    $out .= '</div>';
                                                }
                                                $out .= '</div>';

                                                // Add JavaScript for instant switching
                                                $out .= '
                                                        <script>
                                                            document.getElementById("nix_notif_email_active").addEventListener("change", function() {
                                                                document.querySelectorAll(".notif-settings-email").forEach(el => el.style.display = "none");
                                                                let target = document.getElementById("notif_email_" + this.value);
                                                                if (target) target.style.display = "block";
                                                            });
                                                            document.getElementById("nix_notif_wa_active").addEventListener("change", function() {
                                                                document.querySelectorAll(".notif-settings-wa").forEach(el => el.style.display = "none");
                                                                let target = document.getElementById("notif_wa_" + this.value);
                                                                if (target) target.style.display = "block";
                                                            });
                                                        </script>';
                                                return $out;
                                            })()
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ];
            })()
        ],
        // Tab: Invoice
        'invoice' => [
            'label' => 'Invoice Design',
            'icon' => 'bi bi-file-earmark-pdf',
            'content' => [
                [
                    'type' => 'card',
                    'title' => 'Invoice Branding & Info',
                    'icon' => 'bi bi-palette',
                    'subtitle' => 'Customize how your invoice looks to customers',
                    'body_elements' => [
                        [
                            'type' => 'row',
                            'items' => [
                                [
                                    'width' => 4,
                                    'content' => [
                                        'type' => 'select',
                                        'name' => 'nix_invoice_show_store_name',
                                        'label' => 'Show Store Name',
                                        'options' => ['yes' => 'Show', 'no' => 'Hide'],
                                        'selected' => (Options::v('nix_invoice_show_store_name') ?: 'yes')
                                    ]
                                ],
                                [
                                    'width' => 4,
                                    'content' => [
                                        'type' => 'select',
                                        'name' => 'nix_invoice_show_store_phone',
                                        'label' => 'Show Store Phone',
                                        'options' => ['yes' => 'Show', 'no' => 'Hide'],
                                        'selected' => (Options::v('nix_invoice_show_store_phone') ?: 'yes')
                                    ]
                                ],
                                [
                                    'width' => 4,
                                    'content' => [
                                        'type' => 'select',
                                        'name' => 'nix_invoice_show_store_address',
                                        'label' => 'Show Store Address',
                                        'options' => ['yes' => 'Show', 'no' => 'Hide'],
                                        'selected' => (Options::v('nix_invoice_show_store_address') ?: 'yes')
                                    ]
                                ]
                            ]
                        ],
                        [
                            'type' => 'row',
                            'items' => [
                                [
                                    'width' => 6,
                                    'content' => [
                                        'type' => 'input',
                                        'name' => 'nix_invoice_color',
                                        'label' => 'Primary Brand Color',
                                        'input_type' => 'color',
                                        'value' => (Options::v('nix_invoice_color') ?: '#1a73e8')
                                    ]
                                ],
                                [
                                    'width' => 6,
                                    'content' => [
                                        'type' => 'select',
                                        'name' => 'nix_invoice_show_logo',
                                        'label' => 'Show Logo on Invoice',
                                        'options' => [
                                            'yes' => 'Show Logo',
                                            'no' => 'Hide Logo'
                                        ],
                                        'selected' => (Options::v('nix_invoice_show_logo') ?: 'yes')
                                    ]
                                ]
                            ]
                        ],
                        [
                            'type' => 'textarea',
                            'name' => 'nix_invoice_footer_note',
                            'label' => 'Invoice Footer Note',
                            'value' => (Options::v('nix_invoice_footer_note') ?: 'Thank you for your business!'),
                            'help' => 'This will appear at the bottom of the invoice'
                        ],
                        [
                            'type' => 'row',
                            'class' => 'mt-3 border-top pt-3',
                            'items' => [
                                [
                                    'width' => 8,
                                    'content' => [
                                        'type' => 'textarea',
                                        'name' => 'nix_invoice_extra_info',
                                        'label' => 'Extra Information (Side Box)',
                                        'value' => (Options::v('nix_invoice_extra_info') ?: ''),
                                        'help' => 'This will appear next to the total breakdown. Use for payment instructions, etc.',
                                        'rows' => 4
                                    ]
                                ],
                                [
                                    'width' => 4,
                                    'content' => [
                                        'type' => 'select',
                                        'name' => 'nix_invoice_extra_type',
                                        'label' => 'Alert Box Type',
                                        'options' => [
                                            'info' => 'Blue (Info)',
                                            'warning' => 'Yellow (Warning)',
                                            'danger' => 'Red (Danger)',
                                            'success' => 'Green (Success)',
                                            'light' => 'Grey (Light)'
                                        ],
                                        'selected' => (Options::v('nix_invoice_extra_type') ?: 'info')
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ],
        // Tab 4: Payment
        'payment' => [
            'label' => 'Payment',
            'icon' => 'bi bi-credit-card',
            'content' => [
                [
                    'type' => 'card',
                    'title' => 'Payment Gateway Activation',
                    'icon' => 'bi bi-credit-card-2-front',
                    'subtitle' => 'Activate the payment gateways you want to offer to your customers.',
                    'body_elements' => [
                        [
                            'type' => 'raw',
                            'html' => (function () {
                                $gateways = Hooks::filter('nix_payment_gateways', [
                                    'bank_transfer' => ['name' => 'Bank Transfer', 'icon' => 'bi bi-bank'],
                                    'paypal' => ['name' => 'PayPal', 'icon' => 'bi bi-paypal']
                                ]);

                                $enabled = explode(',', Options::v('nix_enabled_gateways') ?: 'bank_transfer');
                                $html = '<div class="gateway-settings"> ';

                                foreach ($gateways as $k => $v) {
                                    $checked = in_array($k, $enabled) ? 'checked' : '';
                                    $html .= '
                                            <div class="card mb-4 shadow-sm border-light-subtle">
                                                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                                                    <div class="d-flex align-items-center">
                                                        <i class="' . ($v['icon'] ?? 'bi bi-wallet2') . ' me-2 fs-4 text-primary"></i>
                                                        <h5 class="mb-0 fw-bold">' . $v['name'] . '</h5>
                                                    </div>
                                                    <div class="form-check form-switch p-0 m-0">
                                                        <input class="form-check-input" type="checkbox" name="nix_enabled_gateways[]" value="' . $k . '" id="gw_' . $k . '" ' . $checked . ' style="width: 2.5em; height: 1.25em; cursor: pointer;">
                                                    </div>
                                                </div>
                                                <div class="card-body bg-light-subtle">
                                                    ' . Hooks::filter('nix_gateway_settings_body_' . $k, '') . '
                                                </div>
                                            </div>';
                                }

                                $html .= '</div>';
                                return $html;
                            })()
                        ]
                    ]
                ]
            ]
        ]
    ],
    'content' => [
        [
            'type' => 'raw',
            'html' => '
                    <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        const selCountry = document.getElementById("orig_country");
                        const selEngine = document.getElementById("shipping_engine");
                        
                        // Indonesia Box visibility
                        const toggleIndoBox = () => {
                            const indoBox = document.getElementById("indonesia_shipping_settings");
                            if (selCountry.value === "Indonesia") {
                                indoBox.style.display = "block";
                            } else {
                                indoBox.style.display = "none";
                            }
                        };
                        selCountry.onchange = toggleIndoBox;
                        toggleIndoBox();

                        // Engine Toggle (called after fetchApicoid is defined below)
                        let toggleEngine;

                        // API.CO.ID Cascadings
                        const apiProv = document.getElementById("orig_province");
                        const apiCity = document.getElementById("orig_city");
                        const apiDist = document.getElementById("orig_district");
                        const apiVill = document.getElementById("orig_village");

                        const fetchApicoid = (type, params, targetSelect, placeholder) => {
                            targetSelect.innerHTML = `<option value=\"\">${placeholder}</option>`;
                            let id = "";
                            if (type === "cities") { id = params.province_code; type = "city"; }
                            else if (type === "districts") { id = params.regency_code; type = "district"; }
                            else if (type === "villages") { id = params.district_code; type = "village"; }
                            else if (type === "provinces") { type = "province"; }

                            let url = "' . Url::ajax("nixomers", "shipping_regions") . '&type=" + type;
                            if (id) url += `&parent=${id}`;
                            
                            return fetch(url)
                            .then(r => r.json())
                            .then(res => {
                                if (res.data && res.data.length > 0) {
                                    res.data.forEach(item => {
                                        let o = document.createElement("option");
                                        o.value = item.id;
                                        o.text = item.name;
                                        targetSelect.add(o);
                                    });
                                }
                                return res.data;
                            });
                        };

                        if (apiProv) {
                            apiProv.onchange = function() {
                                if (this.value) fetchApicoid("cities", {province_code: this.value}, apiCity, "Select City...");
                            };
                            apiCity.onchange = function() {
                                if (this.value) fetchApicoid("districts", {regency_code: this.value}, apiDist, "Select District...");
                            };
                            apiDist.onchange = function() {
                                if (this.value) fetchApicoid("villages", {district_code: this.value}, apiVill, "Select Village...");
                            };
                        }

                        // Define toggleEngine now that fetchApicoid is available
                        toggleEngine = () => {
                            const engine = selEngine.value;
                            const knBox = document.getElementById("kiriminaja_settings_box");
                            const apiBox = document.getElementById("apicoid_settings_box");
                            if (knBox) knBox.style.display = (engine === "kiriminaja") ? "block" : "none";
                            if (apiBox) apiBox.style.display = (engine === "apicoid") ? "block" : "none";

                            if (engine === "apicoid") {
                                selCountry.value = "Indonesia";
                                toggleIndoBox();
                                const tokenVal = document.getElementById("apicoid_token").value;
                                if (tokenVal && apiProv && apiProv.options.length <= 1) {
                                    // Initial Load & Pre-select
                                    const savedProv = "' . (Options::v('nix_orig_province') ?: '') . '";
                                    const savedCity = "' . (Options::v('nix_orig_city') ?: '') . '";
                                    const savedDist = "' . (Options::v('nix_orig_district') ?: '') . '";
                                    const savedVill = "' . (Options::v('nix_orig_village') ?: '') . '";
                                    
                                    fetchApicoid("provinces", {}, apiProv, "Select Province...").then(() => {
                                        if(savedProv) {
                                            apiProv.value = savedProv;
                                            fetchApicoid("cities", {province_code: savedProv}, apiCity, "Select City...").then(() => {
                                                if(savedCity) {
                                                    apiCity.value = savedCity;
                                                    fetchApicoid("districts", {regency_code: savedCity}, apiDist, "Select District...").then(() => {
                                                        if (savedDist) {
                                                            apiDist.value = savedDist;
                                                            fetchApicoid("villages", {district_code: savedDist}, apiVill, "Select Village...").then(() => {
                                                                if (savedVill) apiVill.value = savedVill;
                                                            });
                                                        }
                                                    });
                                                }
                                            });
                                        }
                                    });
                                }
                            }
                        };
                        selEngine.onchange = toggleEngine;
                        toggleEngine();

                        const apiTokenInput = document.getElementById("apicoid_token");
                        if (apiTokenInput) {
                            apiTokenInput.addEventListener("change", function() {
                                if (this.value && selEngine.value === "apicoid") {
                                    fetchApicoid("provinces", {}, apiProv, "Select Province...");
                                }
                            });
                        }

                        if (selEngine) {
                            selEngine.onchange = toggleEngine;
                            toggleEngine(); // Now safe to call
                        }

                        // KiriminAja District Search
                        const btnKira = document.getElementById("btn_search_district");
                        if (btnKira) {
                            btnKira.onclick = function() {
                                const q = document.getElementById("search_district_input").value;
                                const resDiv = document.getElementById("district_search_results");
                                if (q.length < 3) {
                                    alert("Please enter at least 3 characters");
                                    return;
                                }
                                resDiv.innerHTML = "Searching...";
                                fetch("' . Url::ajax("nixomers", "search_district") . '&q=" + encodeURIComponent(q))
                                .then(r => r.json())
                                .then(res => {
                                    resDiv.innerHTML = "";
                                    if (res.status && res.data) {
                                        res.data.forEach(d => {
                                            let div = document.createElement("div");
                                            div.className = "p-2 border-bottom cursor-pointer hover-bg-light";
                                            div.style.cursor = "pointer";
                                            div.innerHTML = `<strong>${d.text}</strong> <span class=\"badge bg-secondary\">${d.id}</span>`;
                                            div.onclick = function() {
                                                document.getElementById("kiriminaja_origin_id").value = d.id;
                                                resDiv.innerHTML = `<span class=\"text-success\">Selected: ${d.text} (${d.id})</span>`;
                                            };
                                            resDiv.appendChild(div);
                                        });
                                    } else {
                                        resDiv.innerHTML = `<span class=\"text-danger\">${res.message || "No results found"}</span>`;
                                    }
                                });
                            };
                        }
                    });
                    </script>
                    '
        ]
    ]
];

echo '<form action="' . $mod_url . '&sel=settings" method="post" id="nixSettingsForm">';
echo '<input type="hidden" name="token" value="' . TOKEN . '">';
$ui = new UiBuilder($schema);
echo $ui->render();
echo '</form>';
