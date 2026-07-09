<?php
defined( 'ABSPATH' ) || exit;

class RLWC_Plugin {

	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		$this->load();
		$this->hooks();
	}

	private function load() {
		require_once RLWC_PLUGIN_DIR . 'includes/class-database.php';
		require_once RLWC_PLUGIN_DIR . 'includes/class-settings.php';
		require_once RLWC_PLUGIN_DIR . 'includes/class-license.php';
		require_once RLWC_PLUGIN_DIR . 'includes/class-hours.php';
		require_once RLWC_PLUGIN_DIR . 'includes/class-messages.php';
		require_once RLWC_PLUGIN_DIR . 'includes/class-routing.php';
		require_once RLWC_PLUGIN_DIR . 'includes/class-tracking.php';
		require_once RLWC_PLUGIN_DIR . 'includes/class-frontend.php';
		require_once RLWC_PLUGIN_DIR . 'includes/frontend/class-widget.php';
		require_once RLWC_PLUGIN_DIR . 'includes/blocks/class-gutenberg.php';

		if ( did_action( 'elementor/loaded' ) || class_exists( '\Elementor\Plugin' ) ) {
			require_once RLWC_PLUGIN_DIR . 'includes/blocks/class-elementor.php';
		}

		if ( is_admin() ) {
			require_once RLWC_PLUGIN_DIR . 'includes/admin/class-admin.php';
		}
	}

	private function hooks() {
		add_action( 'plugins_loaded', array( $this, 'upgrade_db' ) );
		RLWC_License::init();
		RLWC_Tracking::init();
		RLWC_Widget::init();
		RLWC_Gutenberg::init();

		add_action( 'plugins_loaded', array( $this, 'init_elementor' ), 20 );

		if ( is_admin() ) {
			RLWC_Admin::init();
		}
	}

	public function upgrade_db() {
		if ( get_option( 'rlwc_db_version' ) !== RLWC_VERSION ) {
			RLWC_Database::create_tables();
			RLWC_Settings::seed_defaults();
			RLWC_Settings::ensure_default_agents();
			update_option( 'rlwc_db_version', RLWC_VERSION );
		}
	}

	public function init_elementor() {
		if ( class_exists( '\Elementor\Plugin' ) ) {
			RLWC_Elementor::init();
		}
	}
}
