<?php

/**
 * Response class
 *
 *
 * @link       http://simplicity.rs
 * @since      1.0.0
 *
 * @package    emswoo-payment
 * @subpackage emswoo-payment/includes
 */

/**
 * @since      1.0.0
 * @package    emswoo-payment
 * @subpackage emswoo-payment/includes
 * @author     Aleksandar Andrijevic <yu1nis@gmail.com>
 */

class EMS_Woo_Payment_Response {
	
	//Process the response
	public function process_response() {

		//If parametars are not set
		$store_id = isset($_POST['MerchantID']) ? $_POST['MerchantID'] : die();
		$order_id = isset($_POST['OrderID']) ? $_POST['MerchantID'] : die();
		$ems_id = isset($_POST['EMSID']) ? $_POST['EMSID'] : die();
		$status = isset($_POST['OrderStatus']) ? $_POST['OrderStatus'] : die();
		$signature = isset($_POST['Signature']) ? $_POST['Signature'] : die();

		// error_log('StoreID:' . $store_id );
		// error_log('OrderID:' . $order_id );
		// error_log('EmsID:' . $ems_id );
		// error_log('Status:' . $status );
		// error_log('Signature:' . $signature );

		//Get store key from options
		$settings = get_option( 'woocommerce_emswoo-payment_settings');
		$store_key = htmlspecialchars_decode($settings['store_key']);

		$ems_status = new eMSStatus($store_id,$order_id,$ems_id,$status,$signature,$store_key,"SIMPLE");

		//Get order
		$customer_order = new WC_Order( $order_id );


		//Check for signature
		$check_signature = $ems_status->CheckNotificationSimple();
		if (!$check_signature) {
			//Not a valid signature
			$customer_order->add_order_note( __( 'EMS payment not successfull, wrong signature.', 'emswoo-payment' ) );
			$ems_status->LogToFile();
			die();
		}

		$ems_status->LogToFile();

		//Process results based on status
		switch ($status) {

			//Transaction authorized
			case '100':

				//Add notice
				$customer_order->add_order_note( __( 'EMS payment completed.', 'emswoo-payment' ) );

				// Mark order as Paid
				$customer_order->payment_complete($ems_id);

				global $woocommerce;
				if ($woocommerce != null) {
					//Empty cart
					$woocommerce->cart->empty_cart();
				}

				//TODO: make an option that when payment is completed the order should be also completed for virtual products
				$customer_order->update_status('completed', __( 'EMS order completed.', 'emswoo-payment' ), true);
			break;

			//Transaction not authorized, system error
			case '200':
				$customer_order->add_order_note( __( 'EMS payment not successfull, transaction is not authorized. EMS system error.', 'emswoo-payment' ) );
				$customer_order->update_status('failed', __( 'EMS order failed.', 'emswoo-payment' ), true);
			break;

			//Transaction not authorized, wrong card data(number, cvv or exp.date)
			case '201':
				$customer_order->add_order_note( __( 'EMS payment not successfull, transaction is not authorized, wrong card data.', 'emswoo-payment' ) );
				$customer_order->update_status('failed', __( 'EMS order failed.', 'emswoo-payment' ), true);
			break;

			//Seems same as 201, but 202 code is not in documentation. It happens when user try to unsuccesfully pay 3 times in a row
			case '202':
				$customer_order->add_order_note( __( 'EMS payment not successfull, transaction is not authorized, wrong card data.', 'emswoo-payment' ) );
				$customer_order->update_status('failed', __( 'EMS order failed.', 'emswoo-payment' ), true);
			break;

			//Unknown transaction result
			case '210':
				$customer_order->add_order_note( __( 'EMS payment not successfull, unknown transaction result.', 'emswoo-payment' ) );
				$customer_order->update_status('failed', __( 'EMS order failed.', 'emswoo-payment' ), true);
			break;

			//Buyer has not agreed with ems shop agreement
			case '300':
				$customer_order->add_order_note( __( 'EMS payment not successfull, buyer has not agreed with ems shop agreement.', 'emswoo-payment' ) );
				$customer_order->update_status('failed', __( 'EMS order failed.', 'emswoo-payment' ), true);
			break;

			//Buyer has not logged in into ems system
			case '301':
				$customer_order->add_order_note( __( 'EMS payment not successfull, buyer has not logged in into ems system.', 'emswoo-payment' ) );
				$customer_order->update_status('failed', __( 'EMS order failed.', 'emswoo-payment' ), true);
			break;

			//Buyer has not choosed the type of card
			case '302':
				$customer_order->add_order_note( __( 'EMS payment not successfull, buyer has not choosed the type of card.', 'emswoo-payment' ) );
				$customer_order->update_status('failed', __( 'EMS order failed.', 'emswoo-payment' ), true);
			break;

			//Captured, transaction is confirmed
			case '400':
				$customer_order->add_order_note( __( 'Funds on EMS has been captured.', 'emswoo-payment' ) );
			break;

			//Voided, transaction is cancelled
			case '401':
				$customer_order->add_order_note( __( 'Transaction is cancelled.', 'emswoo-payment' ) );
			break;

			//Credit requested, the merchant has sent ems request for transaction cancelling
			case '402':
				$customer_order->add_order_note( __( 'The merchant has sent EMS request for transaction cancelling.', 'emswoo-payment' ) );
			break;

			//Credited, transaction is cancelled
			case '403':
				$customer_order->add_order_note( __( 'Transaction is cancelled.', 'emswoo-payment' ) );
			break;
			
			//Unknown status
			default:
				$customer_order->add_order_note( __( 'Unknown result.', 'emswoo-payment' ) );
				$customer_order->update_status('failed', __( 'EMS order failed.', 'emswoo-payment' ), true);
			break;
		}


		die();


	}

}