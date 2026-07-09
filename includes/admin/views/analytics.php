<?php
defined( 'ABSPATH' ) || exit;
$days_list = array( 7, 30, 90 );
?>
<div class="rlwc-admin-card">
	<p>
		<?php esc_html_e( 'Period:', 'ramerlabs-whatsapp-chat-pro' ); ?>
		<?php foreach ( $days_list as $d ) : ?>
			<a class="<?php echo $days === $d ? 'rlwc-badge' : ''; ?>" href="<?php echo esc_url( admin_url( 'admin.php?page=rlwc-analytics&days=' . $d ) ); ?>"><?php echo (int) $d; ?> <?php esc_html_e( 'days', 'ramerlabs-whatsapp-chat-pro' ); ?></a>
		<?php endforeach; ?>
	</p>

	<div class="rlwc-admin-grid">
		<div class="rlwc-stat"><strong><?php echo (int) $stats['total']; ?></strong><span><?php esc_html_e( 'Total clicks', 'ramerlabs-whatsapp-chat-pro' ); ?></span></div>
	</div>
</div>

<div class="rlwc-admin-card">
	<h2><?php esc_html_e( 'Clicks by department', 'ramerlabs-whatsapp-chat-pro' ); ?></h2>
	<table class="widefat striped">
		<thead><tr><th><?php esc_html_e( 'Department', 'ramerlabs-whatsapp-chat-pro' ); ?></th><th><?php esc_html_e( 'Clicks', 'ramerlabs-whatsapp-chat-pro' ); ?></th></tr></thead>
		<tbody>
		<?php if ( empty( $stats['by_department'] ) ) : ?>
			<tr><td colspan="2"><?php esc_html_e( 'No clicks recorded yet.', 'ramerlabs-whatsapp-chat-pro' ); ?></td></tr>
		<?php else : ?>
			<?php foreach ( $stats['by_department'] as $row ) : ?>
				<tr>
					<td><span class="rlwc-badge"><?php echo esc_html( $row['department'] ?: 'general' ); ?></span></td>
					<td><?php echo (int) $row['clicks']; ?></td>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>
		</tbody>
	</table>
</div>

<div class="rlwc-admin-card">
	<h2><?php esc_html_e( 'Top agents', 'ramerlabs-whatsapp-chat-pro' ); ?></h2>
	<table class="widefat striped">
		<thead><tr><th><?php esc_html_e( 'Agent', 'ramerlabs-whatsapp-chat-pro' ); ?></th><th><?php esc_html_e( 'Clicks', 'ramerlabs-whatsapp-chat-pro' ); ?></th></tr></thead>
		<tbody>
		<?php if ( empty( $stats['by_agent'] ) ) : ?>
			<tr><td colspan="2"><?php esc_html_e( 'No agent clicks yet.', 'ramerlabs-whatsapp-chat-pro' ); ?></td></tr>
		<?php else : ?>
			<?php foreach ( $stats['by_agent'] as $row ) : ?>
				<tr>
					<td><?php echo esc_html( $row['agent_name'] ?: $row['agent_id'] ); ?></td>
					<td><?php echo (int) $row['clicks']; ?></td>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>
		</tbody>
	</table>
</div>

<div class="rlwc-admin-card">
	<h2><?php esc_html_e( 'Recent clicks', 'ramerlabs-whatsapp-chat-pro' ); ?></h2>
	<table class="widefat striped">
		<thead>
			<tr>
				<th><?php esc_html_e( 'Date', 'ramerlabs-whatsapp-chat-pro' ); ?></th>
				<th><?php esc_html_e( 'Page', 'ramerlabs-whatsapp-chat-pro' ); ?></th>
				<th><?php esc_html_e( 'Agent', 'ramerlabs-whatsapp-chat-pro' ); ?></th>
				<th><?php esc_html_e( 'UTM', 'ramerlabs-whatsapp-chat-pro' ); ?></th>
				<th><?php esc_html_e( 'Consent', 'ramerlabs-whatsapp-chat-pro' ); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php if ( empty( $stats['recent'] ) ) : ?>
			<tr><td colspan="5"><?php esc_html_e( 'No recent activity.', 'ramerlabs-whatsapp-chat-pro' ); ?></td></tr>
		<?php else : ?>
			<?php foreach ( $stats['recent'] as $row ) : ?>
				<tr>
					<td><?php echo esc_html( get_date_from_gmt( $row->created_at, 'Y-m-d H:i' ) ); ?></td>
					<td>
						<a href="<?php echo esc_url( $row->page_url ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $row->page_title ?: wp_parse_url( $row->page_url, PHP_URL_PATH ) ); ?></a>
					</td>
					<td><?php echo esc_html( $row->agent_name ); ?> <span class="rlwc-badge"><?php echo esc_html( $row->department ); ?></span></td>
					<td><?php echo esc_html( trim( $row->utm_source . ' / ' . $row->utm_medium . ' / ' . $row->utm_campaign, ' /' ) ); ?></td>
					<td><?php echo $row->consent_given ? '✓' : '—'; ?></td>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>
		</tbody>
	</table>
</div>
