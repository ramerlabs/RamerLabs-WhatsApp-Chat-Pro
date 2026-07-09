<?php
defined( 'ABSPATH' ) || exit;

class RLWC_Elementor {

	public static function init() {
		add_action( 'elementor/widgets/register', array( __CLASS__, 'register_widget' ) );
	}

	public static function register_widget( $widgets_manager ) {
		require_once RLWC_PLUGIN_DIR . 'includes/blocks/class-elementor-widget.php';
		$widgets_manager->register( new RLWC_Elementor_Widget() );
	}
}
