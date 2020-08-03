<?php
namespace App\Classes;
use PDO;
class SmsSender
{
    function send($phone, $message)
    {
      
		if( isset($phone) && !empty($phone))
		{ 
			$mobile=$phone;
		}
		else 
		{
			$mobile="null";
		}
		if($mobile !== 'null')
		{
		$source = "Epay";
		$parts = 1;
		
		$cyrilic = preg_match('/[А-Яа-яЁё]/u', $message);
		
		$length = mb_strlen($message, 'UTF-8');
		
    	if ($cyrilic)
		{
			if ($length > 70) 
			{
					$result = ($length / 67);
					$whole = floor($result);      
					$fraction = $result - $whole;
					
					if ($fraction > 0)
					{
						$parts = $whole + 1;
					}
					else
					{
						$parts = $whole;
					}
			}
		}
		else 
		{
			if ($length > 160) 
			{
					$result = ($length / 153);
					$whole = floor($result);      
					$fraction = $result - $whole;
					
					if ($fraction > 0)
					{
						$parts = $whole + 1;
					}
					else
					{
						$parts = $whole;
					}
			}
		}
			include_once("SmppClass.php");
			$smpphost = "10.241.201.184";
			$smppport = 2775;
			$systemid = "dfghb@h";
			$password = "fghbju@"; 
			$system_type = "IBT";
			$from = "IBT"; 
			$text = $message;
			$mobile=$phone;
			$smpp = new SmppClass();
			$smpp->SetSender($from);
			$smpp->Start($smpphost, $smppport, $systemid, $password, $system_type);
			$res = $smpp->Send($mobile, $smpp->strToHTML($text), true); 
			if ($res)
			{ 
			$servername = "localhost";
			$username = "epayuser";
			$password = "Yagonchi314";
			$dbname = "smpp";
						
						$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
						$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
						$conn->exec("set names utf8");
			$stmt = $conn->prepare('insert into `sms` (mobile, text, source, parts, date) values (:mobile, :text, :source, :parts, now())');
			$stmt->bindParam(':mobile',$mobile);
			$stmt->bindParam(':text',$text);
			$stmt->bindParam(':source',$source);
			$stmt->bindParam(':parts',$parts);
			$stmt->execute();
			return true;
			} 
			else 
			{
				error_log(date('Y/m/d h:i:sa'). '  [LOG] PHONE: ' .$phone. "\r\n", 3, "logs/errors".date('Y-m-d').".log");	
				return false; 
			} 	
			$smpp->End();
		} 
    }
}
?>
