<?php
defined( 'ABSPATH' ) || exit;

class RLWC_Frontend {

	private static $assets_enqueued = false;

	public static function enqueue_assets() {
		if ( self::$assets_enqueued ) {
			return;
		}

		wp_enqueue_style(
			'rlwc-widget',
			RLWC_PLUGIN_URL . 'assets/css/widget.css',
			array(),
			RLWC_VERSION
		);

		wp_enqueue_script(
			'rlwc-widget',
			RLWC_PLUGIN_URL . 'assets/js/widget.js',
			array(),
			RLWC_VERSION,
			true
		);

		$settings = RLWC_Settings::get();
		$custom_css = sprintf(
			':root { --rlwc-button-color: %s; --rlwc-z-index: %d; }',
			esc_attr( $settings['button_color'] ),
			(int) $settings['z_index']
		);
		wp_add_inline_style( 'rlwc-widget', $custom_css );
		self::$assets_enqueued = true;
	}

	public static function localize_for_route( $route, $instance_id ) {
		$settings = RLWC_Settings::get();
		$context  = RLWC_Messages::current_context();
		$message  = RLWC_Messages::build_for_context( $context );
		$agent    = $route['agent'];

		if ( empty( $agent['phone'] ) ) {
			return false;
		}

		$utm = array();
		if ( ! empty( $settings['append_utm'] ) ) {
			$utm = array(
				'utm_source'   => $settings['utm_source'],
				'utm_medium'   => $settings['utm_medium'],
				'utm_campaign' => $settings['utm_campaign'],
			);
		}

		wp_localize_script(
			'rlwc-widget',
			'rlwcWidget_' . $instance_id,
			array(
				'restUrl'       => esc_url_raw( rest_url( 'rlwc/v1/click' ) ),
				'nonce'         => wp_create_nonce( 'wp_rest' ),
				'whatsappUrl'   => RLWC_Messages::whatsapp_url( $agent['phone'], $message ),
				'gdprEnabled'   => (bool) $settings['gdpr_enabled'],
				'gdprTitle'     => $settings['gdpr_title'],
				'gdprMessage'   => $settings['gdpr_message'],
				'gdprButton'    => $settings['gdpr_button'],
				'privacyUrl'    => $settings['gdpr_privacy_url'],
				'statusMessage' => RLWC_Hours::status_message(),
				'isOpen'        => RLWC_Hours::is_open(),
				'agentId'       => $agent['id'],
				'agentName'     => $agent['name'],
				'department'    => $route['department'],
				'ruleId'        => $route['rule_id'],
				'pageUrl'       => RLWC_Messages::current_url(),
				'pageTitle'     => wp_get_document_title(),
				'utm'           => $utm,
				'sessionKey'    => 'rlwc_session',
				'instanceId'    => $instance_id,
			)
		);

		return true;
	}

	public static function render_button( $atts = array() ) {
		$atts = wp_parse_args(
			$atts,
			array(
				'text'       => '',
				'department' => '',
				'style'      => 'button',
				'show_icon'  => true,
				'class'      => '',
			)
		);

		$route = RLWC_Routing::resolve( $atts['department'] );
		if ( empty( $route['agent']['phone'] ) ) {
			return '';
		}

		self::enqueue_assets();

		static $instance = 0;
		$instance++;
		$instance_id = 'inline_' . $instance;

		self::localize_for_route( $route, $instance_id );

		$settings = RLWC_Settings::get();
		$label    = $atts['text'] ?: $settings['button_text'];
		$classes  = array( 'rlwc-inline', 'rlwc-inline--' . sanitize_html_class( $atts['style'] ) );
		if ( $atts['class'] ) {
			$classes[] = sanitize_html_class( $atts['class'] );
		}

		ob_start();
		?>
		<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" data-rlwc-instance="<?php echo esc_attr( $instance_id ); ?>">
			<button type="button" class="rlwc-inline__button rlwc-widget__button" data-rlwc-trigger="<?php echo esc_attr( $instance_id ); ?>">
				<?php if ( $atts['show_icon'] ) : ?>
					<span class="rlwc-widget__icon" aria-hidden="true">
						<svg viewBox="0 0 24 24" width="22" height="22" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.435 9.884-9.881 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
					</span>
				<?php endif; ?>
				<span class="rlwc-widget__label"><?php echo esc_html( $label ); ?></span>
			</button>

			<div class="rlwc-widget__modal" id="rlwc-gdpr-modal-<?php echo esc_attr( $instance_id ); ?>" role="dialog" aria-modal="true" hidden>
				<div class="rlwc-widget__modal-backdrop" data-rlwc-close></div>
				<div class="rlwc-widget__modal-card">
					<h3 class="rlwc-widget__modal-title"></h3>
					<p class="rlwc-widget__modal-text"></p>
					<p class="rlwc-widget__modal-privacy"></p>
					<div class="rlwc-widget__modal-actions">
						<button type="button" class="rlwc-widget__modal-cancel" data-rlwc-close><?php esc_html_e( 'Cancel', 'ramerlabs-whatsapp-chat-pro' ); ?></button>
						<button type="button" class="rlwc-widget__modal-confirm"></button>
					</div>
				</div>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}
}
