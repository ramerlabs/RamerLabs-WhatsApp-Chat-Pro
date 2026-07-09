<?php
defined( 'ABSPATH' ) || exit;
$departments = array( 'sales', 'support', 'billing', 'general' );
?>
<form method="post" class="rlwc-admin-card">
	<?php wp_nonce_field( 'rlwc_agents' ); ?>
	<input type="hidden" name="rlwc_save_agents" value="1">

	<table class="widefat striped">
		<thead>
			<tr>
				<th><?php esc_html_e( 'Name', 'ramerlabs-whatsapp-chat-pro' ); ?></th>
				<th><?php esc_html_e( 'Phone (country code, no +)', 'ramerlabs-whatsapp-chat-pro' ); ?></th>
				<th><?php esc_html_e( 'Department', 'ramerlabs-whatsapp-chat-pro' ); ?></th>
				<th><?php esc_html_e( 'ID', 'ramerlabs-whatsapp-chat-pro' ); ?></th>
				<th><?php esc_html_e( 'Enabled', 'ramerlabs-whatsapp-chat-pro' ); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php
		$rows = $agents;
		$rows[] = array();
		foreach ( $rows as $i => $agent ) :
			?>
			<tr class="rlwc-agent-row">
				<td>
					<input type="hidden" name="agents[<?php echo (int) $i; ?>][id]" value="<?php echo esc_attr( $agent['id'] ?? '' ); ?>">
					<input type="text" name="agents[<?php echo (int) $i; ?>][name]" value="<?php echo esc_attr( $agent['name'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Agent name', 'ramerlabs-whatsapp-chat-pro' ); ?>">
				</td>
				<td><input type="tel" name="agents[<?php echo (int) $i; ?>][phone]" value="<?php echo esc_attr( $agent['phone'] ?? '' ); ?>" placeholder="639761052652"></td>
				<td>
					<select name="agents[<?php echo (int) $i; ?>][department]">
						<?php foreach ( $departments as $dept ) : ?>
							<option value="<?php echo esc_attr( $dept ); ?>" <?php selected( $agent['department'] ?? 'general', $dept ); ?>><?php echo esc_html( ucfirst( $dept ) ); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
				<td><code><?php echo esc_html( $agent['id'] ?? __( 'auto', 'ramerlabs-whatsapp-chat-pro' ) ); ?></code></td>
				<td><label><input type="checkbox" name="agents[<?php echo (int) $i; ?>][enabled]" value="1" <?php checked( ! empty( $agent['enabled'] ) || ! isset( $agent['name'] ) ); ?>></label></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>

	<p class="rlwc-help"><?php esc_html_e( 'Add a blank row to create another agent. Phone numbers must include country code without spaces.', 'ramerlabs-whatsapp-chat-pro' ); ?></p>
	<?php submit_button( __( 'Save agents', 'ramerlabs-whatsapp-chat-pro' ) ); ?>
</form>
