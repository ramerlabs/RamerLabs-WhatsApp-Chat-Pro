<?php
/**
 * Plugin Name:       RamerLabs WhatsApp Chat Pro
 * Plugin URI:        https://ramerlabs.com
 * Description:       Smart WhatsApp chat widget with page routing, business hours, agent round-robin, GDPR consent, and click analytics.
 * Version:           1.0.4
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            RamerLabs
 * Author URI:        https://ramerlabs.com
 * License:           GPL-2.0-or-later
 * Text Domain:       ramerlabs-whatsapp-chat-pro
 */

defined( 'ABSPATH' ) || exit;

define( 'RLWC_VERSION', '1.0.4' );
define( 'RLWC_PLUGIN_FILE', __FILE__ );
define( 'RLWC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'RLWC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'RLWC_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'RLWC_PRODUCT_SLUG', 'ramerlabs-whatsapp-chat-pro' );

require_once RLWC_PLUGIN_DIR . 'includes/class-activator.php';
require_once RLWC_PLUGIN_DIR . 'includes/class-plugin.php';

register_activation_hook( __FILE__, array( 'RLWC_Activator', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'RLWC_Activator', 'deactivate' ) );

/**
 * @return RLWC_Plugin
 */
function rlwc() {
	return RLWC_Plugin::instance();
}

rlwc();
