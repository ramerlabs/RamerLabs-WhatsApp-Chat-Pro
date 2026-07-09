<?php
defined( 'ABSPATH' ) || exit;

class RLWC_Widget {

	public static function init() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue' ) );
		add_action( 'wp_footer', array( __CLASS__, 'render' ), 99 );
	}

	public static function enqueue() {
		if ( ! RLWC_Routing::should_show_widget() ) {
			return;
		}

		RLWC_Frontend::enqueue_assets();
		RLWC_Frontend::localize_for_route( RLWC_Routing::resolve(), 'floating' );
	}

	public static function render() {
		if ( ! RLWC_Routing::should_show_widget() ) {
			return;
		}

		$settings = RLWC_Settings::get();
		$classes  = array(
			'rlwc-widget',
			'rlwc-widget--' . sanitize_html_class( $settings['button_position'] ),
		);

		if ( empty( $settings['show_on_mobile'] ) ) {
			$classes[] = 'rlwc-widget--hide-mobile';
		}
		if ( empty( $settings['show_on_desktop'] ) ) {
			$classes[] = 'rlwc-widget--hide-desktop';
		}

		include RLWC_PLUGIN_DIR . 'includes/frontend/widget.php';
	}
}
