<?php
session_start();
if(isset($_POST)){
  $_SESSION['cartmaindata']['product_id']=$_POST['product_id'];
  $_SESSION['cartmaindata']['option_id']=$_POST['option_id'];
  $_SESSION['cartmaindata']['shoppingmsg']=$_POST['shoppingmsg'];
  $_SESSION['cartmaindata']['formsg']=$_POST['formsg'];
  $_SESSION['cartmaindata']['ladyoperator']=$_POST['ladyoperator'];
  $_SESSION['cartmaindata']['printstaffmsg']=$_POST['printstaffmsg'];
  $_SESSION['cartmaindata']['cartMasterId']=$_POST['cartMasterId'];
  $_SESSION['cartmaindata']['customerId']=$_POST['customerId'];
  $_SESSION['cartmaindata']['backurl']=$_POST['backurl'];
  $_SESSION['cartmaindata']['caption']=$_POST['caption'];
}
?>
