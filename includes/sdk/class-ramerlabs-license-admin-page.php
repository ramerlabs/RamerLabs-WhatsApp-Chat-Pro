<?php
/**
 * RamerLabs License Admin Page
 *
 * Adds a license activation screen to your plugin or theme settings.
 *
 * @package RamerLabs_License_Client
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'RamerLabs_License_Admin_Page' ) ) {

	class RamerLabs_License_Admin_Page {

		private $client;
		private $page_title;
		private $menu_title;
		private $parent_slug;
		private $capability;
		private $menu_slug;

		/**
		 * @param RamerLabs_License_Client $client       License client instance.
		 * @param array                    $config       Page configuration.
		 */
		public function __construct( $client, $config = array() ) {
			$this->client = $client;

			$defaults = array(
				'page_title'  => __( 'License', 'ramerlabs-membership-pro' ),
				'menu_title'  => __( 'License', 'ramerlabs-membership-pro' ),
				'parent_slug' => 'options-general.php',
				'capability'  => 'manage_options',
				'menu_slug'   => '',
			);

			$config = wp_parse_args( $config, $defaults );

			$this->page_title  = $config['page_title'];
			$this->menu_title  = $config['menu_title'];
			$this->parent_slug = $config['parent_slug'];
			$this->capability  = $config['capability'];
			$this->menu_slug   = $config['menu_slug'] ?: 'ramerlabs-license-' . sanitize_title( $client->get_product_slug() );
		}

		public function register() {
			// Parent menu must exist first (see RLMP_Admin::menu priority 5).
			add_action( 'admin_menu', array( $this, 'add_menu_page' ), 15 );
			add_action( 'admin_init', array( $this, 'handle_form' ) );
			add_action( 'admin_notices', array( $this, 'admin_notices' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		}

		public function enqueue_assets( $hook ) {
			if ( empty( $_GET['page'] ) || $_GET['page'] !== $this->menu_slug ) {
				return;
			}

			if ( class_exists( 'RamerLabs_Branding' ) ) {
				RamerLabs_Branding::enqueue_client_assets();
			}
		}

		public function add_menu_page() {
			add_submenu_page(
				$this->parent_slug,
				$this->page_title,
				$this->menu_title,
				$this->capability,
				$this->menu_slug,
				array( $this, 'render_page' )
			);
		}

		public function handle_form() {
			if ( empty( $_POST['rlm_client_action'] ) || empty( $_POST['_wpnonce'] ) ) {
				return;
			}

			if ( ! current_user_can( $this->capability ) ) {
				return;
			}

			if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'rlm_client_license' ) ) {
				return;
			}

			$action = sanitize_key( wp_unslash( $_POST['rlm_client_action'] ) );
			$key    = isset( $_POST['license_key'] ) ? sanitize_text_field( wp_unslash( $_POST['license_key'] ) ) : '';

			if ( 'activate' === $action ) {
				$result = $this->client->activate( $key );
				$this->set_notice( $result, 'activate' );
			}

			if ( 'deactivate' === $action ) {
				$result = $this->client->deactivate();
				$this->set_notice( $result, 'deactivate' );
			}

			if ( 'check' === $action ) {
				$valid = $this->client->validate( true );
				$message = $valid
					? __( 'License is valid.', 'ramerlabs-membership-pro' )
					: __( 'License is not active on this site yet. Enter your key and click Activate License first.', 'ramerlabs-membership-pro' );
				set_transient(
					$this->get_notice_key(),
					array(
						'type'    => $valid ? 'success' : 'error',
						'message' => $message,
					),
					30
				);
			}

			wp_safe_redirect( admin_url( 'admin.php?page=' . $this->menu_slug ) );
			exit;
		}

		public function admin_notices() {
			if ( empty( $_GET['page'] ) || $_GET['page'] !== $this->menu_slug ) {
				return;
			}

			$notice = get_transient( $this->get_notice_key() );
			if ( ! $notice ) {
				return;
			}

			delete_transient( $this->get_notice_key() );

			printf(
				'<div class="notice notice-%s is-dismissible"><p>%s</p></div>',
				esc_attr( 'success' === $notice['type'] ? 'success' : 'error' ),
				esc_html( $notice['message'] )
			);
		}

		public function render_page() {
			$license_key  = $this->client->get_license_key();
			$is_valid     = $this->client->is_valid();
			$status       = $this->client->get_status();
			$data         = $this->client->get_license_data();
			$company_name = class_exists( 'RamerLabs_Branding' ) ? 'RamerLabs' : get_bloginfo( 'name' );
			$company_url  = class_exists( 'RamerLabs_Branding' ) ? RamerLabs_Branding::company_url() : 'https://ramerlabs.com';
			?>
			<div class="wrap rlm-client-wrap">
				<div class="rlm-client-hero">
					<a class="rlm-client-hero__brand" href="<?php echo esc_url( $company_url ); ?>" target="_blank" rel="noopener noreferrer">
						<?php echo esc_html( $company_name ); ?>
					</a>
					<h1 class="rlm-client-hero__title"><?php echo esc_html( $this->page_title ); ?></h1>
				</div>

				<div class="rlm-client-card">
					<p>
						<strong><?php esc_html_e( 'Status:', 'ramerlabs-membership-pro' ); ?></strong>
						<span class="rlm-client-status <?php echo $is_valid ? 'rlm-client-status--active' : 'rlm-client-status--inactive'; ?>">
							<?php echo $is_valid ? esc_html__( 'Active', 'ramerlabs-membership-pro' ) : esc_html( ucfirst( $status ) ); ?>
						</span>
					</p>

					<?php if ( ! empty( $data['expires_at'] ) ) : ?>
						<p><strong><?php esc_html_e( 'Expires:', 'ramerlabs-membership-pro' ); ?></strong> <?php echo esc_html( $data['expires_at'] ); ?></p>
					<?php endif; ?>

					<form method="post" style="margin-top:20px;">
						<?php wp_nonce_field( 'rlm_client_license' ); ?>

						<table class="form-table">
							<tr>
								<th scope="row"><label for="license_key"><?php esc_html_e( 'License Key', 'ramerlabs-membership-pro' ); ?></label></th>
								<td>
									<input type="text" name="license_key" id="license_key" class="regular-text" value="<?php echo esc_attr( $license_key ); ?>" placeholder="RLM-XXXX-XXXX-XXXX">
									<p class="description">
										<?php esc_html_e( 'Enter the license key from your purchase confirmation email, then click Activate License.', 'ramerlabs-membership-pro' ); ?>
										<?php
										printf(
											' <a href="%s" target="_blank" rel="noopener noreferrer">%s</a>',
											esc_url( $company_url ),
											esc_html__( 'Purchase at ramerlabs.com', 'ramerlabs-membership-pro' )
										);
										?>
									</p>
								</td>
							</tr>
						</table>

						<p class="submit">
							<button type="submit" name="rlm_client_action" value="activate" class="button button-primary"><?php esc_html_e( 'Activate License', 'ramerlabs-membership-pro' ); ?></button>
							<button type="submit" name="rlm_client_action" value="check" class="button"><?php esc_html_e( 'Check License', 'ramerlabs-membership-pro' ); ?></button>
							<?php if ( $license_key ) : ?>
								<button type="submit" name="rlm_client_action" value="deactivate" class="button button-secondary" onclick="return confirm('<?php echo esc_js( __( 'Deactivate this license on this site?', 'ramerlabs-membership-pro' ) ); ?>');"><?php esc_html_e( 'Deactivate', 'ramerlabs-membership-pro' ); ?></button>
							<?php endif; ?>
						</p>
					</form>
				</div>

				<div class="rlm-client-footer">
					<a href="<?php echo esc_url( $company_url ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Need help? support@ramerlabs.com', 'ramerlabs-membership-pro' ); ?></a>
				</div>
			</div>
			<?php
		}

		private function set_notice( $result, $action ) {
			if ( is_wp_error( $result ) ) {
				$notice = array(
					'type'    => 'error',
					'message' => $result->get_error_message(),
				);
			} elseif ( is_array( $result ) && empty( $result['success'] ) ) {
				$notice = array(
					'type'    => 'error',
					'message' => isset( $result['message'] ) ? $result['message'] : __( 'We could not verify your license. Check your key or contact support@ramerlabs.com.', 'ramerlabs-membership-pro' ),
				);
			} elseif ( 'activate' === $action ) {
				$notice = array(
					'type'    => 'success',
					'message' => __( 'License activated successfully.', 'ramerlabs-membership-pro' ),
				);
			} else {
				$notice = array(
					'type'    => 'success',
					'message' => __( 'License deactivated successfully.', 'ramerlabs-membership-pro' ),
				);
			}

			set_transient( $this->get_notice_key(), $notice, 30 );
		}

		private function get_notice_key() {
			return 'rlm_client_notice_' . md5( $this->menu_slug );
		}
	}
}
