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
        
        //======================================================================
        // Adding a query to the list
        private function AddRequest($name, $action, $params = array()) {
		// Normalize the input data
		if (!is_array($params)) {
			$params = array();
		}
		$params['action'] = $action;
		$this->prepared_requests[$name] = $params;
		return TRUE;
        }
        //======================================================================
		
        //======================================================================
        // Request a transaction
        public function AddRequestGetTransactions($name, $click_id = '', $date_from = '', $date_to = '', $date_type = '', $order_status = '', $additional_fields = '',$page = 1, $per_page = 300){
		// Add the request to the list
		$this->AddRequest(
				$name,
				'get_transactions',
				array(
					'click_id' => $click_id,
					'date_from' => $date_from,
					'date_to' => $date_to,
					'date_type' => $date_type,
					'order_status' => $order_status,
					'additional_fields' => $additional_fields,
					'page' => $page,
					'per_page' => $per_page
				)
			);
		return TRUE;
        }
        //======================================================================
        
        //======================================================================
        // Link Verification Request
        public function AddRequestCheckLink($name, $link) {
		// Add the request to the list
		$this->AddRequest(
				$name,
				'check_link',
				array(
					'link' => $link,
				)
			);
		return TRUE;
        }
        //======================================================================
		
        //======================================================================
        // Request for creatives
        public function AddRequestGetCreatives($name,$page = 1,$per_page = 50) {
		// Add the request to the list
		$this->AddRequest(
				$name,
				'get_creatives',
				array(
					'page' => $page,
					'per_page' => $per_page
				)
			);
		return TRUE;
        }
        //======================================================================
		
        //======================================================================
        // Querying in statistics with grouping by days
        public function AddRequestGetStatisticsByDay($name, $creative_id = 0, $date_from = '', $date_to = '',$page = 1, $per_page = 20) {
		// Add the request to the list
		$this->AddRequest(
				$name,
				'get_statistics_by_day',
				array(
					'creative_id' => $creative_id,
					'date_from' => $date_from,
					'date_to' => $date_to,
					'page' => $page,
					'per_page' => $per_page
				)
			);
		return TRUE;
        }
        //======================================================================
		
        //======================================================================
        // Query statistics grouped by the hour
        public function AddRequestGetStatisticsByHour($name, $creative_id = 0, $date_from = '', $date_to = '',$page = 1, $per_page = 20) {
		// Add the request to the list
		$this->AddRequest(
				$name,
				'get_statistics_by_hour',
				array(
					'creative_id' => $creative_id,
					'date_from' => $date_from,
					'date_to' => $date_to,
					'page' => $page,
					'per_page' => $per_page
				)
			);
		return TRUE;
        }
        //======================================================================
		
        //======================================================================
        // Query statistics grouped by creative
        public function AddRequestGetStatisticsByCreative($name, $creative_id = 0, $date_from = '', $date_to = '',$page = 1, $per_page = 20) {
		// Add the request to the list
		$this->AddRequest(
				$name,
				'get_statistics_by_creative',
				array(
					'creative_id' => $creative_id,
					'date_from' => $date_from,
					'date_to' => $date_to,
					'page' => $page,
					'per_page' => $per_page
				)
			);
		return TRUE;
        }
        //======================================================================
		
        //======================================================================
        // Querying in statistics with grouping by sub
        public function AddRequestGetStatisticsBySub($name, $creative_id, $date_from = '', $date_to = '',$page = 1, $per_page = 20) {
		// Add the request to the list
		$this->AddRequest(
				$name,
				'get_statistics_by_sub',
				array(
					'creative_id' => $creative_id,
					'date_from' => $date_from,
					'date_to' => $date_to,
					'page' => $page,
					'per_page' => $per_page
				)
			);
		return TRUE;
        }
        //======================================================================
        
        //======================================================================
        // Performance of all queries
        public function RunRequests() {
		// Reset the variables
		$this->request_results = array();
		$this->last_error = '';
        
		// Structure for sending the request
		$data = array(
			'user_api_key' => $this->user_api_key,
			'api_version' => self::EPN_CLIENT_API_VERSION,
			'requests' => $this->prepared_requests,
		);
		// Query string
		$post_data = json_encode($data);
		// Signature request
		$data_sign = md5($this->user_private_key . $post_data);
		// Initialize cURL
		$ch = curl_init();
		// Executing the query
		curl_setopt($ch, CURLOPT_URL,            self::EPN_API_URL);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST,           1);
		curl_setopt($ch, CURLOPT_POSTFIELDS,     $post_data); 
		curl_setopt($ch, CURLOPT_HTTPHEADER,     array(
				"Content-Type: text/plain",
				"X-EPN-Digest-Sign: $data_sign",
			));
		$result = curl_exec($ch);
		$curl_error_msg = curl_error($ch);
		//print "<!-- $curl_error_msg\n\n$result -->\n";
		// If the http request is processed with an error
		if ($curl_error_msg != '') {
			$this->last_error = $curl_error_msg;
		}
		else {
			// Parsim data
			$result_data = json_decode($result, TRUE);
			$this->last_error = isset($result_data['error']) ? $result_data['error'] : '';
			$this->request_results = isset($result_data['results']) && is_array($result_data['results']) ? $result_data['results'] : array();
		}
		// Regardless of the result, we drop the list of requests
		$this->prepared_requests = array();
		// If there were no mistakes, then everything is fine
		return $this->last_error == '' ? TRUE : FALSE;
		
	}
	//======================================================================

		//======================================================================
		// Receiving a response
		public function GetRequestResult($name) {
			return isset($this->request_results[$name]) ? $this->request_results[$name] : FALSE;
		}
			//======================================================================

		//======================================================================
		// Information about the last error
		public function LastError() {
			return $this->last_error;
		}
		//======================================================================
}