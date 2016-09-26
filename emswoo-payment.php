<?php
/**
 *
 * @link              http://simplicity.rs
 * @since             1.0.0
 * @package           emswoo-payment
 *
 * @wordpress-plugin
 * Plugin Name:       eMS payment gateway for WooCommerce
 * Plugin URI:        http://simplicity.rs/
 * Description:       eMS payment gateway for WooCommerce
 * Version:           1.0.2
 * Author:            Simplicity LLC
 * Author URI:        http://simplicity.rs
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       emswoo-payment
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

//On activate
function activate_emswoo_payment() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-emswoo-payment-activator.php';
	EMS_Woo_Payment_Activator::activate();
}

//On deactivate
function deactivate_emswoo_payment() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-emswoo-payment-deactivator.php';
	EMS_Woo_Payment_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_emswoo_payment' );
register_deactivation_hook( __FILE__, 'deactivate_emswoo_payment' );

require plugin_dir_path( __FILE__ ) . 'includes/class-emswoo-payment.php';

//Main run
function run_emswoo_payment() {
	$plugin = new EMS_Woo_Payment();
	$plugin->run();
}
run_emswoo_payment();
