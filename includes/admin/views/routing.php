<?php
defined( 'ABSPATH' ) || exit;
$departments = array( 'sales', 'support', 'billing', 'general' );
$match_types = array(
	'url_contains' => __( 'URL contains', 'ramerlabs-whatsapp-chat-pro' ),
	'page_id'      => __( 'Page ID', 'ramerlabs-whatsapp-chat-pro' ),
	'post_id'      => __( 'Post ID', 'ramerlabs-whatsapp-chat-pro' ),
	'post_type'    => __( 'Post type', 'ramerlabs-whatsapp-chat-pro' ),
	'woocommerce'  => __( 'WooCommerce', 'ramerlabs-whatsapp-chat-pro' ),
);
?>
<form method="post" class="rlwc-admin-card">
	<?php wp_nonce_field( 'rlwc_rules' ); ?>
	<input type="hidden" name="rlwc_save_rules" value="1">

	<p class="rlwc-help"><?php esc_html_e( 'Rules are evaluated by priority (lowest number first). Use comma-separated values for URL contains and agent IDs.', 'ramerlabs-whatsapp-chat-pro' ); ?></p>

	<table class="widefat striped">
		<thead>
			<tr>
				<th><?php esc_html_e( 'Name', 'ramerlabs-whatsapp-chat-pro' ); ?></th>
				<th><?php esc_html_e( 'Match', 'ramerlabs-whatsapp-chat-pro' ); ?></th>
				<th><?php esc_html_e( 'Value', 'ramerlabs-whatsapp-chat-pro' ); ?></th>
				<th><?php esc_html_e( 'Department', 'ramerlabs-whatsapp-chat-pro' ); ?></th>
				<th><?php esc_html_e( 'Agent IDs', 'ramerlabs-whatsapp-chat-pro' ); ?></th>
				<th><?php esc_html_e( 'Priority', 'ramerlabs-whatsapp-chat-pro' ); ?></th>
				<th><?php esc_html_e( 'On', 'ramerlabs-whatsapp-chat-pro' ); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php
		$rows = $rules;
		$rows[] = array();
		foreach ( $rows as $i => $rule ) :
			?>
			<tr class="rlwc-rule-row">
				<td>
					<input type="hidden" name="rules[<?php echo (int) $i; ?>][id]" value="<?php echo esc_attr( $rule['id'] ?? '' ); ?>">
					<input type="text" name="rules[<?php echo (int) $i; ?>][name]" value="<?php echo esc_attr( $rule['name'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Rule name', 'ramerlabs-whatsapp-chat-pro' ); ?>">
				</td>
				<td>
					<select name="rules[<?php echo (int) $i; ?>][match_type]">
						<?php foreach ( $match_types as $key => $label ) : ?>
							<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $rule['match_type'] ?? '', $key ); ?>><?php echo esc_html( $label ); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
				<td><input type="text" name="rules[<?php echo (int) $i; ?>][match_value]" value="<?php echo esc_attr( $rule['match_value'] ?? '' ); ?>" placeholder="checkout,cart / shop / 12"></td>
				<td>
					<select name="rules[<?php echo (int) $i; ?>][department]">
						<?php foreach ( $departments as $dept ) : ?>
							<option value="<?php echo esc_attr( $dept ); ?>" <?php selected( $rule['department'] ?? 'general', $dept ); ?>><?php echo esc_html( ucfirst( $dept ) ); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
				<td>
					<input type="text" name="rules[<?php echo (int) $i; ?>][agent_ids]" value="<?php echo esc_attr( ! empty( $rule['agent_ids'] ) ? implode( ',', (array) $rule['agent_ids'] ) : '' ); ?>" placeholder="agent_sales,agent_support">
					<p class="rlwc-help">
						<?php
						foreach ( $agents as $agent ) {
							echo esc_html( $agent['id'] . ' (' . $agent['name'] . ')' ) . '<br>';
						}
						?>
					</p>
				</td>
				<td><input type="number" name="rules[<?php echo (int) $i; ?>][priority]" value="<?php echo esc_attr( $rule['priority'] ?? 10 ); ?>" min="1" style="width:70px"></td>
				<td><label><input type="checkbox" name="rules[<?php echo (int) $i; ?>][enabled]" value="1" <?php checked( ! empty( $rule['enabled'] ) || ! isset( $rule['name'] ) ); ?>></label></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>

	<?php submit_button( __( 'Save routing rules', 'ramerlabs-whatsapp-chat-pro' ) ); ?>
</form>
