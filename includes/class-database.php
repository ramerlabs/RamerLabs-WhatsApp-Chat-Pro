<?php
defined( 'ABSPATH' ) || exit;

class RLWC_Database {

	public static function table_name() {
		global $wpdb;
		return $wpdb->prefix . 'rlwc_clicks';
	}

	public static function create_tables() {
		global $wpdb;

		$table   = self::table_name();
		$charset = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE {$table} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			created_at datetime NOT NULL,
			session_id varchar(64) NOT NULL DEFAULT '',
			page_url text NOT NULL,
			page_title varchar(255) NOT NULL DEFAULT '',
			agent_id varchar(64) NOT NULL DEFAULT '',
			agent_name varchar(120) NOT NULL DEFAULT '',
			department varchar(64) NOT NULL DEFAULT '',
			rule_id varchar(64) NOT NULL DEFAULT '',
			utm_source varchar(120) NOT NULL DEFAULT '',
			utm_medium varchar(120) NOT NULL DEFAULT '',
			utm_campaign varchar(120) NOT NULL DEFAULT '',
			referrer text NOT NULL,
			user_agent varchar(255) NOT NULL DEFAULT '',
			consent_given tinyint(1) NOT NULL DEFAULT 0,
			PRIMARY KEY  (id),
			KEY created_at (created_at),
			KEY agent_id (agent_id),
			KEY department (department)
		) {$charset};";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}
}
