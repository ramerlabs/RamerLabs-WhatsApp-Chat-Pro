<?php
defined( 'ABSPATH' ) || exit;

class RLWC_Settings {

	const OPTION_KEY  = 'rlwc_settings';
	const AGENTS_KEY  = 'rlwc_agents';
	const RULES_KEY   = 'rlwc_routing_rules';

	public static function defaults() {
		return array(
			'enabled'           => true,
			'default_agent_id'  => '',
			'default_country_code' => '',
			'button_text'       => __( 'Chat on WhatsApp', 'ramerlabs-whatsapp-chat-pro' ),
			'button_position'   => 'bottom-right',
			'button_color'      => '#25D366',
			'button_icon'       => 'whatsapp',
			'show_on_mobile'    => true,
			'show_on_desktop'   => true,
			'z_index'           => 99999,
			'gdpr_enabled'      => true,
			'gdpr_title'        => __( 'Before we connect you on WhatsApp', 'ramerlabs-whatsapp-chat-pro' ),
			'gdpr_message'      => __( 'By continuing, you agree to start a WhatsApp conversation with us. We may store this interaction to improve support.', 'ramerlabs-whatsapp-chat-pro' ),
			'gdpr_button'       => __( 'Continue to WhatsApp', 'ramerlabs-whatsapp-chat-pro' ),
			'gdpr_privacy_url'  => '',
			'offline_message'   => __( 'We typically reply within {hours} hours.', 'ramerlabs-whatsapp-chat-pro' ),
			'online_message'    => __( 'We are online — chat now!', 'ramerlabs-whatsapp-chat-pro' ),
			'timezone'          => '',
			'business_hours'    => self::default_business_hours(),
			'message_templates' => array(
				'default' => __( 'Hi! I have a question about {page_title} on {site_name}.', 'ramerlabs-whatsapp-chat-pro' ),
				'product' => __( 'Hi! I am interested in {product_name} ({product_price}) — {page_url}', 'ramerlabs-whatsapp-chat-pro' ),
				'post'    => __( 'Hi! I read "{post_title}" and have a question.', 'ramerlabs-whatsapp-chat-pro' ),
				'page'    => __( 'Hi! I need help with {page_title}.', 'ramerlabs-whatsapp-chat-pro' ),
			),
			'append_utm'        => true,
			'utm_source'        => 'website',
			'utm_medium'        => 'whatsapp_widget',
			'utm_campaign'      => 'chat_button',
			'exclude_urls'      => '',
			'exclude_post_types'=> array(),
		);
	}

	public static function default_business_hours() {
		$days = array( 'mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun' );
		$hours = array();
		foreach ( $days as $day ) {
			$weekday = in_array( $day, array( 'sat', 'sun' ), true );
			$hours[ $day ] = array(
				'enabled' => ! $weekday,
				'start'   => '09:00',
				'end'     => '17:00',
			);
		}
		return $hours;
	}

	public static function ensure_default_agents() {
		$agents = self::get_agents();
		$ids    = wp_list_pluck( $agents, 'id' );

		if ( ! in_array( 'agent_billing', $ids, true ) ) {
			$agents[] = array(
				'id'         => 'agent_billing',
				'name'       => __( 'Billing Team', 'ramerlabs-whatsapp-chat-pro' ),
				'phone'      => '',
				'department' => 'billing',
				'enabled'    => true,
			);
			update_option( self::AGENTS_KEY, $agents );
		}
	}

	public static function seed_defaults() {
		if ( false === get_option( self::OPTION_KEY, false ) ) {
			update_option( self::OPTION_KEY, self::defaults() );
		}
		if ( false === get_option( self::AGENTS_KEY, false ) ) {
			update_option( self::AGENTS_KEY, array(
				array(
					'id'         => 'agent_sales',
					'name'       => __( 'Sales Team', 'ramerlabs-whatsapp-chat-pro' ),
					'phone'      => '',
					'department' => 'sales',
					'enabled'    => true,
				),
				array(
					'id'         => 'agent_support',
					'name'       => __( 'Support Team', 'ramerlabs-whatsapp-chat-pro' ),
					'phone'      => '',
					'department' => 'support',
					'enabled'    => true,
				),
				array(
					'id'         => 'agent_billing',
					'name'       => __( 'Billing Team', 'ramerlabs-whatsapp-chat-pro' ),
					'phone'      => '',
					'department' => 'billing',
					'enabled'    => true,
				),
			) );
		}
		if ( false === get_option( self::RULES_KEY, false ) ) {
			update_option( self::RULES_KEY, array(
				array(
					'id'         => 'rule_shop',
					'name'       => __( 'WooCommerce shop', 'ramerlabs-whatsapp-chat-pro' ),
					'match_type' => 'woocommerce',
					'match_value'=> 'shop',
					'department' => 'sales',
					'agent_ids'  => array(),
					'priority'   => 10,
					'enabled'    => true,
				),
				array(
					'id'         => 'rule_billing',
					'name'       => __( 'Billing & checkout', 'ramerlabs-whatsapp-chat-pro' ),
					'match_type' => 'url_contains',
					'match_value'=> 'checkout,cart,my-account',
					'department' => 'billing',
					'agent_ids'  => array(),
					'priority'   => 20,
					'enabled'    => true,
				),
				array(
					'id'         => 'rule_support',
					'name'       => __( 'Support pages', 'ramerlabs-whatsapp-chat-pro' ),
					'match_type' => 'url_contains',
					'match_value'=> 'support,contact,help,faq',
					'department' => 'support',
					'agent_ids'  => array(),
					'priority'   => 30,
					'enabled'    => true,
				),
			) );
		}
	}

