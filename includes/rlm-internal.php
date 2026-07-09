<?php
defined( 'ABSPATH' ) || exit;

function rlwc_internal_license_server() {
	$parts = array( 'https://', 'rame', 'rlabs', '.com' );
	return untrailingslashit( implode( '', $parts ) );
}
