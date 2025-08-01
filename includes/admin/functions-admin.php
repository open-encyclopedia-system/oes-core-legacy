<?php

namespace OES\Legacy;

function admin_menu_pages(array $adminMenuPages): array
{
    $adminMenuPages['055_legacy'] = [
        'subpage' => true,
        'page_parameters' => [
            'page_title' => 'Legacy',
            'menu_title' => 'Legacy',
            'menu_slug' => 'oes_settings_legacy',
            'position' => 35,
            'parent_slug' => 'oes_settings'
        ],
        'view_file_name_full_path' => (__DIR__ . '/views/view-settings-legacy.php'),
        'is_core_page' => true
    ];

    /* get options where feature options are stored */
    $features = get_option('oes_legacy_features');
    if ($features) $features = json_decode($features, true);

    if (!$features || ($features['update'] ?? false))
        $adminMenuPages['260_tools_update'] = [
            'subpage' => true,
            'page_parameters' => [
                'page_title' => 'Update',
                'menu_title' => 'Update',
                'parent_slug' => 'oes_tools',
                'menu_slug' => 'oes_tools_update',
                'position' => 9
            ],
            'view_file_name_full_path' => (__DIR__ . '/views/view-tools-update.php'),
            'is_core_page' => true
        ];

    if (!$features || ($features['delete'] ?? false))
        $adminMenuPages['270_tools_delete'] = [
            'subpage' => true,
            'page_parameters' => [
                'page_title' => 'Delete',
                'menu_title' => 'Delete',
                'parent_slug' => 'oes_tools',
                'menu_slug' => 'oes_tools_delete',
                'position' => 11
            ],
            'view_file_name_full_path' => (__DIR__ . '/views/view-tools-delete.php'),
            'is_core_page' => true
        ];

    if (!$features || ($features['cache'] ?? false))
        $adminMenuPages['290_tools_cache'] = [
            'subpage' => true,
            'page_parameters' => [
                'page_title' => 'Cache',
                'menu_title' => 'Cache',
                'parent_slug' => 'oes_tools',
                'menu_slug' => 'oes_tools_cache',
                'position' => 12
            ],
            'view_file_name_full_path' => (__DIR__ . '/views/view-tools-cache.php'),
            'is_core_page' => true
        ];

    return $adminMenuPages;
}
