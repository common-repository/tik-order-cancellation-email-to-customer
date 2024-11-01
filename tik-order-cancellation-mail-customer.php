<?php

/**
 * Plugin Name: Tik Order Cancellation Email to Customer
 * Description: This plugin is an extension of WooCommerce for sending order cancellation email to customer
 * Version: 1.0.0
 * Author: Tawhidul Islam Khan
 * Text Domain: tik-order-cancellation-mail-customer
 * WC requires at least: 3.0
 * WC tested up to: 5.0.0
 * Domain Path: /languages/
 * License: GPL2
 */

/*
 * Released under the GPL license
 * http://www.opensource.org/licenses/gpl-license.php
 *
 * This is an add-on for WordPress
 * http://wordpress.org/
 *
 * **********************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 * **********************************************************************
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Tik_Order_Cancellation_Mail_Customer {


	public $version = '1.0.0';

	/**
	 * Instance of self
	 */

	private static $instance = null;

	private function __construct() {
		add_action( 'woocommerce_loaded', array( $this, 'init_plugin' ) );
		$this->define_constants();

	}

	public function init_plugin() {
		include_once __DIR__ . '/includes/email/class-tik-email-controller.php';
	}

	public function define_constants() {
		define( 'TIK_OCMC_DIR', __DIR__ );
	}

	public static function init() {
		if ( self::$instance === null ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
}


function tik_order_cancellation_mail_customer() {
	return Tik_Order_Cancellation_Mail_Customer::init();
}

tik_order_cancellation_mail_customer();
