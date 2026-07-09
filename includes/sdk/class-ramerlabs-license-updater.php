<?php
/**
 * RamerLabs License Updater — gates plugin updates behind active license.
 */
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'RamerLabs_License_Updater' ) ) {

	class RamerLabs_License_Updater {

		private $client;
		private $plugin_file;
		private $plugin_slug;

		public function __construct( $client, $plugin_file ) {
			$this->client       = $client;
			$this->plugin_file  = $plugin_file;
			$this->plugin_slug  = dirname( plugin_basename( $plugin_file ) );
		}

		public function register() {
			add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_for_update' ) );
			add_filter( 'plugins_api', array( $this, 'plugin_info' ), 10, 3 );
		}

		public function check_for_update( $transient ) {
			if ( empty( $transient->checked[ $this->plugin_file ] ) ) {
				return $transient;
			}

			if ( ! $this->client->is_valid() ) {
				return $transient;
			}

			$response = $this->client->request(
				'check-update',
				array(
					'current_version' => $transient->checked[ $this->plugin_file ],
				)
			);

			if ( is_wp_error( $response ) || empty( $response['data']['update_available'] ) ) {
				return $transient;
			}

			$data = $response['data'];
			$obj  = (object) array(
				'slug'        => $this->plugin_slug,
				'plugin'      => $this->plugin_file,
				'new_version' => $data['version'],
				'url'         => RamerLabs_Branding::company_url(),
				'package'     => $data['package_url'] ?? '',
				'tested'      => $data['tested_wp'] ?? '',
				'requires'    => $data['requires_wp'] ?? '',
				'requires_php'=> $data['requires_php'] ?? '',
			);

			$transient->response[ $this->plugin_file ] = $obj;
			return $transient;
		}

		public function plugin_info( $result, $action, $args ) {
			if ( 'plugin_information' !== $action || empty( $args->slug ) || $args->slug !== $this->plugin_slug ) {
				return $result;
			}

			$response = $this->client->request( 'check-update', array( 'current_version' => '0.0.0' ) );
			if ( is_wp_error( $response ) || empty( $response['data']['update_available'] ) ) {
				return $result;
			}

			$data = $response['data'];
			return (object) array(
				'name'          => $args->slug,
				'slug'          => $this->plugin_slug,
				'version'       => $data['version'],
				'author'        => '<a href="' . esc_url( RamerLabs_Branding::company_url() ) . '">RamerLabs</a>',
				'homepage'      => RamerLabs_Branding::company_url(),
				'download_link' => $data['package_url'] ?? '',
				'sections'      => array(
					'description' => __( 'RamerLabs Membership Pro.', 'ramerlabs-membership-pro' ),
					'changelog'   => $data['changelog'] ?? '',
				),
			);
		}
	}
}
