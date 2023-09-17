<?php
require_once "./vendor/autoload.php";

if ( $_SESSION['user_logged_in'] !== True || !isset($_SESSION['user_id'])) {
  header("location: ../login.php"); 
  exit();  
} else {
$suid = $conn->escape_string($_SESSION["user_id"]);
}

// Get CMI Settings	
$sql = "SELECT site_url, cmi_url, cmi_id, cmi_key FROM configuration WHERE (id = '1')";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
 while($row = $result->fetch_assoc()) { 
$cmi_url = $row['cmi_url'];
$cmi_id = $row['cmi_id'];
$cmi_key = $row['cmi_key'];
$site_url = $row['site_url'];
 }}

// Get User
$users = "SELECT shp_email, shp_name, shp_company, shp_adress, shp_phone, shp_country FROM shop WHERE user_id = '$suid' LIMIT 1";
$users = $conn->query($users);
if ($users->num_rows > 0) { 
	  while($user = $users->fetch_assoc()) {
         $codecountry = $user['shp_country'];
         $getcountry = $conn->query("SELECT ctry_name FROM country WHERE (ctry_sym = '$codecountry')")->fetch_array();
		 $username = $user['shp_name'];
		 $shp_company = $user['shp_company'];
		 $email = $user['shp_email'];
		 $address = $user['shp_adress'];
		 $phone = $user['shp_phone'];
		 $country = $getcountry[0];
	  }
}
	  
$request = array("clientid"=>$cmi_id,
"amount"=>$price,
"okUrl"=>$site_url."/credit?paid",
"failUrl"=>$site_url."/credit?error",
"shopurl"=>$site_url,
"TranType"=>"PreAuth",
"rnd"=>microtime(),
"callbackUrl"=>$site_url."/payments/cmi-callback.php",
"currency"=>"840",
"storetype"=>"3D_PAY_HOSTING",
"hashAlgorithm"=>"ver3",
"lang"=>"en",
"description"=>$name,
"refreshtime"=>"5",
"BillToName"=>$username,
"BillToCompany"=>$shp_company,
"BillToStreet1"=>$address,
"BillToCountry"=>$country,
"tel"=>$phone,
"email"=>$email,
"encoding"=>"UTF-8",
"oid"=>$orderid);
 
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
