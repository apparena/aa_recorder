<?php

/**
 * Helper functions for iConsultants Apps
 * @author Sebastian Buckpesch (s.buckpesch@iconsultants.eu)
 * @version 0.13
 * @copyright  Copyright (c) 2011 iConsultants UG (http://www.iconsultants.eu)
 * @license    http://www.iconsultants.eu/license/     OSL
 *
 */
class iCon_Helper {

	private $aa_app_id;
	private $aa_inst_id;

	function __construct($aa_inst_id,$aa_app_id = 0){
		if ($aa_app_id == 0) {
			// Load config
			try {
				require_once dirname(__FILE__).'/../../config.php';
			} catch (Exception $e) {
				error_log($e);
			}						
		}


		$this->aa_inst_id=$aa_inst_id;
		$this->aa_app_id=$aa_app_id;
	}

	/**
	 * Creates a link to a file which should be shown in the Fanpage tab
	 * @param String $file Filename which should be loaded to the Fanpage-Tab
	 * @param Array $params Array of additional parameters which should be concated to the variable
	 * @return String Link to show the commited file in the Fanpage Tab
	 */
	function getLink($file, $params = null, $shortenLink = false) {
		$session = new Zend_Session_Namespace('aa_session_' . $this->aa_inst_id);
		$concat_params = "";

		if (is_array($params)) {
			foreach ($params as $param) {
				$concat_params .= $param . ";";
			};
		}

		$encodedLink = base64_encode(urlencode($file . ";" . $concat_params));
		// Check format of facebook page url
		if (substr($session->fb_page['app_url'],0,24) != "http://www.facebook.com/" && substr($session->fb_page['app_url'],0,25) != "https://www.facebook.com/")
			$url = "http://www.facebook.com/" . $session->fb_page['app_url'];
		else
			$url = $session->fb_page['app_url'];
		$link = $url . "&app_data=" . $encodedLink;

		if ($shortenLink) {
			$link = chop($this->make_bitly_url($link));
		}

		return $link;
	}

	/**
	 * Returns the splitted parameters concated via URL
	 * @param String $encodedAppData urlencoded and base64_encoded parameter concated by ";"
	 * @return Array Array with decoded parameters
	 */
	function getAppDataParams($encodedAppData) {
		$js_result = explode("%253B", base64_decode(urldecode($encodedAppData)));
		if (count($js_result) > 1)
			return $js_result;
		else return explode("%3B", base64_decode(urldecode($encodedAppData)));
	}


	/**
	 * Enter description here ...
	 * @param unknown_type $url
	 * @param unknown_type $login
	 * @param unknown_type $appkey
	 * @param unknown_type $format
	 * @param unknown_type $history
	 * @return string
	 */
	function make_bitly_url($url,$login = "iconsultants", 
		$appkey = "R_02100f13b7fe5bf53a06d04bbce6a3cf",
		$format = 'txt', $history = 1) {

		//create the URL
		$bitly = 'http://api.bit.ly/v3/shorten?login='.$login.'&apiKey='.$appkey.'&uri='.urlencode($url).
			'&format='.$format.'&history='.$history;

		//get the url
		//could also use cURL here
		$response = file_get_contents($bitly);

		//parse depending on desired format
		if(strtolower($format) == 'json')
		{
			$json = @json_decode($response,true);
			return $json['data']['url'];
		}
		elseif(strtolower($format) == 'xml') //xml
		{
			$xml = simplexml_load_string($response);
			return $xml->data->url;
		}
		elseif(strtolower($format) == 'txt') //text
		{
			return $response;
		}
	}

	/**
	 * Returns the IP of the client 
	 * @return String client ip
	 */
	public function getClientIp(){
		// Get client ip address
		if ( isset($_SERVER["REMOTE_ADDR"]))
		    $client_ip = $_SERVER["REMOTE_ADDR"];
		else if ( isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
		    $client_ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
		else if ( isset($_SERVER["HTTP_CLIENT_IP"]))
		    $client_ip = $_SERVER["HTTP_CLIENT_IP"];

		return $client_ip;
	}

	/**
	 * Removes all \r and \n from a string
	 * @param String $str input string with special characters
	 * @return String output string without special characters
	 */
	public function removeSpecialChar($str) {
		$str=str_replace("\n","",$str);
		$str=str_replace("\r","",$str);
  		return $str; 
	}

	/**
	 * Generates an Email mailto-Link with subject and body
	 * @param String $to Email-Address of the mailto-link
	 * @param String $subject Subject of the email
	 * @param String $body Body text of the email
	 * @return string mailto-Link
	 */
	public function getEmailLink ($to='', $subject='', $body='') {
		$link = 'mailto:' . rawurlencode($to);
		$params = array();
		$remove = array('&', '=', '?', '"');
		if (!empty($subject)) $params[] = 'subject=' . rawurlencode(str_replace($remove, '', $subject));
		if (!empty($body)) {
			$body = str_replace(array("\r\n", "\n", '<br />'), array("\n", '', '%0A'), nl2br($body));
			$params[] = 'body=' . rawurlencode(str_replace($remove, '', $body));
		}
		if (!empty($params)) $link .= '?' . implode('&', $params);
		$link = base64_encode($link);
		echo "<h3>$link</h3>";
		return $link;
	}
}
?>