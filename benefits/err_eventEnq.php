<?php
	error_reporting(0);

	require('benefit_config.php');
	
	require('iPayBenefitPipe.php');

	$baseUrl  = BASEURL;
	
	$myObj =new iPayBenefitPipe(); 
	
	// modify the following to reflect your "Alias Name", "resource.cgn" file path, "keystore.pooh" file path.
	$myObj->setAlias(ALIAS);
	$myObj->setResourcePath(RESOURCEPATH); //only the path that contains the file; do not write the file name
	$myObj->setKeystorePath(RESOURCEPATH); //only the path that contains the file; do not write the file name
	
	$trandata = "";
	$paymentID = "";
	$result = "";
	$responseCode = "";
	$response = "";
	$transactionID = "";
	$referenceID = "";
	$trackID = "";
	$amount = "";
	$UDF1 = "";
	$UDF2 = "";
	$UDF3 = "";
	$UDF4 = "";
	$UDF5 = "";
	$authCode = "";
	$postDate = "";
	$errorCode = "";
	$errorText = "";
	
	$trandata = isset($_REQUEST["trandata"]) ? $_REQUEST["trandata"] : "";
	
	if ($trandata != "")
	{
		$returnValue = $myObj->parseEncryptedRequest($trandata);
		if ($returnValue == 0)
		{
			$paymentID = $myObj->getPaymentId();
			$result = $myObj->getRef();
			$responseCode = $myObj->getAuthRespCode();
			$transactionID = $myObj->getTransId();
			$referenceID = $myObj->getRef();
			$trackID = $myObj->getTrackId();
			$amount = $myObj->getAmt();
			$UDF1 = $myObj->getUdf1();
			$UDF2 = $myObj->getUdf2();
			$UDF3 = $myObj->getUdf3();
			$UDF4 = $myObj->getUdf4();
			$UDF5 = $myObj->getUdf5();
			$authCode = $myObj->getAuth();
			$postDate = $myObj->getDate();
			$errorCode = $myObj->getError();
			$errorText = $myObj->getError_text();

		}
		else
		{
			$errorText = $myObj->getError_text();
		}
	}
	else if (isset($_REQUEST["ErrorText"]))
    {
        $paymentID = $_REQUEST["paymentid"];
        $trackID = $_REQUEST["trackid"];
        $amount = $_REQUEST["amt"];
        $UDF1 = $_REQUEST["udf1"];
        $UDF2 = $_REQUEST["udf2"];
        $UDF3 = $_REQUEST["udf3"];
        $UDF4 = $_REQUEST["udf4"];
        $UDF5 = $_REQUEST["udf5"];
        $errorText = $_REQUEST["ErrorText"];
    }
    else
    {
		$trackID = $_REQUEST["trackid"];
        $errorText = "Cancelled by user";
    }
	

	// Remove any HTML/CSS/javascrip from the page. Also, you MUST NOT write anything on the page EXCEPT the word "REDIRECT=" (in upper-case only) followed by a URL.
	// If anything else is written on the page then you will not be able to complete the process.
	if ($myObj->getResult() == "CAPTURED")
	{
		$successUrl = EVENTENQ_SUCCESS_URL . "?track_id=".$trackID."&payment_id=".$paymentID;

		header("Location: $successUrl"); /* Redirect browser */

		/* Make sure that code below does not get executed when we redirect. */
		exit;
		
	}
	else if ($myObj->getResult() == "NOT CAPTURED" || $myObj->getResult() == "CANCELED" || $myObj->getResult() == "DENIED BY RISK" || $myObj->getResult() == "HOST TIMEOUT")
	{
		if ($myObj->getResult() == "NOT CAPTURED")
		{
			switch ($myObj->getAuthRespCode())
			{
				case "05":
					$response = "Please contact issuer";
					break;
				case "14":
					$response = "Invalid card number";
					break;
				case "33":
					$response = "Expired card";
					break;
				case "36":
					$response = "Restricted card";
					break;
				case "38":
					$response = "Allowable PIN tries exceeded";
					break;
				case "51":
					$response = "Insufficient funds";
					break;
				case "54":
					$response = "Expired card";
					break;
				case "55":
					$response = "Incorrect PIN";
					break;
				case "61":
					$response = "Exceeds withdrawal amount limit";
					break;
				case "62":
					$response = "Restricted Card";
					break;
				case "65":
					$response = "Exceeds withdrawal frequency limit";
					break;
				case "75":
					$response = "Allowable number PIN tries exceeded";
					break;
				case "76":
					$response = "Ineligible account";
					break;
				case "78":
					$response = "Refer to Issuer";
					break;
				case "91":
					$response = "Issuer is inoperative";
					break;
				default:
					// for unlisted values, please generate a proper user-friendly message
					$response = "Unable to process transaction temporarily. Try again later or try using another card.";
					break;				
			}

			$cancelUrl = EVENTENQ_CANCEL_URL . "?track_id=".$trackID."&error=".$response;

			header("Location: $cancelUrl"); /* Redirect browser */

			/* Make sure that code below does not get executed when we redirect. */
			exit;
		}
		else if ($myObj->getResult() == "CANCELED")
		{
			$response = "Transaction was canceled by user.";

			$cancelUrl = EVENTENQ_CANCEL_URL . "?track_id=".$trackID."&error=".$response;

			header("Location: $cancelUrl"); /* Redirect browser */

			/* Make sure that code below does not get executed when we redirect. */
			exit;
		}
		else if ($myObj->getResult() == "DENIED BY RISK")
		{
			$response = "Maximum number of transactions has exceeded the daily limit.";

			$cancelUrl = EVENTENQ_CANCEL_URL . "?track_id=".$trackID."&error=".$response;

			header("Location: $cancelUrl"); /* Redirect browser */

			/* Make sure that code below does not get executed when we redirect. */
			exit;
		}
		else if ($myObj->getResult() == "HOST TIMEOUT")
		{
			$response = "Unable to process transaction temporarily. Try again later.";

			$cancelUrl = EVENTENQ_CANCEL_URL . "?track_id=".$trackID."&error=".$response;

			header("Location: $cancelUrl"); /* Redirect browser */

			/* Make sure that code below does not get executed when we redirect. */
			exit;
		}		
	}
	else
	{
		//Unable to process transaction temporarily. Try again later or try using another card.
		$cancelUrl = EVENTENQ_CANCEL_URL . "?track_id=".$trackID."&error=".$errorText;

		header("Location: $cancelUrl"); /* Redirect browser */

		/* Make sure that code below does not get executed when we redirect. */
		exit;
	}
	
?>