	public static function get() {
		return wp_parse_args( get_option( self::OPTION_KEY, array() ), self::defaults() );
	}

	public static function save( $settings ) {
		// Unchecked HTML checkboxes are omitted from POST — set explicit false before merge.
		foreach ( array( 'enabled', 'show_on_mobile', 'show_on_desktop', 'gdpr_enabled', 'append_utm' ) as $key ) {
			$settings[ $key ] = ! empty( $settings[ $key ] );
		}

		if ( isset( $settings['business_hours'] ) && is_array( $settings['business_hours'] ) ) {
			foreach ( array_keys( self::default_business_hours() ) as $day ) {
				if ( ! isset( $settings['business_hours'][ $day ] ) || ! is_array( $settings['business_hours'][ $day ] ) ) {
					$settings['business_hours'][ $day ] = array();
				}
				$settings['business_hours'][ $day ]['enabled'] = ! empty( $settings['business_hours'][ $day ]['enabled'] );
			}
		}

		$current   = self::get();
		$merged    = array_merge( $current, $settings );
		$sanitized = self::sanitize( $merged );
		update_option( self::OPTION_KEY, $sanitized );
		return $sanitized;
	}

	public static function sanitize( $settings ) {
		$defaults = self::defaults();
		$out      = array();

		$out['enabled']          = ! empty( $settings['enabled'] );
		$out['default_agent_id'] = sanitize_key( $settings['default_agent_id'] ?? '' );
		$out['default_country_code'] = preg_replace( '/[^0-9]/', '', $settings['default_country_code'] ?? '' );
		$out['button_text']      = sanitize_text_field( $settings['button_text'] ?? $defaults['button_text'] );
		$out['button_position']  = in_array( $settings['button_position'] ?? '', array( 'bottom-right', 'bottom-left' ), true )
			? $settings['button_position']
			: 'bottom-right';
		$out['button_color']     = sanitize_hex_color( $settings['button_color'] ?? '#25D366' ) ?: '#25D366';
		$out['button_icon']      = 'whatsapp';
		$out['show_on_mobile']   = ! empty( $settings['show_on_mobile'] );
		$out['show_on_desktop']  = ! empty( $settings['show_on_desktop'] );
		$out['z_index']          = max( 1, absint( $settings['z_index'] ?? 99999 ) );
		$out['gdpr_enabled']     = ! empty( $settings['gdpr_enabled'] );
		$out['gdpr_title']       = sanitize_text_field( $settings['gdpr_title'] ?? '' );
		$out['gdpr_message']     = sanitize_textarea_field( $settings['gdpr_message'] ?? '' );
		$out['gdpr_button']      = sanitize_text_field( $settings['gdpr_button'] ?? '' );
		$out['gdpr_privacy_url'] = esc_url_raw( $settings['gdpr_privacy_url'] ?? '' );
		$out['offline_message']  = sanitize_text_field( $settings['offline_message'] ?? '' );
		$out['online_message']   = sanitize_text_field( $settings['online_message'] ?? '' );
		$out['timezone']         = sanitize_text_field( $settings['timezone'] ?? '' );
		$out['append_utm']       = ! empty( $settings['append_utm'] );
		$out['utm_source']       = sanitize_key( $settings['utm_source'] ?? 'website' );
		$out['utm_medium']       = sanitize_key( $settings['utm_medium'] ?? 'whatsapp_widget' );
		$out['utm_campaign']     = sanitize_key( $settings['utm_campaign'] ?? 'chat_button' );
		$out['exclude_urls']     = sanitize_textarea_field( $settings['exclude_urls'] ?? '' );

		$out['exclude_post_types'] = array();
		if ( ! empty( $settings['exclude_post_types'] ) && is_array( $settings['exclude_post_types'] ) ) {
			$out['exclude_post_types'] = array_map( 'sanitize_key', $settings['exclude_post_types'] );
		}

		$out['business_hours'] = array();
		$default_hours         = self::default_business_hours();
		foreach ( $default_hours as $day => $default_day ) {
			$incoming = $settings['business_hours'][ $day ] ?? array();
			$out['business_hours'][ $day ] = array(
				'enabled' => ! empty( $incoming['enabled'] ),
				'start'   => self::sanitize_time( $incoming['start'] ?? $default_day['start'] ),
				'end'     => self::sanitize_time( $incoming['end'] ?? $default_day['end'] ),
			);
		}

		$out['message_templates'] = array();
		foreach ( $defaults['message_templates'] as $key => $default_template ) {
			$out['message_templates'][ $key ] = sanitize_textarea_field(
				$settings['message_templates'][ $key ] ?? $default_template
			);
		}

		return $out;
	}

