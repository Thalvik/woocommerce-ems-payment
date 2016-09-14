<?php

/**
 * Provide template for response
 *
 * @link       http://simplicity.rs
 * @since      1.0.0
 *
 * @package    emswoo-payment
 * @subpackage emswoo-payment/templates
 */


//Process order based on status
$ems_payment_response = new EMS_Woo_Payment_Response();
$ems_payment_response->process_response();