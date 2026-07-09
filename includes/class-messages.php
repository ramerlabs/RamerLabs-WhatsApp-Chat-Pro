<?php
defined( 'ABSPATH' ) || exit;

class RLWC_Messages {

	public static function build_for_context( $context = array() ) {
		$settings = RLWC_Settings::get();
		$type     = $context['type'] ?? 'default';
		$template = $settings['message_templates'][ $type ] ?? $settings['message_templates']['default'];

		$replacements = self::replacement_map( $context );
		$message      = str_replace( array_keys( $replacements ), array_values( $replacements ), $template );

		return trim( preg_replace( '/\s+/', ' ', $message ) );
	}

	public static function detect_context_type() {
		if ( function_exists( 'is_product' ) && is_product() ) {
			return 'product';
		}
		if ( is_singular( 'post' ) ) {
			return 'post';
		}
		if ( is_page() ) {
			return 'page';
		}
		return 'default';
	}

	public static function current_context() {
		$type = self::detect_context_type();
		$ctx  = array(
			'type'       => $type,
			'page_title' => wp_get_document_title(),
			'page_url'   => self::current_url(),
			'site_name'  => get_bloginfo( 'name' ),
		);

		if ( 'product' === $type && function_exists( 'wc_get_product' ) ) {
			$product = wc_get_product( get_the_ID() );
			if ( $product ) {
				$ctx['product_name']  = $product->get_name();
				$ctx['product_price'] = wp_strip_all_tags( wc_price( $product->get_price() ) );
				$ctx['product_sku']   = $product->get_sku();
			}
		}

		if ( in_array( $type, array( 'post', 'page' ), true ) ) {
			$ctx['post_title'] = get_the_title();
		}

		return $ctx;
	}

	private static function replacement_map( $context ) {
		$map = array(
			'{page_title}'   => $context['page_title'] ?? '',
			'{page_url}'     => $context['page_url'] ?? '',
			'{site_name}'    => $context['site_name'] ?? get_bloginfo( 'name' ),
			'{post_title}'   => $context['post_title'] ?? ( $context['page_title'] ?? '' ),
			'{product_name}' => $context['product_name'] ?? '',
			'{product_price}'=> $context['product_price'] ?? '',
			'{product_sku}'  => $context['product_sku'] ?? '',
		);
		return $map;
	}

	public static function current_url() {
		if ( is_singular() ) {
			return get_permalink();
		}
		return home_url( add_query_arg( array(), $GLOBALS['wp']->request ?? '' ) );
	}

	public static function normalize_phone( $phone, $country_code = '' ) {
		$phone = preg_replace( '/[^0-9]/', '', (string) $phone );
		$country_code = preg_replace( '/[^0-9]/', '', (string) $country_code );

		if ( '' === $phone ) {
			return '';
		}

		if ( '' !== $country_code && '0' === $phone[0] ) {
			$phone = $country_code . substr( $phone, 1 );
		}

		return $phone;
	}

	public static function whatsapp_url( $phone, $message ) {
		$settings = RLWC_Settings::get();
		$phone    = self::normalize_phone( $phone, $settings['default_country_code'] ?? '' );

		if ( '' === $phone ) {
			return '';
		}

		return add_query_arg(
			array(
				'phone' => $phone,
				'text'  => $message,
			),
			'https://api.whatsapp.com/send'
		);
	}
}
