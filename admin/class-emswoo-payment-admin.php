<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://simplicity.rs
 * @since      1.0.0
 *
 * @package    emswoo-payment
 * @subpackage emswoo-payment/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    emswoo-payment
 * @subpackage emswoo-payment/admin
 * @author     Aleksandar Andrijevic <yu1nis@gmail.com>
 */
class EMS_Woo_Payment_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/emswoo-payment-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/emswoo-payment-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Include class for payment gateway
	 *
	 * @since    1.0.0
	 */
	public function include_ems_gateway_file() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-emswoo-payment-gateway.php';
	}

	/**
	 * Register gateway in array of WooCommerce payment methods
	 *
	 * @since    1.0.0
	 */
	public function add_ems_gateway($methods) {

		$methods[] = 'EMS_Woo_Payment_Gateway';
		
		return $methods;
	}

}
