<?php
defined( 'ABSPATH' ) || exit;

class RLWC_Tracking {

	public static function init() {
		add_action( 'rest_api_init', array( __CLASS__, 'register_routes' ) );
	}

	public static function register_routes() {
		register_rest_route(
			'rlwc/v1',
			'/click',
			array(
				'methods'             => 'POST',
				'callback'            => array( __CLASS__, 'record_click' ),
				'permission_callback' => '__return_true',
				'args'                => array(
					'session_id' => array( 'sanitize_callback' => 'sanitize_text_field' ),
					'page_url'   => array( 'sanitize_callback' => 'esc_url_raw' ),
					'page_title' => array( 'sanitize_callback' => 'sanitize_text_field' ),
					'agent_id'   => array( 'sanitize_callback' => 'sanitize_key' ),
					'department' => array( 'sanitize_callback' => 'sanitize_key' ),
					'rule_id'    => array( 'sanitize_callback' => 'sanitize_key' ),
					'consent'    => array( 'sanitize_callback' => 'rest_sanitize_boolean' ),
				),
			)
		);
	}

	public static function record_click( WP_REST_Request $request ) {
		if ( ! RLWC_License::can_show_frontend() ) {
			return new WP_REST_Response( array( 'success' => false ), 403 );
		}

		global $wpdb;

		$agent_id = $request->get_param( 'agent_id' );
		$agent    = RLWC_Settings::get_agent_by_id( $agent_id );

		$wpdb->insert(
			RLWC_Database::table_name(),
			array(
				'created_at'    => current_time( 'mysql', true ),
				'session_id'    => substr( sanitize_text_field( $request->get_param( 'session_id' ) ?: '' ), 0, 64 ),
				'page_url'      => esc_url_raw( $request->get_param( 'page_url' ) ?: '' ),
				'page_title'    => sanitize_text_field( $request->get_param( 'page_title' ) ?: '' ),
				'agent_id'      => $agent_id ?: '',
				'agent_name'    => $agent['name'] ?? '',
				'department'    => sanitize_key( $request->get_param( 'department' ) ?: '' ),
				'rule_id'       => sanitize_key( $request->get_param( 'rule_id' ) ?: '' ),
				'utm_source'    => sanitize_key( $request->get_param( 'utm_source' ) ?: '' ),
				'utm_medium'    => sanitize_key( $request->get_param( 'utm_medium' ) ?: '' ),
				'utm_campaign'  => sanitize_key( $request->get_param( 'utm_campaign' ) ?: '' ),
				'referrer'      => esc_url_raw( wp_get_referer() ?: '' ),
				'user_agent'    => substr( sanitize_text_field( $_SERVER['HTTP_USER_AGENT'] ?? '' ), 0, 255 ),
				'consent_given' => $request->get_param( 'consent' ) ? 1 : 0,
			),
			array( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d' )
		);

		return new WP_REST_Response( array( 'success' => true ), 200 );
	}

	public static function get_stats( $days = 30 ) {
		global $wpdb;
		$table = RLWC_Database::table_name();
		$since = gmdate( 'Y-m-d H:i:s', strtotime( '-' . absint( $days ) . ' days' ) );

		$total = (int) $wpdb->get_var(
			$wpdb->prepare( "SELECT COUNT(*) FROM {$table} WHERE created_at >= %s", $since )
		);

		$by_department = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT department, COUNT(*) AS clicks FROM {$table} WHERE created_at >= %s GROUP BY department ORDER BY clicks DESC",
				$since
			),
			ARRAY_A
		);

		$by_agent = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT agent_id, agent_name, COUNT(*) AS clicks FROM {$table} WHERE created_at >= %s GROUP BY agent_id, agent_name ORDER BY clicks DESC LIMIT 10",
				$since
			),
			ARRAY_A
		);

		$recent = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM {$table} WHERE created_at >= %s ORDER BY created_at DESC LIMIT 50",
				$since
			)
		);

		return compact( 'total', 'by_department', 'by_agent', 'recent', 'days' );
	}
}
