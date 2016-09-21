<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://simplicity.rs
 * @since      1.0.0
 *
 * @package    emswoo-payment
 * @subpackage emswoo-payment/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    emswoo-payment
 * @subpackage emswoo-payment/public
 * @author     Aleksandar Andrijevic <yu1nis@gmail.com>
 */
class EMS_Woo_Payment_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/emswoo-payment-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/emswoo-payment-public.js', array( 'jquery' ), $this->version, false );

	}


	/**
	 * Register notify template file
	 *
	 * @since    1.0.0
	 */
	public function notify_page_template($page_template ) {

		$settings = get_option( 'woocommerce_emswoo-payment_settings');
		if ($settings) {

			$page_slug = $settings['store_notify_url'];

			if ( is_page( $page_slug ) ) {
		        $page_template = sprintf("%s/templates/notify-template.php", plugin_dir_path( dirname( __FILE__ ) ) );
		    }
	    }
	    return $page_template;
	}

	/**
	 * Remove EMS payment gateway for users who are not on testing list
	 *
	 * @since    1.0.0
	 */
	public function filter_woocommerce_gateways($args) {

		//Only for logged-in users, since this is better way to test it and there are no reason to not work when user is not logged in
		if(!is_user_logged_in()) {
			unset($args['emswoo-payment']);
		} else {
			//Get settings
			$settings = get_option( 'woocommerce_emswoo-payment_settings');
			if ($settings) {
				$current_user = wp_get_current_user();
				$testing_on = $settings['store_testing_on'];
				if ($testing_on == 'yes') {
					//Check if user is on list of test users
					$testing_user_mails = explode(',',$settings['store_testing_emails']);
					if (!in_array($current_user->data->user_email, $testing_user_mails) ) {
						unset($args['emswoo-payment']);
					}
				}
			}
		}
		return $args;
	}


	/**
	 * Redirects user to homepage if payment is not succesfull
	 *
	 * @since    1.0.1
	 */
	public function redirect_fail($order_id) {

		$order = new WC_Order( $order_id );

	 	if ($order->payment_method == 'emswoo-payment' and $order->status != 'completed') {
	 		wp_redirect(home_url());
	 		die();
	 	}
	}


}
