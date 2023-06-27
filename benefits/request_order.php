<?php
	// Turn off error reporting
	error_reporting(0);

	require('benefit_config.php');
	
	require('iPayBenefitPipe.php');	
	
	$myObj = new iPayBenefitPipe();
	
	// Do NOT change the values of the following parameters at all.
	$myObj->setAction(BACTION);
	$myObj->setCurrency(BCURRENCY);
	$myObj->setLanguage(BLANGUAGE);
	$myObj->setType(BTYPE);
	
	// modify the following to reflect your "Alias Name", "resource.cgn" file path, "keystore.pooh" file path.
	$myObj->setAlias(ALIAS);
	$myObj->setResourcePath(RESOURCEPATH); //only the path that contains the file; do not write the file name
	$myObj->setKeystorePath(RESOURCEPATH); //only the path that contains the file; do not write the file name
	
	// set a unique track ID for each transaction so you can use it later to match transaction response and identify transactions in your system and “BENEFIT Payment Gateway” portal.
	$trackId = base64_decode(strtr($_GET['merchantOrderId'], '+/=', '-_,'));
	$myObj->setTrackId($trackId); //MERCHANT_ORDER_ID
	
	// modify the following to reflect your pages URLs
	if(!empty($_GET['mobile']) && ($_GET['mobile'] == 1))
	{
		$myObj->setResponseURL(BASEURL . "/benefits/response_order.php?isMobile=1");
		$myObj->setErrorURL(BASEURL . "/benefits/err_order.php?isMobile=1&trackid=".$trackId);
	}
	else
	{
		$myObj->setResponseURL(BASEURL . "/benefits/response_order.php");
		$myObj->setErrorURL(BASEURL . "/benefits/err_order.php?trackid=".$trackId);
	}	
	
	// set transaction amount
	$grandTotal = base64_decode(strtr($_GET['amount'], '+/=', '-_,'));	
	$myObj->setAmt($grandTotal); // TOTAL OF ORDER TABLE
	
	// The following user-defined fields (UDF1, UDF2, UDF3, UDF4, UDF5) are optional fields.
	// However, we recommend setting theses optional fields with invoice/product/customer identification information as they will be reflected in “BENEFIT Payment Gateway” portal where you will be able to link transactions to respective customers. This is helpful for dispute cases. 
	$myObj->setUdf2("");
	$myObj->setUdf2("");
	$myObj->setUdf3("");
	$myObj->setUdf4("");
	$myObj->setUdf5("");
	
	if(trim($myObj->performPaymentInitializationHTTP())!=0)
	{
		echo("ERROR OCCURED! SEE CONSOLE FOR MORE DETAILS");
		return;
	}
	else
	{			
		$url=$myObj->getwebAddress();
		if(!empty($_GET['mobile']) && ($_GET['mobile'] == 1))
		{
			header("Location: ".$url); /* Redirect browser */

			/* Make sure that code below does not get executed when we redirect. */
			exit;
		}
		else
		{
			$arr = array(['url' => $url]);
			echo json_encode($arr);
			exit;		
		}
	}
?>