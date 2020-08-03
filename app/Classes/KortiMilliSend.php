<?php
namespace App\Classes;

class KortiMilliSend
{

    protected $api_key = null;
    protected $user_name = null;
    protected $success_url = null;

    public function __construct($api_key = null, $user_name = null,$success_url=null) 
    {
        $this->api_key = (string) $api_key;
        $this->user_name = (string) $user_name;
        $this->success_url = (string) $success_url;
    }
    
    public function send($amount, $tran_id, $hidden_details) 
    {
        
		//include("Kortimilli_sign.php");
		$host = "https://epay.ibt.tj/merchant/init_session.php";
		// $proxy = '';
		// $proxyauth = ':';	
		//$certificate = "cacert.pem";
		//$username = "osonshop";
		//$success_url = "http://google.com?id=".$tran_id;
		$sign = sha1( $this->api_key.$amount.$tran_id);

		$process = curl_init($host);
		
	
		//curl_setopt($process, CURLOPT_USERPWD, $username . ":" . $password);

		$xmlData = '<?xml version="1.0" encoding="utf-8"?><request><amount>'.$amount.'</amount><username>'.$this->user_name.'</username><sign>'.$sign.'</sign><tran_id>'.$tran_id.'</tran_id><success_url>'.$this->success_url.'</success_url><hidden_details>'.$hidden_details.'</hidden_details></request>';

	   
		//curl_setopt($process, CURLOPT_CAINFO, $certificate);
		//curl_setopt($process, CURLOPT_CAPATH, $certificate);
		// curl_setopt($process, CURLOPT_PROXY, $proxy);
		// curl_setopt($process, CURLOPT_PROXYUSERPWD, $proxyauth);
		// curl_setopt ($process, CURLOPT_SSLVERSION, 6);
		curl_setopt($process, CURLOPT_HTTPHEADER, array('Content-Type: application/xml', 'Connection: Keep-Alive', 'Accept: application/xml'));
		curl_setopt($process, CURLOPT_HEADER, 1);
		curl_setopt($process, CURLOPT_TIMEOUT, 30);
        curl_setopt($process, CURLOPT_POST, 1);
        curl_setopt($process, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($process, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($process, CURLOPT_POSTFIELDS, $xmlData);
		curl_setopt($process, CURLOPT_RETURNTRANSFER,TRUE);

		$response = curl_exec($process);
		if($response === FALSE){
			die(curl_error($process));
		}

		$header_size = curl_getinfo($process, CURLINFO_HEADER_SIZE);
		$header = substr($response, 0, $header_size);
		$body = substr($response, $header_size);
        $repl_body = str_replace(array("\r\n", "\n", "\r"), "", $body);
		 /* header('Refresh: 1; url=https://epay.ibt.tj/index.php'); */
		curl_close($process);
	
		$xmlr = simplexml_load_string($repl_body);
		//$json_enc = json_code($xmlr);
	
		//$resp = new ApiResponse();
        //$resp->message = $xmlr->message;
        //$resp->npctranid = $xmlr->npctranid;
        //$resp->code = (int)$xmlr->code;
		
		if ($xmlr->result == 0)	
				{
					return $xmlr->url;
					//header ("Location: $xmlr->url");
				}
		else die ("Что то пошло не так");

	}
	
}

























