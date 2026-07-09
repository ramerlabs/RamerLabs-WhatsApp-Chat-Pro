<?php
/**
 * RamerLabs internal license server — not shown in customer-facing UI.
 */
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'rlm_internal_license_server' ) ) {
	/**
	 * @return string
	 */
	function rlm_internal_license_server() {
		$parts = array( 'https://', 'rame', 'rlabs', '.com' );

		return untrailingslashit( implode( '', $parts ) );
	}
}
