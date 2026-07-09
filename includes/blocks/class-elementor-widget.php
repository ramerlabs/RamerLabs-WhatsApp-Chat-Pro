<?php
defined( 'ABSPATH' ) || exit;

class RLWC_Elementor_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'rlwc_whatsapp_button';
	}

	public function get_title() {
		return __( 'WhatsApp Chat Button', 'ramerlabs-whatsapp-chat-pro' );
	}

	public function get_icon() {
		return 'eicon-commenting-o';
	}

	public function get_categories() {
		return array( 'general' );
	}

	protected function register_controls() {
		$this->start_controls_section( 'content_section', array(
			'label' => __( 'WhatsApp Chat', 'ramerlabs-whatsapp-chat-pro' ),
		) );

		$this->add_control( 'button_text', array(
			'label'   => __( 'Button text', 'ramerlabs-whatsapp-chat-pro' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => '',
		) );

		$this->add_control( 'department', array(
			'label'   => __( 'Department', 'ramerlabs-whatsapp-chat-pro' ),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => '',
			'options' => array(
				''        => __( 'Auto (page routing)', 'ramerlabs-whatsapp-chat-pro' ),
				'sales'   => __( 'Sales', 'ramerlabs-whatsapp-chat-pro' ),
				'support' => __( 'Support', 'ramerlabs-whatsapp-chat-pro' ),
				'billing' => __( 'Billing', 'ramerlabs-whatsapp-chat-pro' ),
				'general' => __( 'General', 'ramerlabs-whatsapp-chat-pro' ),
			),
		) );

		$this->add_control( 'style', array(
			'label'   => __( 'Style', 'ramerlabs-whatsapp-chat-pro' ),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => 'button',
			'options' => array(
				'button' => __( 'Button', 'ramerlabs-whatsapp-chat-pro' ),
				'link'   => __( 'Link', 'ramerlabs-whatsapp-chat-pro' ),
			),
		) );

		$this->add_control( 'show_icon', array(
			'label'   => __( 'Show icon', 'ramerlabs-whatsapp-chat-pro' ),
			'type'    => \Elementor\Controls_Manager::SWITCHER,
			'default' => 'yes',
		) );

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		echo RLWC_Frontend::render_button( array(
			'text'       => $settings['button_text'],
			'department' => $settings['department'],
			'style'      => $settings['style'],
			'show_icon'  => 'yes' === $settings['show_icon'],
		) );
	}
}
