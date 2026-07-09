<?php
defined( 'ABSPATH' ) || exit;
$days = array( 'mon' => __( 'Monday', 'ramerlabs-whatsapp-chat-pro' ), 'tue' => __( 'Tuesday', 'ramerlabs-whatsapp-chat-pro' ), 'wed' => __( 'Wednesday', 'ramerlabs-whatsapp-chat-pro' ), 'thu' => __( 'Thursday', 'ramerlabs-whatsapp-chat-pro' ), 'fri' => __( 'Friday', 'ramerlabs-whatsapp-chat-pro' ), 'sat' => __( 'Saturday', 'ramerlabs-whatsapp-chat-pro' ), 'sun' => __( 'Sunday', 'ramerlabs-whatsapp-chat-pro' ) );
$agents = RLWC_Settings::get_agents();
?>
<form method="post">
	<?php wp_nonce_field( 'rlwc_settings' ); ?>
	<input type="hidden" name="rlwc_save_settings" value="1">

	<div class="rlwc-admin-card">
		<h2><?php esc_html_e( 'General', 'ramerlabs-whatsapp-chat-pro' ); ?></h2>
		<table class="form-table">
			<tr>
				<th><?php esc_html_e( 'Enable widget', 'ramerlabs-whatsapp-chat-pro' ); ?></th>
				<td><label><input type="checkbox" name="rlwc[enabled]" value="1" <?php checked( $settings['enabled'] ); ?>></label></td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Button text', 'ramerlabs-whatsapp-chat-pro' ); ?></th>
				<td><input type="text" class="regular-text" name="rlwc[button_text]" value="<?php echo esc_attr( $settings['button_text'] ); ?>"></td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Position', 'ramerlabs-whatsapp-chat-pro' ); ?></th>
				<td>
					<select name="rlwc[button_position]">
						<option value="bottom-right" <?php selected( $settings['button_position'], 'bottom-right' ); ?>><?php esc_html_e( 'Bottom right', 'ramerlabs-whatsapp-chat-pro' ); ?></option>
						<option value="bottom-left" <?php selected( $settings['button_position'], 'bottom-left' ); ?>><?php esc_html_e( 'Bottom left', 'ramerlabs-whatsapp-chat-pro' ); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Button color', 'ramerlabs-whatsapp-chat-pro' ); ?></th>
				<td><input type="color" name="rlwc[button_color]" value="<?php echo esc_attr( $settings['button_color'] ); ?>"></td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Default country code', 'ramerlabs-whatsapp-chat-pro' ); ?></th>
				<td>
					<input type="text" class="small-text" name="rlwc[default_country_code]" value="<?php echo esc_attr( $settings['default_country_code'] ); ?>" placeholder="63">
					<p class="rlwc-help"><?php esc_html_e( 'Used to convert local numbers (e.g. 0976…) to international format. Digits only, no +.', 'ramerlabs-whatsapp-chat-pro' ); ?></p>
				</td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Default agent', 'ramerlabs-whatsapp-chat-pro' ); ?></th>
				<td>
					<select name="rlwc[default_agent_id]">
						<option value=""><?php esc_html_e( 'Auto (first available)', 'ramerlabs-whatsapp-chat-pro' ); ?></option>
						<?php foreach ( $agents as $agent ) : ?>
							<option value="<?php echo esc_attr( $agent['id'] ); ?>" <?php selected( $settings['default_agent_id'], $agent['id'] ); ?>><?php echo esc_html( $agent['name'] ); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Visibility', 'ramerlabs-whatsapp-chat-pro' ); ?></th>
				<td>
					<label><input type="checkbox" name="rlwc[show_on_desktop]" value="1" <?php checked( $settings['show_on_desktop'] ); ?>> <?php esc_html_e( 'Desktop', 'ramerlabs-whatsapp-chat-pro' ); ?></label><br>
					<label><input type="checkbox" name="rlwc[show_on_mobile]" value="1" <?php checked( $settings['show_on_mobile'] ); ?>> <?php esc_html_e( 'Mobile', 'ramerlabs-whatsapp-chat-pro' ); ?></label>
				</td>
			</tr>
		</table>
	</div>

	<div class="rlwc-admin-card">
		<h2><?php esc_html_e( 'Business hours', 'ramerlabs-whatsapp-chat-pro' ); ?></h2>
		<table class="form-table">
			<tr>
				<th><?php esc_html_e( 'Timezone', 'ramerlabs-whatsapp-chat-pro' ); ?></th>
				<td>
					<input type="text" class="regular-text" name="rlwc[timezone]" value="<?php echo esc_attr( $settings['timezone'] ); ?>" placeholder="<?php echo esc_attr( wp_timezone_string() ); ?>">
					<p class="rlwc-help"><?php esc_html_e( 'Leave empty to use WordPress site timezone.', 'ramerlabs-whatsapp-chat-pro' ); ?></p>
				</td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Online message', 'ramerlabs-whatsapp-chat-pro' ); ?></th>
				<td><input type="text" class="large-text" name="rlwc[online_message]" value="<?php echo esc_attr( $settings['online_message'] ); ?>"></td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Offline message', 'ramerlabs-whatsapp-chat-pro' ); ?></th>
				<td>
					<input type="text" class="large-text" name="rlwc[offline_message]" value="<?php echo esc_attr( $settings['offline_message'] ); ?>">
					<p class="rlwc-help"><?php esc_html_e( 'Use {hours} for estimated reply time.', 'ramerlabs-whatsapp-chat-pro' ); ?></p>
				</td>
			</tr>
		</table>
		<table class="widefat striped">
			<thead><tr><th><?php esc_html_e( 'Day', 'ramerlabs-whatsapp-chat-pro' ); ?></th><th><?php esc_html_e( 'Open', 'ramerlabs-whatsapp-chat-pro' ); ?></th><th><?php esc_html_e( 'Start', 'ramerlabs-whatsapp-chat-pro' ); ?></th><th><?php esc_html_e( 'End', 'ramerlabs-whatsapp-chat-pro' ); ?></th></tr></thead>
			<tbody>
			<?php foreach ( $days as $key => $label ) : $day = $settings['business_hours'][ $key ]; ?>
				<tr>
					<td><?php echo esc_html( $label ); ?></td>
					<td><input type="checkbox" name="rlwc[business_hours][<?php echo esc_attr( $key ); ?>][enabled]" value="1" <?php checked( ! empty( $day['enabled'] ) ); ?>></td>
					<td><input type="time" name="rlwc[business_hours][<?php echo esc_attr( $key ); ?>][start]" value="<?php echo esc_attr( $day['start'] ); ?>"></td>
					<td><input type="time" name="rlwc[business_hours][<?php echo esc_attr( $key ); ?>][end]" value="<?php echo esc_attr( $day['end'] ); ?>"></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	</div>

	<div class="rlwc-admin-card">
		<h2><?php esc_html_e( 'Message templates', 'ramerlabs-whatsapp-chat-pro' ); ?></h2>
		<p class="rlwc-help"><?php esc_html_e( 'Placeholders: {page_title}, {page_url}, {site_name}, {post_title}, {product_name}, {product_price}, {product_sku}', 'ramerlabs-whatsapp-chat-pro' ); ?></p>
		<table class="form-table">
			<?php foreach ( $settings['message_templates'] as $key => $template ) : ?>
				<tr>
					<th><?php echo esc_html( ucfirst( $key ) ); ?></th>
					<td><textarea class="large-text" rows="2" name="rlwc[message_templates][<?php echo esc_attr( $key ); ?>]"><?php echo esc_textarea( $template ); ?></textarea></td>
				</tr>
			<?php endforeach; ?>
		</table>
	</div>

	<div class="rlwc-admin-card">
		<h2><?php esc_html_e( 'GDPR consent', 'ramerlabs-whatsapp-chat-pro' ); ?></h2>
		<table class="form-table">
			<tr>
				<th><?php esc_html_e( 'Require consent', 'ramerlabs-whatsapp-chat-pro' ); ?></th>
				<td><label><input type="checkbox" name="rlwc[gdpr_enabled]" value="1" <?php checked( $settings['gdpr_enabled'] ); ?>></label></td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Modal title', 'ramerlabs-whatsapp-chat-pro' ); ?></th>
				<td><input type="text" class="large-text" name="rlwc[gdpr_title]" value="<?php echo esc_attr( $settings['gdpr_title'] ); ?>"></td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Modal message', 'ramerlabs-whatsapp-chat-pro' ); ?></th>
				<td><textarea class="large-text" rows="3" name="rlwc[gdpr_message]"><?php echo esc_textarea( $settings['gdpr_message'] ); ?></textarea></td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Continue button', 'ramerlabs-whatsapp-chat-pro' ); ?></th>
				<td><input type="text" class="regular-text" name="rlwc[gdpr_button]" value="<?php echo esc_attr( $settings['gdpr_button'] ); ?>"></td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Privacy policy URL', 'ramerlabs-whatsapp-chat-pro' ); ?></th>
				<td><input type="url" class="large-text" name="rlwc[gdpr_privacy_url]" value="<?php echo esc_attr( $settings['gdpr_privacy_url'] ); ?>"></td>
			</tr>
		</table>
	</div>

	<div class="rlwc-admin-card">
		<h2><?php esc_html_e( 'UTM tracking', 'ramerlabs-whatsapp-chat-pro' ); ?></h2>
		<table class="form-table">
			<tr>
				<th><?php esc_html_e( 'Append UTM to WhatsApp link', 'ramerlabs-whatsapp-chat-pro' ); ?></th>
				<td><label><input type="checkbox" name="rlwc[append_utm]" value="1" <?php checked( $settings['append_utm'] ); ?>></label></td>
			</tr>
			<tr>
				<th>utm_source</th>
				<td><input type="text" name="rlwc[utm_source]" value="<?php echo esc_attr( $settings['utm_source'] ); ?>"></td>
			</tr>
			<tr>
				<th>utm_medium</th>
				<td><input type="text" name="rlwc[utm_medium]" value="<?php echo esc_attr( $settings['utm_medium'] ); ?>"></td>
			</tr>
			<tr>
				<th>utm_campaign</th>
				<td><input type="text" name="rlwc[utm_campaign]" value="<?php echo esc_attr( $settings['utm_campaign'] ); ?>"></td>
			</tr>
		</table>
	</div>

	<div class="rlwc-admin-card">
		<h2><?php esc_html_e( 'Exclusions', 'ramerlabs-whatsapp-chat-pro' ); ?></h2>
		<table class="form-table">
			<tr>
				<th><?php esc_html_e( 'Exclude URLs', 'ramerlabs-whatsapp-chat-pro' ); ?></th>
				<td>
					<textarea class="large-text" rows="3" name="rlwc[exclude_urls]" placeholder="/wp-admin&#10;/checkout"><?php echo esc_textarea( $settings['exclude_urls'] ); ?></textarea>
					<p class="rlwc-help"><?php esc_html_e( 'One URL fragment per line.', 'ramerlabs-whatsapp-chat-pro' ); ?></p>
				</td>
			</tr>
		</table>
	</div>

	<?php submit_button( __( 'Save settings', 'ramerlabs-whatsapp-chat-pro' ) ); ?>
</form>
