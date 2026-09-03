<?php

/**
 * @wordpress-plugin
 * Plugin Name:        OES Legacy (OES Core Module)
 * Plugin URI:         https://www.open-encyclopedia-system.org/
 * Description:        Enable OES legacy features and functions.
 * Version:            1.1.0
 * Author:             Maren Welterlich-Strobl, Freie Universität Berlin, FUB-IT
 * Author URI:         https://www.it.fu-berlin.de/die-fub-it/mitarbeitende/mstrobl.html
 * Requires at least:  6.5
 * Tested up to:       7.0.0
 * Requires PHP:       8.1
 * Tags:               oes, legacy
 * License:            GPLv2 or later
 * License URI:        https://www.gnu.org/licenses/gpl-2.0.html
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 */

if (!defined('ABSPATH')) exit; // Exit if accessed directly

add_action('oes/plugins_loaded', function () {


    OES(__DIR__);

    // include admin pages
    include_once(__DIR__ . '/includes/admin/functions-admin.php');
    add_filter('oes/admin_menu_pages', '\OES\Legacy\admin_menu_pages');

    // get options where feature options are stored
    $features = get_option('oes_legacy_features');
    if ($features) {
        $features = json_decode($features, true);
    }

    // tools
    include_once(__DIR__ . '/includes/admin/tools/config/class-config-legacy_features.php');

    add_action('admin_init', function () use ($features) {
        if (!$features || ($features['delete'] ?? false))
            include_once(__DIR__ . '/includes/admin/tools/class-tool-delete.php');
        if (!$features || ($features['update'] ?? false))
            include_once(__DIR__ . '/includes/admin/tools/class-tool-update.php');
        if (!$features || ($features['cache'] ?? false)) {
            include_once(__DIR__ . '/includes/admin/tools/cache/class-cache_empty.php');
            include_once(__DIR__ . '/includes/admin/tools/cache/class-cache_update.php');
            //@oesDevelopment include_once(__DIR__ . '/includes/admin/tools/config/class-config-cache_scheduler.php');
        }
    });

    // include cache feature
    if (!$features || ($features['cache'] ?? false)) {
        include_once(__DIR__ . '/includes/admin/db/initialize-db.php');
        include_once(__DIR__ . '/includes/admin/tools/cache/functions-cache.php');
    }

    // include legacy functions
    include_once(__DIR__ . '/includes/functions-legacy.php');
    include_once(__DIR__ . '/includes/functions-misc.php');
    include_once(__DIR__ . '/includes/functions-oes.php');
    add_action('template_redirect', '\OES\Legacy\add_global_nav_language', 99);

    // theme
    include_once __DIR__ . '/includes/theme/functions-data.php';
}, 12);