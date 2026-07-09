<?php
defined( 'ABSPATH' ) || exit;

class RLWC_License {

	private static $client = null;

	public static function init() {
		require_once RLWC_PLUGIN_DIR . 'includes/sdk/class-ramerlabs-license-client.php';
		require_once RLWC_PLUGIN_DIR . 'includes/sdk/class-ramerlabs-license-admin-page.php';
		require_once RLWC_PLUGIN_DIR . 'includes/sdk/rlm-branding.php';
		require_once RLWC_PLUGIN_DIR . 'includes/rlm-internal.php';

		self::$client = new RamerLabs_License_Client(
			rlwc_internal_license_server(),
			RLWC_PRODUCT_SLUG,
			RLWC_PLUGIN_FILE
		);

		if ( is_admin() ) {
			$page = new RamerLabs_License_Admin_Page( self::$client, array(
				'page_title'  => __( 'Activate WhatsApp Chat Pro', 'ramerlabs-whatsapp-chat-pro' ),
				'menu_title'  => __( 'License', 'ramerlabs-whatsapp-chat-pro' ),
				'parent_slug' => 'rlwc-dashboard',
				'menu_slug'   => 'rlwc-license',
			) );
			$page->register();
			add_action( 'admin_notices', array( __CLASS__, 'admin_notice' ) );
		}
	}

	public static function client() {
		return self::$client;
	}

	public static function is_valid() {
		return self::$client && self::$client->is_valid();
	}

	public static function admin_notice() {
		if ( self::is_valid() || ! current_user_can( 'manage_options' ) ) {
			return;
		}
		if ( isset( $_GET['page'] ) && 'rlwc-license' === $_GET['page'] ) {
			return;
		}
		printf(
			'<div class="notice notice-warning"><p>%s <a href="%s">%s</a> — %s <a href="%s" target="_blank">%s</a></p></div>',
			esc_html__( 'WhatsApp Chat Pro is not activated yet.', 'ramerlabs-whatsapp-chat-pro' ),
			esc_url( admin_url( 'admin.php?page=rlwc-license' ) ),
			esc_html__( 'Enter your license key', 'ramerlabs-whatsapp-chat-pro' ),
			esc_html__( 'Need a key?', 'ramerlabs-whatsapp-chat-pro' ),
			esc_url( 'https://ramerlabs.com' ),
			esc_html__( 'Get it at ramerlabs.com', 'ramerlabs-whatsapp-chat-pro' )
		);
	}
}
