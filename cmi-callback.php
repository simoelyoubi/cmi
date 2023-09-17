<?php
error_reporting(0);
ini_set('display_errors', 0);

include('../config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

$cmikey = "SELECT cmi_key FROM configuration WHERE id = '1' LIMIT 1";
$storeKey = $conn->query($cmikey)->fetch_assoc();
		
if($_POST["ProcReturnCode"] == "00") {

	$orderid =  $conn->escape_string($_POST["oid"]);
	$orderid = $conn->escape_string($orderid);
	$payment_id = $_POST["TransId"];
	$payment_id = $conn->escape_string($payment_id);
	
	// Check Order
	$getorders = "SELECT * FROM admin_payment WHERE id = '$orderid' LIMIT 1";
	$getorders = $conn->query($getorders);
	if ($getorders->num_rows > 0) {
		while($order = $getorders->fetch_array()) {
$pid = $order['id'];
$userid = $order['user_id'];
$deposit = $order['deposit'];
$now = date("Y-m-d H:i:s");

$setcredit = "UPDATE balance SET deposit = deposit +$deposit WHERE user_id = $userid";

$setpayment = "UPDATE admin_payment SET transaction_id ='$payment_id', status = '1', tdate = '$now' WHERE id='$pid'";


if ($conn->query($setcredit) === TRUE && $conn->query($setpayment) === TRUE) {
	
	//add affliate commision
$getaffid = $conn->query("SELECT shop_affiliate FROM shop WHERE (id = '$userid')")->fetch_array();
$affid = $getaffid[0];
if ($affid >1) {
$getpercentage = $conn->query("SELECT aff_percent FROM configuration WHERE (id = '1')")->fetch_array();
$percentage = $getpercentage[0];
$affcom = ($percentage/100)*$deposit;
$debit = "INSERT INTO affiliate_history (user_id, aff_id, amount, status) VALUES ('$affid', 'userid', '$affcom', '0')";
$conn->query($debit);
echo "ACTION=POSTAUTH";
} else {
	echo "ACTION=POSTAUTH";
}
}

}
} else {
	error_log("CMI Payment Error (Order ID : #".$_POST["oid"].") : Order not found. \n");		
	echo "FAILTURE"; 
}

} else {
	$error = $_POST["ErrMsg"];
	error_log("CMI Payment Error (Order ID : #".$_POST["oid"].") : ".$error."\n");		
	echo "FAILTURE";
}
	
} else { 
	header("Location: ../unauthorized.php");

}


