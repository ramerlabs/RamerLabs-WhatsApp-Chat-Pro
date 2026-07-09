<?php
/**
 * RamerLabs branding defaults for client plugins and themes.
 */
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'RamerLabs_Branding' ) ) {

	class RamerLabs_Branding {

		const COMPANY_URL    = 'https://ramerlabs.com';
		const COMPANY_NAME   = 'RamerLabs';
		const COMPANY_EMAIL  = 'support@ramerlabs.com';
		const LICENSE_SERVER = 'https://ramerlabs.com';

		public static function company_url() {
			return apply_filters( 'ramerlabs_company_url', self::COMPANY_URL );
		}

		public static function license_server() {
			return apply_filters( 'ramerlabs_license_server', self::LICENSE_SERVER );
		}

		public static function enqueue_client_assets() {
			$css = dirname( __FILE__ ) . '/assets/rlm-client-saas.css';
			if ( ! file_exists( $css ) ) {
				return;
			}

			wp_enqueue_style(
				'ramerlabs-client-saas',
				plugins_url( 'assets/rlm-client-saas.css', __FILE__ ),
				array(),
				'1.0.0'
			);
		}
	}
}
