<?php
defined( 'ABSPATH' ) || exit;

class RLWC_Routing {

	const ROUND_ROBIN_KEY = 'rlwc_round_robin_counters';

	public static function resolve( $department_override = '' ) {
		$settings = RLWC_Settings::get();
		$rules    = RLWC_Settings::get_rules();
		$matched  = null;

		if ( $department_override ) {
			$agent = self::pick_agent( array(), sanitize_key( $department_override ), $settings['default_agent_id'] );
			return array(
				'rule'       => null,
				'rule_id'    => '',
				'department' => sanitize_key( $department_override ),
				'agent'      => $agent,
			);
		}

		foreach ( $rules as $rule ) {
			if ( empty( $rule['enabled'] ) ) {
				continue;
			}
			if ( self::rule_matches( $rule ) ) {
				$matched = $rule;
				break;
			}
		}

		$department = $matched['department'] ?? 'general';
		$agent_ids  = ! empty( $matched['agent_ids'] ) ? $matched['agent_ids'] : array();
		$agent      = self::pick_agent( $agent_ids, $department, $settings['default_agent_id'] );

		return array(
			'rule'       => $matched,
			'rule_id'    => $matched['id'] ?? '',
			'department' => $department,
			'agent'      => $agent,
		);
	}

	private static function rule_matches( $rule ) {
		$type  = $rule['match_type'] ?? 'url_contains';
		$value = $rule['match_value'] ?? '';

		switch ( $type ) {
			case 'page_id':
				return is_page( absint( $value ) );

			case 'post_id':
				return is_single( absint( $value ) );

			case 'post_type':
				return is_singular( sanitize_key( $value ) );

			case 'url_contains':
				$needles = array_filter( array_map( 'trim', explode( ',', $value ) ) );
				$url     = RLWC_Messages::current_url();
				$path    = wp_parse_url( $url, PHP_URL_PATH );
				foreach ( $needles as $needle ) {
					if ( false !== stripos( $path, $needle ) || false !== stripos( $url, $needle ) ) {
						return true;
					}
				}
				return false;

			case 'woocommerce':
				if ( ! function_exists( 'is_woocommerce' ) ) {
					return false;
				}
				if ( 'shop' === $value ) {
					return is_shop() || is_product_category() || is_product_tag() || is_product();
				}
				if ( 'cart' === $value ) {
					return is_cart();
				}
				if ( 'checkout' === $value ) {
					return is_checkout();
				}
				if ( 'account' === $value ) {
					return is_account_page();
				}
				return is_woocommerce();

			default:
				return false;
		}
	}

	private static function pick_agent( $agent_ids, $department, $default_agent_id ) {
		$agents = array_filter(
			RLWC_Settings::get_agents(),
			static function ( $agent ) {
				return ! empty( $agent['enabled'] ) && ! empty( $agent['phone'] );
			}
		);

		if ( empty( $agents ) ) {
			return null;
		}

		$pool = array();
		if ( ! empty( $agent_ids ) ) {
			foreach ( $agents as $agent ) {
				if ( in_array( $agent['id'], $agent_ids, true ) ) {
					$pool[] = $agent;
				}
			}
		}

		if ( empty( $pool ) ) {
			foreach ( $agents as $agent ) {
				if ( $agent['department'] === $department ) {
					$pool[] = $agent;
				}
			}
		}

		if ( empty( $pool ) && $default_agent_id ) {
			$agent = RLWC_Settings::get_agent_by_id( $default_agent_id );
			return $agent && ! empty( $agent['phone'] ) ? $agent : reset( $agents );
		}

		if ( empty( $pool ) ) {
			$pool = $agents;
		}

		$pool = array_values( $pool );
		if ( 1 === count( $pool ) ) {
			return $pool[0];
		}

		$key      = $department . '_' . md5( wp_json_encode( $agent_ids ) );
		$counters = get_option( self::ROUND_ROBIN_KEY, array() );
		$index    = isset( $counters[ $key ] ) ? (int) $counters[ $key ] : 0;
		$agent    = $pool[ $index % count( $pool ) ];

		$counters[ $key ] = $index + 1;
		update_option( self::ROUND_ROBIN_KEY, $counters, false );

		return $agent;
	}

	public static function should_show_widget() {
		$settings = RLWC_Settings::get();
		if ( empty( $settings['enabled'] ) ) {
			return false;
		}

		if ( is_admin() ) {
			return false;
		}

		$post_type = get_post_type();
		if ( $post_type && in_array( $post_type, (array) $settings['exclude_post_types'], true ) ) {
			return false;
		}

		$exclude = array_filter( array_map( 'trim', explode( "\n", $settings['exclude_urls'] ) ) );
		$url     = RLWC_Messages::current_url();
		foreach ( $exclude as $pattern ) {
			if ( $pattern && false !== stripos( $url, $pattern ) ) {
				return false;
			}
		}

		$route = self::resolve();
		return ! empty( $route['agent']['phone'] );
	}
}
