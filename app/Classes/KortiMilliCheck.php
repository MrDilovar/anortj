<?php
namespace App\Classes;

class KortiMilliCheck
{

    protected $api_key = null;
    protected $user_name = null;
 
    public function __construct($api_key = null, $user_name = null) 
    {
        $this->api_key = (string) $api_key;
        $this->user_name = (string) $user_name;

    }
    
    public function check($tran_id) 
    {	
		
	$host = "https://epay.ibt.tj/merchant/get_status.php";
	// $proxy = '';
	// $proxyauth = ':';	
	//  $certificate = "cacert.pem";

	//$api_key = "";
	//$username = "";

    $sign = sha1( $this->api_key.$tran_id);

    $process = curl_init($host);
    
    $xmlData = '<?xml version="1.0" encoding="utf-8"?>
				<request>
					<username>'.$this->user_name.'</username>
					<sign>'.$sign.'</sign>
					<tran_id>'.$tran_id.'</tran_id>
				</request>';

	// curl_setopt($process, CURLOPT_CAINFO, $certificate);
	// curl_setopt($process, CURLOPT_CAPATH, $certificate);
	
	// curl_setopt($process, CURLOPT_PROXY, $proxy);
	// curl_setopt($process, CURLOPT_PROXYUSERPWD, $proxyauth);
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


	 /* header('Refresh: 1; url=https://epay.ibt.tj/index.php'); */
	curl_close($process);

	$xmlr = simplexml_load_string($body);
		
            //    echo $xmlr->result;
				return $xmlr;
			  
    }
}
