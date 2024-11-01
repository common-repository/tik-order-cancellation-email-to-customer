<?php

class Tik_Email_Controller {


	public function __construct() {
		add_filter( 'woocommerce_email_classes', array( $this, 'custom_emails' ) );
	}

	public function custom_emails( $emails ) {
		include_once __DIR__ . '/class-tik-email-cancelled-order.php';

		$emails['Tik_Email_Order_Canceled'] = new Tik_Email_Cancelled_Order();
		return $emails;
	}
}

new Tik_Email_Controller();
