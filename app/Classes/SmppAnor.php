<?php
namespace App\Classes;

class SmppAnor
{

    public static function sendSMS($number, $msg) 
    {
        
		//include("Kortimilli_sign.php");
		$host = "http://smpptest.ibt.tj/Anor.php";
		// $proxy = '';
		// $proxyauth = ':';	
		//$certificate = "cacert.pem";
		//$username = "osonshop";
		$process = curl_init($host);
		//curl_setopt($process, CURLOPT_USERPWD, $username . ":" . $password);
		$xmlData = 'XML=<?xml version="1.0" encoding="utf-8"?>
					<request>
						<mobnumber>992'.$number.'</mobnumber>
						<textSms>'.$msg.'</textSms>
					</request>';
		//curl_setopt($process, CURLOPT_CAINFO, $certificate);
		//curl_setopt($process, CURLOPT_CAPATH, $certificate);
		// curl_setopt($process, CURLOPT_PROXY, $proxy);
		// curl_setopt($process, CURLOPT_PROXYUSERPWD, $proxyauth);
		// curl_setopt ($process, CURLOPT_SSLVERSION, 6);
		curl_setopt($process, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded', 'Connection: Keep-Alive','Accept: */*'));
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
	
		//$xmlr = simplexml_load_string($repl_body);
		
		return $body;

	}
	
}

























