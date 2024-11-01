<?php

/**
 * Class Tik_Email_Cancelled_Order file.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Tik_Email_Cancelled_Order', false ) ) :

	/**
	 * Cancelled Order Email.
	 * An email sent to the admin when an order is cancelled.
	 *
	 * @extends WC_Email
	 */
	class Tik_Email_Cancelled_Order extends WC_Email {


		/**
		 * Constructor.
		 */
		public function __construct() {
			$this->id             = 'tik_cancelled_order';
			$this->title          = __( 'Tik Cancelled order', 'tik-order-cancellation-mail-customer' );
			$this->description    = __( 'Cancelled order emails are sent to chosen recipient(s) when orders have been marked cancelled (if they were previously processing or on-hold).', 'tik-order-cancellation-mail-customer' );
			$this->template_html  = 'admin-cancelled-order.php';
			$this->template_plain = 'plain/admin-cancelled-order.php';
			$this->template_base  = TIK_OCMC_DIR . '/templates/';
			$this->placeholders   = array(
				'{order_date}'              => '',
				'{order_number}'            => '',
				'{order_billing_full_name}' => '',
			);
			// Triggers for this email.
			add_action( 'woocommerce_order_status_processing_to_cancelled_notification', array( $this, 'trigger' ), 10, 2 );
			add_action( 'woocommerce_order_status_on-hold_to_cancelled_notification', array( $this, 'trigger' ), 10, 2 );

			// Call parent constructor.
			parent::__construct();
		}

		/**
		 * Get email subject.
		 *
		 * @since  3.1.0
		 * @return string
		 */
		public function get_default_subject() {
			return __( '[{site_title}]: Order #{order_number} has been cancelled', 'tik-order-cancellation-mail-customer' );
		}

		/**
		 * Get email heading.
		 *
		 * @since  3.1.0
		 * @return string
		 */
		public function get_default_heading() {
			return __( 'Order Cancelled: #{order_number}', 'tik-order-cancellation-mail-customer' );
		}

		/**
		 * Trigger the sending of this email.
		 *
		 * @param int            $order_id The order ID.
		 * @param WC_Order|false $order    Order object.
		 */
		public function trigger( $order_id, $order = false ) {
			$this->setup_locale();

			if ( $order_id && ! is_a( $order, 'WC_Order' ) ) {
				$order = wc_get_order( $order_id );
			}

			if ( is_a( $order, 'WC_Order' ) ) {
				$this->object                                    = $order;
				$this->placeholders['{order_date}']              = wc_format_datetime( $this->object->get_date_created() );
				$this->placeholders['{order_number}']            = $this->object->get_order_number();
				$this->placeholders['{order_billing_full_name}'] = $this->object->get_formatted_billing_full_name();
			}
			
			$form_field_recipient = get_option( 'woocommerce_tik_cancelled_order_settings' )['recipient'];
			$this->recipient = $form_field_recipient ? $form_field_recipient : $order->get_billing_email();

			if ( $this->is_enabled() && $this->get_recipient() ) {
				$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
			}

			$this->restore_locale();
		}

		/**
		 * Get content html.
		 *
		 * @return string
		 */
		public function get_content_html() {
			return wc_get_template_html(
				$this->template_html,
				array(
					'order'              => $this->object,
					'email_heading'      => $this->get_heading(),
					'additional_content' => $this->get_additional_content(),
					'sent_to_admin'      => false,
					'plain_text'         => false,
					'email'              => $this,
				),
				'tik-order-cancellation-mail-customer/',
				$this->template_base
			);
		}

		/**
		 * Get content plain.
		 *
		 * @return string
		 */
		public function get_content_plain() {
			return wc_get_template_html(
				$this->template_plain,
				array(
					'order'              => $this->object,
					'email_heading'      => $this->get_heading(),
					'additional_content' => $this->get_additional_content(),
					'sent_to_admin'      => false,
					'plain_text'         => true,
					'email'              => $this,
				),
				'tik-order-cancellation-mail-customer/',
				$this->template_base
			);
		}

		/**
		 * Default content to show below main email content.
		 *
		 * @since  3.7.0
		 * @return string
		 */
		public function get_default_additional_content() {
			return __( 'Thanks for reading.', 'woocommerce' );
		}

		/**
		 * Initialise settings form fields.
		 */
		public function init_form_fields() {
			/* translators: %s: list of placeholders */
			$placeholder_text  = sprintf( __( 'Available placeholders: %s', 'tik-order-cancellation-mail-customer' ), '<code>' . esc_html( implode( '</code>, <code>', array_keys( $this->placeholders ) ) ) . '</code>' );
			$this->form_fields = array(
				'enabled'            => array(
					'title'   => __( 'Enable/Disable', 'tik-order-cancellation-mail-customer' ),
					'type'    => 'checkbox',
					'label'   => __( 'Enable this email notification', 'tik-order-cancellation-mail-customer' ),
					'default' => 'yes',
				),
				'recipient'          => array(
					'title'       => __( 'Recipient(s)', 'tik-order-cancellation-mail-customer' ),
					'type'        => 'text',
					'description' => __( 'Enter recipients (comma separated) for this email. Defaults to order customer billing email', 'tik-order-cancellation-mail-customer' ),
					'placeholder' => '',
					'default'     => '',
					'desc_tip'    => true,
				),
				'subject'            => array(
					'title'       => __( 'Subject', 'tik-order-cancellation-mail-customer' ),
					'type'        => 'text',
					'desc_tip'    => true,
					'description' => $placeholder_text,
					'placeholder' => $this->get_default_subject(),
					'default'     => '',
				),
				'heading'            => array(
					'title'       => __( 'Email heading', 'tik-order-cancellation-mail-customer' ),
					'type'        => 'text',
					'desc_tip'    => true,
					'description' => $placeholder_text,
					'placeholder' => $this->get_default_heading(),
					'default'     => '',
				),
				'additional_content' => array(
					'title'       => __( 'Additional content', 'tik-order-cancellation-mail-customer' ),
					'description' => __( 'Text to appear below the main email content.', 'tik-order-cancellation-mail-customer' ) . ' ' . $placeholder_text,
					'css'         => 'width:400px; height: 75px;',
					'placeholder' => __( 'N/A', 'tik-order-cancellation-mail-customer' ),
					'type'        => 'textarea',
					'default'     => $this->get_default_additional_content(),
					'desc_tip'    => true,
				),
				'email_type'         => array(
					'title'       => __( 'Email type', 'tik-order-cancellation-mail-customer' ),
					'type'        => 'select',
					'description' => __( 'Choose which format of email to send.', 'tik-order-cancellation-mail-customer' ),
					'default'     => 'html',
					'class'       => 'email_type wc-enhanced-select',
					'options'     => $this->get_email_type_options(),
					'desc_tip'    => true,
				),
			);
		}
	}

endif;
