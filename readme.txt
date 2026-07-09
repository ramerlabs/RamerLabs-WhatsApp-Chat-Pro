=== RamerLabs WhatsApp Chat Pro ===
Contributors: ramerlabs
Tags: whatsapp, chat, woocommerce, support, live chat
Requires at least: 6.0
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 1.0.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Smart WhatsApp chat widget with page routing, business hours, agent round-robin, GDPR consent, and click analytics.

== Description ==

RamerLabs WhatsApp Chat Pro adds a floating WhatsApp button that does more than open a chat link.

* Route visitors by page (sales, support, billing)
* Business hours with reply-time messaging
* Pre-filled messages from product and post context
* Multiple agents with round-robin
* UTM and click tracking in WordPress admin
* GDPR consent modal before opening WhatsApp

== Installation ==

1. Upload the plugin folder to `/wp-content/plugins/`
2. Activate the plugin
3. Add agent phone numbers under WhatsApp Chat → Agents
4. Configure routing and settings
5. Enter your license key under WhatsApp Chat → License

== Changelog ==

= 1.0.4 =
* Require valid license before showing frontend widget, blocks, shortcode, and click tracking

= 1.0.3 =
* Fix: Use api.whatsapp.com links (avoids wa.me SSL/DNS issues on some networks)
* Fix: Convert local phone numbers to international format using default country code

= 1.0.2 =
* Fix: GDPR consent and other checkbox settings now save correctly when unchecked

= 1.0.1 =
* Billing agent in default seed data
* Gutenberg block, Elementor widget, and shortcode for inline buttons
* License Manager product auto-seed support

= 1.0.0 =
* Initial release
