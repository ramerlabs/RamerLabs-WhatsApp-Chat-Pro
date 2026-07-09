<?php
defined( 'ABSPATH' ) || exit;

class RLWC_Gutenberg {

	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_block' ) );
		add_shortcode( 'rlwc_button', array( __CLASS__, 'shortcode' ) );
	}

	public static function register_block() {
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}

		register_block_type(
			RLWC_PLUGIN_DIR . 'includes/blocks/whatsapp-button',
			array(
				'render_callback' => array( __CLASS__, 'render_block' ),
			)
		);
	}

	public static function render_block( $attributes ) {
		return RLWC_Frontend::render_button(
			array(
				'text'       => $attributes['buttonText'] ?? '',
				'department' => $attributes['department'] ?? '',
				'style'      => $attributes['style'] ?? 'button',
				'show_icon'  => ! empty( $attributes['showIcon'] ),
			)
		);
	}

	public static function shortcode( $atts ) {
		$atts = shortcode_atts(
			array(
				'text'       => '',
				'department' => '',
				'style'      => 'button',
				'icon'       => 'yes',
			),
			$atts,
			'rlwc_button'
		);

		return RLWC_Frontend::render_button(
			array(
				'text'       => $atts['text'],
				'department' => $atts['department'],
				'style'      => $atts['style'],
				'show_icon'  => 'yes' === $atts['icon'],
			)
		);
	}
}
