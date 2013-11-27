<?php
namespace FFI\TA;

require_once(dirname(dirname(__FILE__)) . "/exceptions/Network_Connection_Error.php");

class Proxy {
	public $contentType = "";
	private $options = array();
	public $POST = false;
	public $POSTData = "";
	private $URL;
	private $values = array();
	
	public function __construct($URL) {
		$this->URL = $URL;
	}
	
	public function addCURLOption($option, $value) {
		array_push($this->options, $option);
		array_push($this->values, $value);
	}
	
	public function fetch() {
	//Open a cURL session for making the call
		$curl = curl_init($this->URL);

		curl_setopt($curl, CURLOPT_HEADER, false);
		
	//Set the sending MIME type
		if ($this->contentType != "") {
			curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: " . $this->contentType));
		}
		
	//Set the POST data
		if ($this->POST) {
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $POSTData);
		}
		
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		
	//Add any additional user-defined options
		if (count($this->options)) {
			for ($i = 0; $i < count($this->options); ++$i) {
				curl_setopt($curl, $this->options[$i], $this->values[$i]);
			}
		}

		$response = curl_exec($curl);
		$errorNumber = curl_errno($curl);
		$error = curl_error($curl);
		curl_close($curl);
		
	//Check for any network errors
		if ($errorNumber) {
			throw new Network_Connection_Error("A network connection to Mandrill has failed. cURL error details: " . $error);
		}
		
		return $response;
	}
}
?>