<?php
require('autoload.php');
global $lumise, $lumise_helper;

$data = $lumise->connector->get_session('lumise_cart');
$items = isset($data['items']) ? $data['items'] : array();
foreach($items as $key => $value){
  //$cart_id=$key;
  $file=$value['file'];
}
$cart_data = $lumise->lib->get_cart_item_file($file);
$screenshots = array();
$print_files = array();
if (isset($cart_data['design']['stages'])) {
  $printfile_path = $lumise->cfg->upload_path.'printfiles';
  $screenshot_path = $lumise->cfg->upload_path.'screenshots';
  $isf = 0;
  $i=0;
  $stage=1;
  foreach ($cart_data['design']['stages'] as $s => $sdata) {
    if (isset($sdata['print_file'])) {
      $print_file_name = "Stage-".$stage."_".strtotime(date('Y-m-d')).'_printfiles_'.substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'),1,8).'.png';
      $print_name = $printfile_path . '/' . $print_file_name;
      if (strpos($sdata['print_file'], 'data:image') === false)
        continue;

      if (@file_put_contents(
          $print_name,
          base64_decode(
            preg_replace('#^data:image/\w+;base64,#i', '', $sdata['print_file'])
          )
        )
      ){
        array_push($print_files, $print_file_name);
      }
    }
    if (isset($sdata['screenshot'])) {
      $src_file_name = "Stage-".$stage."_".strtotime(date('Y-m-d')).'_screenshot_'.substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'),1,8).'.png';
      $src_name = $screenshot_path . '/' . $src_file_name;
      if (strpos($sdata['screenshot'], 'data:image') === false)
        continue;

      if (@file_put_contents(
          $src_name,
          base64_decode(
            preg_replace('#^data:image/\w+;base64,#i', '', $sdata['screenshot'])
          )
        )
      ){
        array_push($screenshots, $src_file_name);
      }

      /*if($i==0){
          //$firstimage=$lumise->cfg->upload_url.$image;
          $firstimage=$lumise->cfg->upload_url.$src_file_name;
      }
      else
        //$otherimages[]=$lumise->cfg->upload_url.$image;
        $otherimages[]=$lumise->cfg->upload_url.$src_file_name;
      $i++;*/
    }
    $isf++;

    $stage++;
  }
}
$i=0;
foreach($cart_data['screenshots'] as $image){
  if($i==0){
      $firstimage=$lumise->cfg->upload_url.$image;
  }
  else
  $otherimages[]=$lumise->cfg->upload_url.$image;
  $i++;
}
if(isset($_SESSION['cartmaindata']['isMobile']) && $_SESSION['cartmaindata']['isMobile']==1){
      $result['status'] = "OK";
      $result['statusCode'] = 200;
      $result['message'] = "Success";
      $result['quantity'] = "".$cart_data['qty']."";
      $result['image'] = $firstimage;
      if(!empty($otherimages))
      $result['other_image'] = json_encode($otherimages);
      else
      $result['other_image']="";
      if(!empty($screenshots))
      $result['screenshots'] = json_encode($screenshots);
      else
      $result['screenshots']="";
      if(!empty($print_files))
      $result['print_files'] = json_encode($print_files);
      else
      $result['print_files']="";
      unset($_SESSION['cartmaindata']['isMobile']);
      $lumise->connector->set_session('lumise_cart', array('items' => array()));
      header('Content-Type: application/json');
      echo json_encode($result);
      exit;
}
else{
  if(!empty($otherimages))
  $other_image = json_encode($otherimages);
  else
  $other_image="";
  if(!empty($screenshots))
  $screen_shots = json_encode($screenshots);
  else
  $screen_shots="";
  if(!empty($print_files))
  $printfiles = json_encode($print_files);
  else
  $printfiles="";
$customer_id=$_SESSION['cartmaindata']['customerId'];
if(!isset($_SESSION['cartmaindata']['customerId']) || empty($_SESSION['cartmaindata']['customerId']) || $_SESSION['cartmaindata']['customerId']=='null')
$customer_id=0;
if(!isset($_SESSION['cartmaindata']['cartMasterId']) || empty($_SESSION['cartmaindata']['cartMasterId']) || $_SESSION['cartmaindata']['cartMasterId']=='null'){
  $id = $lumise->lib->addtocartMaster($customer_id);
  $cart_master_id=$id;

  $dataToInsert=array('option_id'=>$_SESSION['cartmaindata']['option_id'],
                     'product_id'=>$_SESSION['cartmaindata']['product_id'],
                     'user_id'=>$customer_id,
                     'cart_master_id'=>$id,
                     'gift_message'=>$_SESSION['cartmaindata']['shoppingmsg'],
                     'gift_wrap'=>$_SESSION['cartmaindata']['formsg'],
                     'lady_operator'=>$_SESSION['cartmaindata']['ladyoperator'],
                     'message'=>$_SESSION['cartmaindata']['printstaffmsg'],
                     'photobook_caption'=>$_SESSION['cartmaindata']['caption'],
                     'quantity'=>$cart_data['qty'],
                     'price'=>$cart_data['price']['total'],
                     'image'=>$firstimage,
                     'other_images'=>$other_image,
                     'screenshots_files'=>$screen_shots,
                     'print_files'=>$printfiles,
                     'created_at'=>date('Y-m-d H:i:s'));
  $data=$lumise->lib->addtocart($dataToInsert);
}
else{
  $cart_master_id=$_SESSION['cartmaindata']['cartMasterId'];
  $dataToInsert=array('option_id'=>$_SESSION['cartmaindata']['option_id'],
                     'product_id'=>$_SESSION['cartmaindata']['product_id'],
                     'user_id'=>$customer_id,
                     'cart_master_id'=>$cart_master_id,
                     'gift_message'=>$_SESSION['cartmaindata']['shoppingmsg'],
                     'gift_wrap'=>$_SESSION['cartmaindata']['formsg'],
                     'lady_operator'=>$_SESSION['cartmaindata']['ladyoperator'],
                     'message'=>$_SESSION['cartmaindata']['printstaffmsg'],
                     'photobook_caption'=>$_SESSION['cartmaindata']['caption'],
                     'quantity'=>$cart_data['qty'],
                     'price'=>$cart_data['price']['total'],
                     'image'=>$firstimage,
                     'other_images'=>$other_image,
                     'screenshots_files'=>$screen_shots,
                     'print_files'=>$printfiles,
                     'created_at'=>date('Y-m-d H:i:s'));
  $data=$lumise->lib->addtocart($dataToInsert);

}
$cart_master_id=strtr(base64_encode($cart_master_id), '+/=', '-_,');
unset($_SESSION['cartmaindata']['cartMasterId']);
unset($_SESSION['cartmaindata']['customerId']);
unset($_SESSION['cartmaindata']['option_id']);
unset($_SESSION['cartmaindata']['product_id']);
unset($_SESSION['cartmaindata']['shoppingmsg']);
unset($_SESSION['cartmaindata']['formsg']);
unset($_SESSION['cartmaindata']['ladyoperator']);
unset($_SESSION['cartmaindata']['printstaffmsg']);
unset($_SESSION['cartmaindata']['caption']);
$lumise->connector->set_session('lumise_cart', array('items' => array()));
$lumise->redirect($lumise->connector->editor_url.'../shopping-cart/'.$cart_master_id);
}
?>
