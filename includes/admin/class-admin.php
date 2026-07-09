<?php
defined( 'ABSPATH' ) || exit;

class RLWC_Admin {

	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'menu' ), 5 );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'assets' ) );
	}

	public static function assets( $hook ) {
		if ( false === strpos( $hook, 'rlwc-' ) && false === strpos( $hook, 'rlwc_' ) ) {
			if ( empty( $_GET['page'] ) || 0 !== strpos( sanitize_key( $_GET['page'] ), 'rlwc' ) ) {
				return;
			}
		}
		wp_enqueue_style( 'rlwc-admin', RLWC_PLUGIN_URL . 'assets/css/admin.css', array(), RLWC_VERSION );
		if ( class_exists( 'RamerLabs_Branding' ) ) {
			RamerLabs_Branding::enqueue_client_assets();
		}
	}

	public static function menu() {
		add_menu_page(
			__( 'WhatsApp Chat Pro', 'ramerlabs-whatsapp-chat-pro' ),
			__( 'WhatsApp Chat', 'ramerlabs-whatsapp-chat-pro' ),
			'manage_options',
			'rlwc-dashboard',
			array( __CLASS__, 'dashboard' ),
			'dashicons-whatsapp',
			58
		);
		add_submenu_page( 'rlwc-dashboard', __( 'Settings', 'ramerlabs-whatsapp-chat-pro' ), __( 'Settings', 'ramerlabs-whatsapp-chat-pro' ), 'manage_options', 'rlwc-settings', array( __CLASS__, 'settings_page' ) );
		add_submenu_page( 'rlwc-dashboard', __( 'Agents', 'ramerlabs-whatsapp-chat-pro' ), __( 'Agents', 'ramerlabs-whatsapp-chat-pro' ), 'manage_options', 'rlwc-agents', array( __CLASS__, 'agents_page' ) );
		add_submenu_page( 'rlwc-dashboard', __( 'Routing Rules', 'ramerlabs-whatsapp-chat-pro' ), __( 'Routing', 'ramerlabs-whatsapp-chat-pro' ), 'manage_options', 'rlwc-routing', array( __CLASS__, 'routing_page' ) );
		add_submenu_page( 'rlwc-dashboard', __( 'Analytics', 'ramerlabs-whatsapp-chat-pro' ), __( 'Analytics', 'ramerlabs-whatsapp-chat-pro' ), 'manage_options', 'rlwc-analytics', array( __CLASS__, 'analytics_page' ) );
	}

	public static function wrap_start( $title, $subtitle = '' ) {
		echo '<div class="wrap rlwc-admin-wrap">';
		echo '<div class="rlwc-admin-hero"><h1>' . esc_html( $title ) . '</h1>';
		if ( $subtitle ) {
			echo '<p>' . esc_html( $subtitle ) . '</p>';
		}
		echo '</div>';
	}

	public static function wrap_end() {
		echo '</div>';
	}

	public static function dashboard() {
		$licensed = RLWC_License::is_valid();
		$settings = RLWC_Settings::get();
		$agents   = RLWC_Settings::get_agents();
		$stats    = RLWC_Tracking::get_stats( 7 );

		self::wrap_start(
			__( 'WhatsApp Chat Pro', 'ramerlabs-whatsapp-chat-pro' ),
			__( 'Smart routing, business hours, GDPR consent, and click analytics.', 'ramerlabs-whatsapp-chat-pro' )
		);

		echo '<div class="rlwc-admin-card">';
		echo $licensed
			? '<p><span class="rlwc-badge">' . esc_html__( 'License active', 'ramerlabs-whatsapp-chat-pro' ) . '</span></p>'
			: '<p>' . esc_html__( 'Activate your license to receive updates.', 'ramerlabs-whatsapp-chat-pro' ) . ' <a href="' . esc_url( admin_url( 'admin.php?page=rlwc-license' ) ) . '">' . esc_html__( 'Enter license key', 'ramerlabs-whatsapp-chat-pro' ) . '</a></p>';

		echo '<div class="rlwc-admin-grid">';
		echo '<div class="rlwc-stat"><strong>' . (int) $stats['total'] . '</strong><span>' . esc_html__( 'Clicks (7 days)', 'ramerlabs-whatsapp-chat-pro' ) . '</span></div>';
		echo '<div class="rlwc-stat"><strong>' . count( $agents ) . '</strong><span>' . esc_html__( 'Agents', 'ramerlabs-whatsapp-chat-pro' ) . '</span></div>';
		echo '<div class="rlwc-stat"><strong>' . ( $settings['enabled'] ? esc_html__( 'On', 'ramerlabs-whatsapp-chat-pro' ) : esc_html__( 'Off', 'ramerlabs-whatsapp-chat-pro' ) ) . '</strong><span>' . esc_html__( 'Widget status', 'ramerlabs-whatsapp-chat-pro' ) . '</span></div>';
		echo '</div></div>';

		echo '<div class="rlwc-admin-card"><h2>' . esc_html__( 'Quick setup', 'ramerlabs-whatsapp-chat-pro' ) . '</h2><ol>';
		echo '<li><a href="' . esc_url( admin_url( 'admin.php?page=rlwc-agents' ) ) . '">' . esc_html__( 'Add agent phone numbers', 'ramerlabs-whatsapp-chat-pro' ) . '</a></li>';
		echo '<li><a href="' . esc_url( admin_url( 'admin.php?page=rlwc-routing' ) ) . '">' . esc_html__( 'Configure page routing rules', 'ramerlabs-whatsapp-chat-pro' ) . '</a></li>';
		echo '<li><a href="' . esc_url( admin_url( 'admin.php?page=rlwc-settings' ) ) . '">' . esc_html__( 'Set business hours & GDPR text', 'ramerlabs-whatsapp-chat-pro' ) . '</a></li>';
		echo '</ol></div>';

		self::wrap_end();
	}

	public static function settings_page() {
		if ( isset( $_POST['rlwc_save_settings'] ) && check_admin_referer( 'rlwc_settings' ) ) {
			$incoming = wp_unslash( $_POST['rlwc'] ?? array() );
			RLWC_Settings::save( $incoming );
			echo '<div class="updated"><p>' . esc_html__( 'Settings saved.', 'ramerlabs-whatsapp-chat-pro' ) . '</p></div>';
		}

		$settings = RLWC_Settings::get();
		self::wrap_start( __( 'Widget Settings', 'ramerlabs-whatsapp-chat-pro' ) );
		include RLWC_PLUGIN_DIR . 'includes/admin/views/settings.php';
		self::wrap_end();
	}

	public static function agents_page() {
		if ( isset( $_POST['rlwc_save_agents'] ) && check_admin_referer( 'rlwc_agents' ) ) {
			RLWC_Settings::save_agents( wp_unslash( $_POST['agents'] ?? array() ) );
			echo '<div class="updated"><p>' . esc_html__( 'Agents saved.', 'ramerlabs-whatsapp-chat-pro' ) . '</p></div>';
		}

		$agents = RLWC_Settings::get_agents();
		self::wrap_start( __( 'WhatsApp Agents', 'ramerlabs-whatsapp-chat-pro' ), __( 'Add team members with phone numbers. Round-robin distributes chats across agents in the same department.', 'ramerlabs-whatsapp-chat-pro' ) );
		include RLWC_PLUGIN_DIR . 'includes/admin/views/agents.php';
		self::wrap_end();
	}

	public static function routing_page() {
		if ( isset( $_POST['rlwc_save_rules'] ) && check_admin_referer( 'rlwc_rules' ) ) {
			RLWC_Settings::save_rules( wp_unslash( $_POST['rules'] ?? array() ) );
			echo '<div class="updated"><p>' . esc_html__( 'Routing rules saved.', 'ramerlabs-whatsapp-chat-pro' ) . '</p></div>';
		}

		$rules  = RLWC_Settings::get_rules();
		$agents = RLWC_Settings::get_agents();
		self::wrap_start( __( 'Page Routing', 'ramerlabs-whatsapp-chat-pro' ), __( 'Route visitors to sales, support, or billing based on the page they are on.', 'ramerlabs-whatsapp-chat-pro' ) );
		include RLWC_PLUGIN_DIR . 'includes/admin/views/routing.php';
		self::wrap_end();
	}

	public static function analytics_page() {
		$days  = isset( $_GET['days'] ) ? absint( $_GET['days'] ) : 30;
		$stats = RLWC_Tracking::get_stats( $days );

		self::wrap_start( __( 'Click Analytics', 'ramerlabs-whatsapp-chat-pro' ), __( 'Track WhatsApp button clicks with UTM parameters and department routing.', 'ramerlabs-whatsapp-chat-pro' ) );
		include RLWC_PLUGIN_DIR . 'includes/admin/views/analytics.php';
		self::wrap_end();
	}
}
