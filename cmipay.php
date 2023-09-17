<?php
require_once "./vendor/autoload.php";



//  CMI Settings	
$cmi_url = 'test.cmi.ccom'; // CIM PAYMENT URL
$cmi_id = '6686655'; // CMI MERCHANT ID
$cmi_key = '6464ddsqdqsdsqdqs'; //CMI KEY
$site_url = 'https://google.com'; //your siteweb


	  
$request = array("clientid"=>$cmi_id,
"amount"=>$price,
"okUrl"=>$site_url."/thankyoupage?paid",  // success callback page
"failUrl"=>$site_url."/thankyoupage?error",  //error callback page
"shopurl"=>$site_url,
"TranType"=>"PreAuth",
"rnd"=>microtime(),
"callbackUrl"=>$site_url."/payments/cmi-callback.php", // callback php page for check
"currency"=>"840", // USD OR MAD --> 840 USD currency
"storetype"=>"3D_PAY_HOSTING",
"hashAlgorithm"=>"ver3",
"lang"=>"en",  // Payment page language
"description"=>'Product description', // product description
"refreshtime"=>"5",
"BillToName"=>'User name', // Client name
"BillToCompany"=>'company', // company (Optional)
"BillToStreet1"=>'Adress',  // Adress
"BillToCountry"=>'MA',  // Morocco 
"tel"=>'0500000000',  // Client phone 
"email"=>'client@gmail.com',  // Client email
"encoding"=>"UTF-8",
"oid"=>'OR-46646';  // Order id
 
$storeKey = $cmi_key;

echo '
<meta http-equiv="Content-Language" content="en">
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-9">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="now">
<body onload="javascript:moveWindow()">
<form name="pay_form" method="post" action="'.$cmi_url.'">';			
			$postParams = array();
			foreach ($request as $key => $value){
				array_push($postParams, $key);
				echo "<input type=\"hidden\" name=\"" .$key ."\" value=\"" .trim($value)."\" /><br />";
			}		
			natcasesort($postParams);		
			$hashval = "";					
			foreach ($postParams as $param){				
				$paramValue = trim($request[$param]);
				$escapedParamValue = str_replace("|", "\\|", str_replace("\\", "\\\\", $paramValue));	
				$lowerParam = strtolower($param);
				if($lowerParam != "hash" && $lowerParam != "encoding" )	{
					$hashval = $hashval . $escapedParamValue . "|";
				}
			}
			$escapedStoreKey = str_replace("|", "\\|", str_replace("\\", "\\\\", $storeKey));	
			$hashval = $hashval . $escapedStoreKey;
			$calculatedHashValue = hash('sha512', $hashval);  
			$hash = base64_encode (pack('H*',$calculatedHashValue));	
			echo "<input type=\"hidden\" name=\"HASH\" value=\"" .$hash."\" /><br />";

echo '
<noscript><input type="submit" value="Click here if not redirected."/></noscript>
</form>
<script type="text/javascript" language="javascript">
function moveWindow() {document.pay_form.submit();}</script>
</body>
';
?>
