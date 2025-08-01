<?php

namespace OES\Admin\DB;

if (!defined('ABSPATH')) exit; // Exit if accessed directly


/* prepare table names */
global $wpdb;
$oesTableCacheName = $wpdb->prefix . 'oes_cache';

/* check if oes cache table exists */
if ($wpdb->get_var("SHOW TABLES LIKE '$oesTableCacheName'") != $oesTableCacheName) {

    /* Create new table */
    $charsetCollate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE $oesTableCacheName (
			id bigint(50) NOT NULL AUTO_INCREMENT,
			cache_name varchar(255) NOT NULL,
			cache_value_raw longtext NOT NULL,
			cache_value_html longtext NOT NULL,
			cache_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			cache_sequence text NOT NULL,
			cache_status text NOT NULL,
			cache_comment text NOT NULL,
			cache_temp varchar(50),
			PRIMARY KEY (id)
			) $charsetCollate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}