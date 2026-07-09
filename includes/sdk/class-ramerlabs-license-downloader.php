<?php
/**
 * RamerLabs License Downloader — vault file access for licensed products.
 */
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'RamerLabs_License_Downloader' ) ) {

	class RamerLabs_License_Downloader {

		private $client;

		public function __construct( $client ) {
			$this->client = $client;
		}

		public function list_files() {
			$response = $this->client->request( 'downloads' );
			if ( is_wp_error( $response ) || empty( $response['data'] ) ) {
				return array();
			}
			return $response['data'];
		}

		public function get_download_link( $file_id ) {
			$response = $this->client->request(
				'request-download',
				array( 'file_id' => absint( $file_id ) )
			);

			if ( is_wp_error( $response ) || empty( $response['data']['url'] ) ) {
				return $response;
			}

			return $response['data']['url'];
		}
	}
}
