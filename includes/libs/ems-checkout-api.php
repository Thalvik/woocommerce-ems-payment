<?php
 /**
  * Klasa kreira stavku korpe koja se salje 
  * Za svaku stavku korpe potrebno je kreirati novu instancu 
  * Obavezna polja za svaku stavku su Opis ($item_description), jedincana cena ($item_unit_price), kolicina ($item_qty), Ukupan iznos($item_total)
  */
  class eMSCartItem {
	var $item_display_order_no;
    var $item_description;
	var $item_code; 
    var $item_unit_price;
    var $item_quantity;
	var $item_tax_percatnge;
	var $item_tax_amount;
	var $item_shipping_price;
	var $item_discount_amount;
	var $item_total;
    
    /**
     * @param string  $item_description naziv proizvoda -- obavezan
     * @param string  $item_code sifra proizvoda u sistemu prodavca -- obavezan
     * @param integer $item_quantity broj proizvoda u korpi -- obavezan
     * @param double  $item_unit_price jedinacna cena proizvoda -- obavezan
     * @param string  $item_total ukupan iznos za stavku korpe - obavezan
     * @param integer $tax_percentage procenta obracunatog poreza na promet 
	 * @param double $tax_amount iznos poreza na promet 
     */
    function eMSCartItem($description, $code, $quantity, $unit_price, $tax_percatnge, $tax_amount, $total) {
      $this->item_description = $description; 
      $this->item_code= $code;
      $this->item_unit_price = $unit_price;
      $this->item_quantity = $quantity;
	  $this->item_tax_percatnge = $tax_percatnge;  
	  $this->item_tax_amount = $tax_amount;
	  $this->item_total = $total;
    }
    
	/*
	* Za svaku stavku korpe obracunava porez po istoj stopi
	* 
	*/
    function CalculateTax($tax_percatnge,$tax_amount) 
	{
		$this->item_tax_amount = rnd(($this->item_tax_percatnge * $this->item_tax_amount/100),2); 
    }
	/**
	* @param double $discount_amount iznos odobrenog popusta 
	*
	*/
	function SetDiscount($discount_amount) {
      $this->item_discount_amount = $discount_amount;
    }
	/**
	* @param integer $item_display_order redni broj stavke u korpi 
	*
	*/
	function SetItemNo($item_display_order)
	{
		$this->item_display_order_no = $item_display_order;
	}
	/**
	* @param double $discount_amount iznos odobrenog popusta 
	*
	*/
	function ToXML()
	{
		return '<Item Ordering="'.$this->item_display_order_no.'"><Description>'.$this->item_description.'</Description><Code>'.$this->item_code.'</Code><UnitPrice>'.$this->item_unit_price.'</UnitPrice><Quantity>'.$this->item_quantity.'</Quantity><Discount>'.$this->item_discount_amount.'</Discount><TaxPercent>'.$this->item_tax_percatnge.'</TaxPercent><TaxAmount>'.$this->item_tax_amount.'</TaxAmount><ShippingPrice>'.$this->item_shipping_price.'</ShippingPrice><TotalPrice>'.$this->item_total.'</TotalPrice></Item>';
	}
  }
  
  
  
  /**
  * Klasa za podatke o isporuci
  * Obavezan je za svaku korpu  
  * jedn korpa moze sadrazti samo jednu instancu ove klase i on vazi sve stavke u okviru nje.  
  * Obavezna polja za podatke o isporuci su...
  */
  class eMSCartShippingInfo 
  {
	var $recipient_name;
	var $recipient_address;
	var $recpient_city;
	var $recipient_postal_code;
	var $recipent_country;
	var $recipient_email_address;
	var $recipient_contact_phone;
	var $recipient_contact_phone2;
	var $recipient_PAK;
	var $total_shipping_price; 
	
	/*
	 * @param string  $name ime i prezime primaoca posilje -- obavezan
     * @param string  $address ulica i broj primaoca posiljke -- obavezan
     * @param string  $city naziv grada primaoca -- obavezan
     * @param double  $postal_code sifra odredisne poste primaoca -- obavezan
     * @param string  $country naziv drazave primaoca - obavezan
	*/
	function eMSCartShippingInfo($name,$address,$city,$postal_code,$country)
	{
		$this->recipient_name = $name;
		$this->recipient_address = $address;
		$this->recpient_city = $city;
		$this->recipient_postal_code = $postal_code;
		$this->recipent_country = $country;
	}
	
	function SetShippingPrice($total_shipping_price)
	{
	   $this->total_shipping_price = $total_shipping_price;
	}
	
	function SetContactPhone($phone1,$phone2="")
	{
		$this->recipient_contact_phone = $phone1;
		$this->recipient_contact_phone2 = $phone2;
	}
	function SetEmailAddress($email)
	{
	   $this->recipient_email_address = $email;
	}
	
	function SetPackageUniversalCode($PAK)
	{
	  $this->recipient_PAK = $PAK;
	}
	
	function ToXml()
	{
	  return '<ShippingInfo><Name>'.$this->recipient_name.'</Name><Address>'.$this->recipient_address.'</Address><City>'.$this->recpient_city.'</City><Postal>'.$this->recipient_postal_code.'</Postal><Country>'.$this->recipent_country.'</Country><Email>'.$this->recipient_email_address.'</Email><Phone>'.$this->recipient_contact_phone.'</Phone><Phone2>'.$this->recipient_contact_phone2.'</Phone2><PAK>'.$this->recipient_PAK.'</PAK></ShippingInfo>';
	}
  }
  
  
   /**
  * Klasa za podatke o prodavnici
  * Obavezna je za svaku korpu koja se salje 
  * jedn korpa moze sadrazti samo jednu instancu ove klase i on vazi sve stavke u okviru nje.  
  * Obavezna polja za podatke o prodavnici su Identifikator prodavnice u okviru eMS sistema ($merchant_id) i naziv prodavnice ($merchant_name)
  */
  class eMSCartMerchantInfo 
  {
	var $merchant_name; 
	var $merchant_emsid;
	var $merchant_address; 
	var $merchant_city;
	var $merchant_country;
	var $merchant_returnurl;
	/* 
	 * @param string  $name naziv internet prodavnice -- obavezan
     * @param string  $emsid identifikator u okviru eMS sistema- obavezan
	*/
	function eMSCartMerchantInfo($name,$emsid)
	{
		$this->merchant_name = $name;
		$this->merchant_emsid = $emsid;
	}
	
	function SetAddress($address,$city,$country)
	{
		$this->merchant_address = $address;
		$this->merchant_city = $city;
		$this->merchant_country=$country;
	}
	
	function SetReturnUrl($returnurl)
	{
		$this->merchant_returnurl = $returnurl;
	}
	
	function ToXml()
	{
		return '<MerchantInfo><ID>'.$this->merchant_emsid.'</ID><Name>'.$this->merchant_name.'</Name><Address>'.$this->merchant_address.'</Address><City>'.$this->merchant_city.'</City><Country>'.$this->merchant_country.'</Country><BackUrl>'.$this->merchant_returnurl.'</BackUrl></MerchantInfo>';
	}
  }
  
  
  /**
  * Klasa za podatke o kupcu
  * Obavezna je za svaku korpu koja se salje 
  * Jedna korpa moze sadrazti samo jednu instancu ove klase i on vazi sve stavke u okviru nje.  
  * Obavezna su sva polja
  */
  class eMSCartBillingInfo
  {
	var $billing_firstname;
	var $billing_lastname;
	var $billing_email_address;
	
	function eMSCartBillingInfo($firstname,$lastname,$email)
	{
	   $this->billing_firstname= $firstname;
	   $this->billing_lastname = $lastname;
	   $this->billing_email_address = $email;
	}
	
	function ToXml()
	{
		return '<BillingInfo><FirstName>'.$this->billing_firstname.'</FirstName><LastName>'.$this->billing_lastname.'</LastName><Email>'.$this->billing_email_address.'</Email></BillingInfo>';
	}
	
  } 
  
  /**
  * Klasa za podatke placanju. Sumarni podaci o korpi
  * Obavezna je za svaku korpu koja se salje 
  * Jedna korpa moze sadrazti samo jednu instancu ove klase i on vazi sve stavke u okviru nje.  
  * Obavezna su sva polja
  */
  class eMSCartTotals
  {
    var $cart_currency_code="RSD";
	var $cart_item_sum;
	var $cart_total_discount;
	var $cart_total_shipping;
	var $cart_total_tax;
	var $cart_total_to_pay;
	
	function eMSCartTotals($item_sum,$total_discount,$total_shipping,$total_tax,$total_to_pay)
	{
		$this->cart_item_sum = $item_sum;
		$this->cart_total_discount = $total_discount;
		$this->cart_total_shipping = $total_shipping;
		$this->cart_total_tax = $total_tax;
		$this->cart_total_to_pay = $total_to_pay;
	}
	
	function ToXml()
	{
		return '<TotalAmounts CurCode="'.$this->cart_currency_code.'"><Items>'.$this->cart_item_sum.'</Items><Discount>'.$this->cart_total_discount.'</Discount><Shipping>'.$this->cart_total_shipping.'</Shipping><Tax>'.$this->cart_total_tax.'</Tax><Total>'.$this->cart_total_to_pay.'</Total></TotalAmounts>';
	}
	
	function ToString()
	{
		return $this->cart_item_sum.$this->cart_total_discount.$this->cart_total_shipping.$this->cart_total_tax.$this->cart_total_to_pay;
	}

  }
  
  /**
  * Klasa za eMS korpu
  * Obavezna je za svaki request 
  * Obavezna su sva polja
  */
  class eMSCart
  {
	var $cart_ID;
	var $cart_language;
	var $cart_expiration;
	var $cart_merchant_info;
	var $cart_shipping_info;
	var $cart_billing_info;
	var $cart_totals;
	var $cart_items;
	var $cart_signature;
	var $cart_signature_type = "SIMPLE";
	var $cart_key;
	var $cart_eMsRequest;
	var $cart_eMsResponse;
	
	function eMSCart($id,$language,$expiration=30)
	{
		$this->cart_ID = $id;
		$this->cart_language = $language;
		$this->cart_expiration = $expiration;
		$this->item_nuber = 0;
		$this->cart_items = array();
		$this->cart_totals = new eMSCartTotals(-1,-1,-1,-1,-1);
	}
	
	function SetShippingInfo($shipping_info)
	{
	  $this->cart_shipping_info = $shipping_info;
	  $this->cart_totals->cart_total_shipping = $shipping_info->total_shipping_price;
	}
	
	function SetBillingInfo($billing_info)
	{
	  $this->cart_billing_info = $billing_info;
	}
		
	function SetMerchantInfo($mercahnt_info)
	{
	  $this->cart_merchant_info = $mercahnt_info;
	}
	
	function SetShippingTotalAmount($shipping_total_amount)
	{ 
		$this->cart_totals->cart_total_shipping = $shipping_total_amount;
	}
	
	function SetItemTotalAmount($item_total_amount)
	{ 
		$this->cart_totals->cart_item_sum = $item_total_amount;
	}
	
	function SetDiscountTotalAmount($discount_total_amount)
	{
	    $this->cart_totals->cart_total_discount = $discount_total_amount;
	}
	
	function SetTaxTotalAmount($tax_total_amount)
	{
	    $this->cart_totals->cart_total_tax = $tax_total_amount;
	}
	
	function SetTotalAmountToPay($total_to_pay_amount) 
	{
	   $this->cart_totals->cart_total_to_pay = $total_to_pay_amount;
	}
	
	
	function AddCartItem($emscartitem)
	{
		$this->item_nuber = $this->item_nuber+1;
		$emscartitem->item_display_order_no = $this->item_nuber;
		$this->cart_items[] = $emscartitem;
	}
	
	function SignCartSimple($mercahnt_key)
	{
	  $this->cart_signature_type = "SIMPLE";
	  $string_for_signing = $this->cart_ID.$this->cart_signature_type.$this->cart_merchant_info->merchant_emsid.$this->cart_totals->ToString().$mercahnt_key;
	  $this->cart_signtrure = sha1($string_for_signing);
	  $this->cart_key = $mercahnt_key;
	}
	
	function SignCartPKI($mercahnt_certificate)
	{
	   $this->cart_signture_type = "PKI";
	  $string_for_signing = $this->cart_ID.$this->cart_signture_type.$this->cart_merchant_info->merchant_emsid.$this->cart_totals->ToString().$mercahnt_key;
	  /*load certificate from store and sign */
	}
	function CalculateTotals()
	{
		$item_sum=0;
		$total_discount=0;
		$total_shipping=0;
		$total_tax=0 ;
		$total_to_pay=0;
		
		foreach ($this->cart_items as $cartitem)
		{
		   $item_sum = $item_sum + ($cartitem->item_unit_price * $cartitem->item_quantity);
		   $total_discount = $total_discount + $cartitem->item_discount_amount;
		   $total_shipping = $total_shipping + $cartitem->item_shipping_price;
		   $total_tax = $total_tax + $cartitem->item_tax_amount;
		   $total_to_pay = $total_to_pay + $cartitem->item_total;
		}
		$this->cart_totals = new eMSCartTotals($item_sum,$total_discount,$total_shipping,$total_tax,$total_to_pay);
	}
	
	function CheckForNulls()
	{
	  if (is_null($this->cart_merchant_info))
	  {
	    $this->cart_merchant_info = new eMSCartMerchantInfo("","");
	  }
	  if (is_null($this->cart_shipping_info))
	  {
	    $this->cart_shipping_info = new eMSCartShippingInfo("","","","","");
	  }
	  if (is_null($this->cart_billing_info))
	  {
	    $this->cart_billing_info = new eMSCartBillingInfo("","","");
	  }
	}
	
	
	function ToXml()
	{
		$this->CheckForNulls();
		$cart_xml = '';
		$items_xml = '';
		foreach ($this->cart_items as $cartitem)
		{
		   $items_xml = $items_xml.$cartitem->ToXml();
		}
		$cart_xml = $cart_xml = '<?xml version="1.0" encoding="utf-8"?><Order ID="'.$this->cart_ID.'" Lang="'.$this->cart_language.'" Timeout="'.$this->cart_expiration.'" Signature="'.$this->cart_signtrure.'" SignatureType="'.$this->cart_signature_type.'">'.$this->cart_merchant_info->ToXml().$this->cart_shipping_info->ToXml().$this->cart_billing_info->ToXml().$this->cart_totals->ToXml().'<Items>'.$items_xml.'</Items></Order>';
		return $cart_xml;
	}

	function CheckoutServer2Server() 
	{
      require_once('nusoap/nusoap.php');
	  require_once('googlelog.php');
	  require_once('ems-response.php');
	  $client = new nusoap_client('http://91.239.151.44/PaymentGateway/Service/OrderInit.asmx?wsdl',true);
	  $client->soap_defencoding = 'UTF-8'; 
          $client->decode_utf8 = false;
	  $log = new GoogleLog(
	  	plugin_dir_path( dirname( __FILE__ ) ) . 'libs/logs/demo_errorlog.txt',
	  	plugin_dir_path( dirname( __FILE__ ) ) . 'libs/logs/demo_messagelog.txt'
	  );
	  $log->logLevel = L_ALL;
	  /* Otkomentarisite ovaj red ukoliko imate problema sa logovanjem 
	  * $log->logLevel = L_OFF;
	  */
	  $err = $client->getError();
	  if ($err) 
	  {
		 $log->LogError($err);
	     return 'SOAP Client Error:'.$err;
      }
	  $this->cart_eMsRequest=$this->ToXml();
	  $attempt = $log->LogRequest("****REQUEST****".$this->cart_eMsRequest);
	  $result = $client->call('SendOrder', array('request' => $this->cart_eMsRequest));
	  $attempt = $log->LogResponse("****RESPONSE****".$client->response);
	  $err = $client->getError();
	  if ($err)
	  {
	    $log->LogError($err);
		return 'SOAP Client Error'.$err;
	  }
	  else
	  {
		$this->cart_eMsResponse = new eMSResponse($result["SendOrderResult"],$this->cart_key,$this->cart_signature_type);
		if ($this->cart_eMsResponse->IsValid())
		  return $this->cart_eMsResponse->redirect_url;
		else
		  return "Response Error. ErrorCode:".$this->cart_eMsResponse->err_code." Error Description: ".$this->cart_eMsResponse->err_message;
	  }
	}
  }
	
	/**
    * Klasa za eMS notifikacije
    * Obavezna je za svaki request 
    * Obavezna su sva polja
	* pre poziva provere potrebno je 
	* postaviti odgovrajuci kljuc
   */
	class eMSStatus 
	{
		var $merchantid;
        var $orderid;
		var $emsid;
		var $orderstatus;
		var $signature;
		var $merchant_key;
		var $signature_type;
		var $signature_verification_string;
		var $signature_verification_result;
		
		function eMSStatus($merchantid,$orderid,$emsid,$orderstatus,$signature,$key,$signature_type="SIMPLE")
		{
			$this->merchantid = $merchantid;
			$this->orderid = $orderid;
			$this->emsid = $emsid;
			$this->orderstatus = $orderstatus;
			$this->signature = $signature;
			$this->signature_type = $signature_type;
			$this->merchant_key = $key;
			$this->signature_verification_result = $signature_type=="SIMPLE"?$this->CheckNotificationSimple():$this->CheckNotificationPKI();
		}
		
		function CheckNotificationPKI()
		{
		   return false; 
		}
		
		function CheckNotificationSimple()
		{
			$this->signature_verification_string = sha1($this->merchantid.$this->orderid.$this->emsid.$this->orderstatus.$this->merchant_key);
			$result = ($this->signature_verification_string==$this->signature);
			return $result;
		}
		
		function VerificationResultToText()
		{
		    if (!$this->signature_verification_result)
			  $verification_result_text = "Not Veririfed (Verification String: ".$this->signature_verification_string.")";
			else
			  $verification_result_text = "OK";
			return $verification_result_text;
		}
		
		function ToXML()
		{
			
			return '<emsNotification><mercantid>'.$this->merchantid.'</mercantid><orderid>'.$this->orderid.'</orderid><emsid>'.$this->emsid.'</emsid><orderstatus>'.$this->orderstatus.'</orderstatus><signature signature_type="'.$this->signature_type.'">'.$this->signature.'</signature><signature_verification>'.$this->VerificationResultToText().'</signature_verification></emsNotification>';
		}
			
		function LogToFile()
		{
			 require_once('googlelog.php');
			 $log = new GoogleLog(
			 	plugin_dir_path( dirname( __FILE__ ) ) . 'libs/logs/demo_errorlog.txt',
			 	plugin_dir_path( dirname( __FILE__ ) ) . 'libs/logs/demo_messagelog.txt'
			 );
			 $log->logLevel = L_ALL;
			 $log->LogResponse("****NOTIFICATION**** ".$this->ToXML());
		}		
	}
  
?>