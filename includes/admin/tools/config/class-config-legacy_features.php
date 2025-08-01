<?php

namespace OES\Admin\Tools;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

if (!class_exists('Config')) oes_include('admin/tools/config/class-config.php');

if (!class_exists('Legacy_Features')) :

    /**
     * Class Legacy_Features
     *
     * Implement the config tool for admin configurations.
     */
    class Legacy_Features extends Config
    {
        const OPTION_KEY = 'oes_legacy_features';

        const FEATURES = [
            'admin' => [
                'title' => 'Admin',
                'features' => [
                    'cache' => [
                        'title' => 'Cache',
                        'subtitle' => 'Enable cache feature.',
                        'default' => true
                    ]
                ]
            ],
            'tools' => [
                'title' => 'Tools',
                'features' => [
                    'update' => [
                        'title' => 'Update',
                        'subtitle' => 'Enable update tool.',
                        'default' => true
                    ],
                    'delete' => [
                        'title' => 'Delete',
                        'subtitle' => 'Enable delete tool.',
                        'default' => true
                    ]
                ]
            ]
        ];


        //Overwrite parent
        function set_table_data_for_display()
        {
            $currentFeaturesOption = get_option(self::OPTION_KEY);
            $currentFeatures = $currentFeaturesOption ? json_decode($currentFeaturesOption, true) : [];

            foreach (self::FEATURES as $featureCategoryKey => $featureCategory) {

                $this->add_table_header(($featureCategory['title'] ?: $featureCategoryKey));

                foreach ($featureCategory['features'] ?? [] as $feature => $featureData){

                    $this->add_table_row(
                        [
                            'title' => ($featureData['title'] ?: $feature),
                            'key' => self::OPTION_KEY . '[' . $feature . ']',
                            'value' => ($currentFeatures[$feature] ?? ($featureData['default'] ?? false)),
                            'type' => 'checkbox'
                        ],
                        [
                           'subtitle' => ($featureData['subtitle'] ?? '')
                        ]
                    );
                }
            }

        }


        //Implement parent
        function admin_post_tool_action(): void
        {
            /* prepare value */
            $valueArray = [];
            foreach (self::FEATURES as $featureCategory)
                foreach ($featureCategory['features'] ?? [] as $featureKey => $featureData)
                    $valueArray[$featureKey] = isset($_POST[self::OPTION_KEY][$featureKey]) &&
                        $_POST[self::OPTION_KEY][$featureKey] == 'on';

            if (!oes_option_exists(self::OPTION_KEY))
                add_option(self::OPTION_KEY, json_encode($valueArray));
            else update_option(self::OPTION_KEY, json_encode($valueArray));
        }
    }

    // initialize
    register_tool('\OES\Admin\Tools\Legacy_Features', 'legacy-features');

endif;