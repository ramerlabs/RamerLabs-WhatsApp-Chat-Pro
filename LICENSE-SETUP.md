# License Manager setup — WhatsApp Chat Pro

Internal runbook for selling and licensing **RamerLabs WhatsApp Chat Pro**.

## Product registration

On **ramerlabs.com** (License Manager), the product is auto-seeded on activation/upgrade:

| Field | Value |
|-------|-------|
| Name | RamerLabs WhatsApp Chat Pro |
| Slug | `ramerlabs-whatsapp-chat-pro` |
| Type | plugin |
| Version | 1.0.0 |
| Max activations | 1 (per license) |

If missing, go to **License Manager → Products → Add Product** and use the values above.

## WooCommerce storefront (ramerlabs.com)

1. Create a WooCommerce product using copy from `PRODUCT-COPY.md`
2. Set featured image: `assets/images/product.png`
3. In License Manager, edit product `ramerlabs-whatsapp-chat-pro` and set **WooCommerce Product ID**
4. Enable **Auto License** in License Manager settings

## Release package

1. Zip the plugin folder (exclude `.git`)
2. Upload release in **License Manager → Releases** for slug `ramerlabs-whatsapp-chat-pro`
3. Tag version to match `RLWC_VERSION` in main plugin file

## Customer activation flow

1. Customer buys on ramerlabs.com
2. License key emailed automatically
3. Customer installs plugin and enters key at **WhatsApp Chat → License**
4. Activation hits `ramerlabs.com/wp-json/ramerlabs-license/v1/activate`

## Shortcode & blocks (for product docs)

- Gutenberg: **WhatsApp Chat Button** block
- Elementor: **WhatsApp Chat Button** widget
- Shortcode: `[rlwc_button text="Chat with sales" department="sales"]`

## Support

support@ramerlabs.com
