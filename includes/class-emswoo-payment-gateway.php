<?php

/**
 * Gateway class
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

if ( class_exists( 'WC_Payment_Gateway' ) ) {

	class EMS_Woo_Payment_Gateway extends WC_Payment_Gateway {

		/**
		 * Constructor
		 *
		 * @since    1.0.0
		 */
		public function __construct() {

			$this->id = 'emswoo-payment';
			$this->method_title = __( 'EMS WooCommerce payment', 'emswoo-payment' );
			$this->method_description = __( 'EMS payment Plug-in for WooCommerce', 'emswoo-payment' );
			$this->title = __( 'EMS WooCommerce payment', 'emswoo-payment' );
			$this->icon = null;
			$this->has_fields = true;
			$this->init_form_fields();
			$this->init_settings();

			//Get stored settings
			$this->title = $this->get_option('title');
	        $this->description = $this->get_option('description');
	        $this->store_language = $this->get_option('store_language');
	        $this->store_name = $this->get_option('store_name');
	        $this->store_id = $this->get_option('store_id');
	        $this->store_key = $this->get_option('store_key');
	        $this->testing_on = $this->get_option('store_testing_on');
	        $this->testing_emails = $this->get_option('store_testing_emails');
			
			// Save settings
			if ( is_admin() ) {
				add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
			}		
		}

		/**
		 * Build the administration fields for this specific Gateway
		 *
		 * @since    1.0.0
		 */
		public function init_form_fields() {


			//Get all pages
			$select_pages = [];

			$pages = get_pages(array(
				'sort_order' => 'asc',
				'post_type' => 'page',
				'post_status' => 'publish' 
			));

			foreach ($pages as $key => $page) {
				$select_pages[$page->post_name] = $page->post_title;
			}


			//Add form fields
			$this->form_fields = array(
				'enabled' => array(
					'title'		=> __( 'Enable / Disable', 'emswoo-payment' ),
					'label'		=> __( 'Enable this payment gateway', 'emswoo-payment' ),
					'type'		=> 'checkbox',
					'default'	=> 'no',
				),
				'title' => array(
					'title'		=> __( 'Title', 'emswoo-payment' ),
					'type'		=> 'text',
					'desc_tip'	=> __( 'Payment title the customer will see during the checkout process.', 'emswoo-payment' ),
					'default'	=> __( 'Platna kartica', 'emswoo-payment' ),
					'css'		=> 'width:650px;'
				),
				'description' => array(
					'title'		=> __( 'Description', 'emswoo-payment' ),
					'type'		=> 'textarea',
					'desc_tip'	=> __( 'Payment description the customer will see during the checkout process.', 'emswoo-payment' ),
					'default'	=> __( 'eMS d.o.o. enables credit card payment', 'emswoo-payment' ),
					'css'		=> 'max-width:650px;'
				),
				'store_language' => array(
	                'title' => __('Store Language', 'emswoo-payment'),
	                'desc_tip' => __('Pick a language that your store is using', 'emswoo-payment'),
	                'type' => 'select',
	                'default' => 'SRB',
	                'options' => array(
	                     'SRB' => __('Serbian'),
	                     'ENU' => __('English'),
	                ),
	            ),                
				'store_name' => array(
					'title' => __('Store Name', 'emswoo-payment'),
					'type' => 'text',
					'description' => __('This should match your store name from eMS merchant portal', 'emswoo-payment'),
					'default' => '',
					'desc_tip' => true,
					'css'		=> 'width:650px;'
				),
				'store_id' => array(
					'title' => __('Store ID', 'emswoo-payment'),
					'type' => 'text',
					'description' => __('This should match your store ID from eMS merchant portal', 'emswoo-payment'),
					'default' => '',
					'desc_tip' => true,
					'css'		=> 'width:650px;'
				),
				'store_key' => array(
	                'title' => __('Store Key', 'emswoo-payment'),
	                'type' => 'text',
	                'description' => __('This should match your store key from eMS merchant portal', 'emswoo-payment'),
	                'default' => '',
	                'desc_tip' => true,
	                'css'		=> 'width:650px;'
	            ),
	            'store_notify_url' => array(
	                'title' => __('Store Notify page URL', 'emswoo-payment'),
	                'desc_tip' => __('Create and select page that will be used as notify url', 'emswoo-payment'),
	                'type' => 'select',
	                'default' => 'SRB',
	                'options' => $select_pages,
	            ),
	            'store_testing_on' => array(
	                'title' => __('Testing mode?', 'emswoo-payment'),
	                'type' => 'checkbox',
	                'description' => __('Check this if you want EMS payment type to be in test mode', 'emswoo-payment'),
	                'default' => '',
	                'desc_tip' => true,
	                'css' => ''
	            ), 
	            'store_testing_emails' => array(
	                'title' => __('Comma seperated user emails for testing purposes', 'emswoo-payment'),
	                'type' => 'text',
	                'description' => __('Comma seperated user emails for testing purposes(only them will see option for EMS payment)', 'emswoo-payment'),
	                'default' => '',
	                'desc_tip' => true,
	                'css' => 'width:650px;'
	            ),       
			);		
		}
		

		/**
		 * Submit payment and handle response
		 *
		 * @since    1.0.0
		 */

		public function process_payment( $order_id ) {

			//Not sure if really needed
			mb_language('uni'); 
	  		mb_internal_encoding('UTF-8');


			global $woocommerce;
			
			//Get order
			$customer_order = new WC_Order( $order_id );
			$response = 0;

			//Crate EMS cart instance
			$ems_cart = new eMSCart($order_id, $this->store_language);

			//Get items from WooCommerce cart
			$woo_cart_items = $customer_order->get_items();

			//Add items to eMSCartItem instance
			foreach ($woo_cart_items as $item) {                                
	            $ems_cart_item = new eMSCartItem(
	            		$item['name'], 
	            		$item['product_id'], 
	            		$item['qty'], 
	            		$item['line_subtotal'], 
	            		$item['line_tax'], 
	            		$item['line_subtotal_tax'], 
	            		$item['line_total']
	            );
	            $ems_cart->AddCartItem($ems_cart_item);
	        }

	        //Crate EMS Merchant Info instance
	        $ems_shop = new eMSCartMerchantInfo($this->store_name, $this->store_id);

	        //Set return url
	        $ems_shop->SetReturnUrl($customer_order->get_checkout_order_received_url());

	        //Set cart merchant info
	        $ems_cart->SetMerchantInfo($ems_shop);


	        //Set total amounts for cart
			$ems_cart->SetShippingTotalAmount($customer_order->get_total_shipping());
			$ems_cart->SetItemTotalAmount($customer_order->get_total());
			$ems_cart->SetDiscountTotalAmount($customer_order->get_total_discount());
			$ems_cart->SetTaxTotalAmount($customer_order->get_total_tax());
			$ems_cart->SetTotalAmountToPay($customer_order->get_total());

			//Sign cart
			$ems_cart->SignCartSimple(htmlspecialchars_decode($this->store_key));


			$ems_checkout_response = $ems_cart->CheckoutServer2Server();

			//Set response parameters
			$response_id = $ems_cart->cart_eMsResponse->getResponseID();
			$response_signature = $ems_cart->cart_eMsResponse->getResponseSignature();
			$redirect_url = $ems_cart->cart_eMsResponse->getRedirectURL();
			$err_code = $ems_cart->cart_eMsResponse->getErrorCode();
			$err_message = $ems_cart->cart_eMsResponse->getErrorMessage();

			if ($err_code == 0) {

				//Redirect user
				return array(
					'result'   => 'success',
					'redirect' => $redirect_url,
				);
			} else {
				wc_add_notice( 'There was an error processing payment! Please contact us.', 'error' );
				$customer_order->add_order_note( 'Error code: ' . $err_code . '<br>Error message:' . $err_message  );
			}


		}


	}

}