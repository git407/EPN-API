<?php

// Class to work with Cabinet EPN
class clEPNCabinetAccess {
	const EPN_API_URL = 'http://api.epn.bz/cabinet';
	const EPN_CLIENT_API_VERSION = 1;
	
	// Options
	private $user_api_key = '';
	private $user_private_key = '';
	private $prepared_requests = array();
	private $request_results = array();
	private $last_error = '';
	//======================================================================
	// Constructor
	public function __construct($user_api_key,$user_private_key) {
		$this->user_api_key = $user_api_key;
		$this->user_private_key = $user_private_key;
        }
        //======================================================================
        
 
}