	private static function sanitize_time( $time ) {
		$time = sanitize_text_field( $time );
		return preg_match( '/^\d{2}:\d{2}$/', $time ) ? $time : '09:00';
	}

	public static function get_agents() {
		$agents = get_option( self::AGENTS_KEY, array() );
		return is_array( $agents ) ? $agents : array();
	}

	public static function save_agents( $agents ) {
		$sanitized = array();
		foreach ( (array) $agents as $agent ) {
			if ( empty( $agent['phone'] ) && empty( $agent['name'] ) ) {
				continue;
			}
			$id = ! empty( $agent['id'] ) ? sanitize_key( $agent['id'] ) : 'agent_' . wp_generate_password( 8, false, false );
			$phone = preg_replace( '/[^0-9+]/', '', $agent['phone'] ?? '' );
			$phone = RLWC_Messages::normalize_phone( $phone, self::get()['default_country_code'] );
			$sanitized[] = array(
				'id'         => $id,
				'name'       => sanitize_text_field( $agent['name'] ?? '' ),
				'phone'      => $phone,
				'department' => sanitize_key( $agent['department'] ?? 'general' ),
				'enabled'    => ! empty( $agent['enabled'] ),
			);
		}
		update_option( self::AGENTS_KEY, $sanitized );
		return $sanitized;
	}

	public static function get_agent_by_id( $agent_id ) {
		foreach ( self::get_agents() as $agent ) {
			if ( $agent['id'] === $agent_id ) {
				return $agent;
			}
		}
		return null;
	}

	public static function get_rules() {
		$rules = get_option( self::RULES_KEY, array() );
		if ( ! is_array( $rules ) ) {
			return array();
		}
		usort(
			$rules,
			static function ( $a, $b ) {
				return (int) ( $a['priority'] ?? 0 ) <=> (int) ( $b['priority'] ?? 0 );
			}
		);
		return $rules;
	}

	public static function save_rules( $rules ) {
		$sanitized = array();
		foreach ( (array) $rules as $rule ) {
			if ( empty( $rule['name'] ) ) {
				continue;
			}
			$id = ! empty( $rule['id'] ) ? sanitize_key( $rule['id'] ) : 'rule_' . wp_generate_password( 8, false, false );
			$agent_ids = array();
			if ( ! empty( $rule['agent_ids'] ) ) {
				if ( is_array( $rule['agent_ids'] ) ) {
					$agent_ids = array_map( 'sanitize_key', $rule['agent_ids'] );
				} else {
					$agent_ids = array_map( 'trim', explode( ',', sanitize_text_field( $rule['agent_ids'] ) ) );
					$agent_ids = array_filter( array_map( 'sanitize_key', $agent_ids ) );
				}
			}
			$sanitized[] = array(
				'id'          => $id,
				'name'        => sanitize_text_field( $rule['name'] ),
				'match_type'  => sanitize_key( $rule['match_type'] ?? 'url_contains' ),
				'match_value' => sanitize_text_field( $rule['match_value'] ?? '' ),
				'department'  => sanitize_key( $rule['department'] ?? 'general' ),
				'agent_ids'   => $agent_ids,
				'priority'    => absint( $rule['priority'] ?? 10 ),
				'enabled'     => ! empty( $rule['enabled'] ),
			);
		}
		update_option( self::RULES_KEY, $sanitized );
		return $sanitized;
	}
}
