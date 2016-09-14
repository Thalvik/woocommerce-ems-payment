<?php

class eMSResponse {

	public function __construct($responsexml,$merchant_key,$verification_type="SIMPLE") {
		$xml = new SimpleXMLElement($responsexml);
		$this->response_id =  $xml["ID"];
		$this->response_signature = $xml['Signature'];
		$this->merchant_reference = $xml->Reference; 
		$this->redirect_url = $xml->URL; 
		$this->err_code = $xml->ErrCode;
		$this->err_message = $xml->ErrText;
		$this->response_verification_type = $verification_type;
		$this->merchant_key = $merchant_key;
	}


	private function CheckResponseSimple() {
	  $verification_string = $this->response_id.$this->merchant_reference.$this->redirect_url.$this->err_code.$this->merchant_key;
	  $verification_signature = sha1($verification_string);
	  return ($verification_signature==$this->response_signture);
	}
	
	private function CheckResponsePKI() {
		return false;
	}
	
	public function IsValid() {
		$signture_ok = $this->response_verification_type == "SIMPLE" ? $this->CheckResponseSimple() : $this->CheckResponsePKI;
		return ($signture_ok and ($this->err_code=="0"));
	}


	public function getResponseID() {
		return (string)$this->response_id;
	}

	public function getResponseSignature() {
		return (string)$this->response_signature;
	}

	public function getMerchantReference() {
		return (string)$this->merchant_reference;
	}

	public function getRedirectURL() {
		return (string)$this->redirect_url;
	}

	public function getErrorCode() {
		return (int)$this->err_code;
	}

	public function getErrorMessage() {
		return (string)$this->err_message;
	}

}