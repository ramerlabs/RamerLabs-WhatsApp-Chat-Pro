<?php
/**
 * RamerLabs License Client SDK
 *
 * Drop-in license client for RamerLabs products.
 * Validates license keys remotely — server URL is configured internally.
 *
 * @package RamerLabs_License_Client
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'RamerLabs_License_Client' ) ) {

	class RamerLabs_License_Client {

		const OPTION_KEY = 'ramerlabs_license_data';
		const TRANSIENT_KEY = 'ramerlabs_license_valid';

		private $server_url;
		private $product_slug;
		private $plugin_file;
		private $license_data;

		/**
		 * @param string $server_url   License server URL (e.g. https://yoursite.com).
		 * @param string $product_slug Product identifier for this plugin.
		 * @param string $plugin_file  Main plugin/theme file path for storage key.
		 */
		public function __construct( $server_url, $product_slug, $plugin_file ) {
			$this->server_url   = untrailingslashit( $server_url );
			$this->product_slug = sanitize_title( $product_slug );
			$this->plugin_file  = $plugin_file;
			$this->license_data = get_option( $this->get_option_key(), array() );
		}

		public function get_license_key() {
			return isset( $this->license_data['license_key'] ) ? $this->license_data['license_key'] : '';
		}

		public function set_license_key( $key ) {
			$this->license_data['license_key'] = sanitize_text_field( $key );
			update_option( $this->get_option_key(), $this->license_data );
		}

		public function activate( $license_key = '' ) {
			if ( $license_key ) {
				$this->set_license_key( $license_key );
			}

			$key = $this->get_license_key();
			if ( empty( $key ) ) {
				return new WP_Error( 'missing_key', __( 'License key is required.', 'ramerlabs-membership-pro' ) );
			}

			$response = $this->request( 'activate', array(
				'license_key'  => $key,
				'product_slug' => $this->product_slug,
				'site_url'     => home_url(),
				'site_name'    => get_bloginfo( 'name' ),
			) );

			if ( is_wp_error( $response ) ) {
				return $response;
			}

			if ( ! empty( $response['success'] ) ) {
				$this->license_data['status']      = 'active';
				$this->license_data['activated_at'] = time();
				$this->license_data['data']        = $response['data'] ?? array();
				update_option( $this->get_option_key(), $this->license_data );
				set_transient( $this->get_transient_key(), true, DAY_IN_SECONDS );
			}

			return $response;
		}

		public function deactivate() {
			$key = $this->get_license_key();
			if ( empty( $key ) ) {
				return new WP_Error( 'missing_key', __( 'No license key stored.', 'ramerlabs-membership-pro' ) );
			}

			$response = $this->request( 'deactivate', array(
				'license_key'  => $key,
				'product_slug' => $this->product_slug,
				'site_url'     => home_url(),
			) );

			delete_option( $this->get_option_key() );
			delete_transient( $this->get_transient_key() );
			$this->license_data = array();

			return $response;
		}

		public function validate( $force = false ) {
			if ( ! $force && get_transient( $this->get_transient_key() ) ) {
				return true;
			}

			$key = $this->get_license_key();
			if ( empty( $key ) ) {
				return false;
			}

			$response = $this->request( 'validate', array(
				'license_key'  => $key,
				'product_slug' => $this->product_slug,
				'site_url'     => home_url(),
			) );

			if ( is_wp_error( $response ) || empty( $response['success'] ) ) {
				delete_transient( $this->get_transient_key() );
				return false;
			}

			set_transient( $this->get_transient_key(), true, DAY_IN_SECONDS );
			return true;
		}

		public function is_valid() {
			return (bool) $this->validate();
		}

		public function get_product_slug() {
			return $this->product_slug;
		}

		public function get_license_data() {
			return isset( $this->license_data['data'] ) ? $this->license_data['data'] : array();
		}

		public function get_status() {
			return isset( $this->license_data['status'] ) ? $this->license_data['status'] : 'inactive';
		}

		public function request( $endpoint, $body = array() ) {
			$body = wp_parse_args(
				$body,
				array(
					'license_key'  => $this->get_license_key(),
					'product_slug' => $this->product_slug,
					'site_url'     => home_url(),
				)
			);

			$url = $this->server_url . '/wp-json/ramerlabs-license/v1/' . $endpoint;

			$response = wp_remote_post(
				$url,
				array(
					'timeout' => 15,
					'headers' => array(
						'Content-Type' => 'application/json',
					),
					'body'    => wp_json_encode( $body ),
				)
			);

			if ( is_wp_error( $response ) ) {
				return $response;
			}

			$code = wp_remote_retrieve_response_code( $response );
			$data = json_decode( wp_remote_retrieve_body( $response ), true );

			if ( $code >= 400 || empty( $data ) ) {
				$message = isset( $data['message'] ) ? $data['message'] : __( 'We could not verify your license. Check your key or contact support@ramerlabs.com.', 'ramerlabs-membership-pro' );
				return new WP_Error(
					isset( $data['code'] ) ? $data['code'] : 'license_error',
					$message
				);
			}

			if ( empty( $data['success'] ) ) {
				$message = isset( $data['message'] ) ? $data['message'] : __( 'We could not verify your license. Check your key or contact support@ramerlabs.com.', 'ramerlabs-membership-pro' );
				return new WP_Error(
					isset( $data['code'] ) ? $data['code'] : 'license_error',
					$message
				);
			}

			return $data;
		}

		private function get_option_key() {
			return self::OPTION_KEY . '_' . md5( $this->plugin_file . $this->product_slug );
		}

		private function get_transient_key() {
			return self::TRANSIENT_KEY . '_' . md5( $this->plugin_file . $this->product_slug );
		}
	}
}
