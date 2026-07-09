<?php
defined( 'ABSPATH' ) || exit;

class RLWC_Activator {

	public static function activate() {
		require_once RLWC_PLUGIN_DIR . 'includes/class-database.php';
		require_once RLWC_PLUGIN_DIR . 'includes/class-settings.php';

		RLWC_Database::create_tables();
		RLWC_Settings::seed_defaults();
		RLWC_Settings::ensure_default_agents();
		update_option( 'rlwc_db_version', RLWC_VERSION );
	}

	public static function deactivate() {
		// Keep data on deactivate.
	}
}
