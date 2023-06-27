<?php

namespace App\Traits;

use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Response;

trait ExportTrait
{
    // public  function export($users, $sheetTitle){

    //     $now = date("Y-m-d H:i:s");
    //     $filename = "categories"."_".$now.".csv";
    //     $handle = fopen("categories.csv", 'w+');
    //     fputcsv($handle, array('Id', 'Parent', 'Title', 'SKU Prefix', 'Slug', 'Display in Top menu', 'Status'));
        
    //     foreach($users as $row) {
        
    //         $id = $row['Id'];
    //         $parentId = $row['Parent Id'];
    //         $title = $row['Title'];
    //         $sKUPrefix = $row['SKU Prefix'];
    //         $slug = $row['Slug'];
    //         $displayInTopMenu = $row['Display in Top menu'];
    //         $status = $row['Status'];
        
    //         fputcsv($handle, array($id, $parentId, $title, $sKUPrefix, $slug, $displayInTopMenu, $status));
    //     }

    //     fclose($handle);

    //     $headers = array(
    //         'Content-Type' => 'text/csv',
    //     );

    //     Response::download('categories.csv', $filename, $headers);
    //     return "categories.csv";
    // }

    public  function exportBrands($brands, $sheetTitle){

        $now = date("Y-m-d H:i:s");
        $filename = "brands"."_".$now.".csv";
        $handle = fopen("brands.csv", 'w+');
        fputcsv($handle, array('Id', 'Brand', 'Status'));
        
        foreach($brands as $row) {
        
            $id = $row['Id'];
            $brand = $row['Name'];
            $status = $row['Status'];
        
            fputcsv($handle, array($id, $brand, $status));
        }

        fclose($handle);

        $headers = array(
            'Content-Type' => 'text/csv',
        );

        Response::download('brands.csv', $filename, $headers);
        return "brands.csv";
    }

    public  function exportOrders($orders, $sheetTitle)
    {
        // dd(public_path());
        $now = date("Y-m-d_H-i-s");
        $filename = "orders"."_".$now.".csv";
        // $filename = "orders.csv";
        $handle = fopen(public_path().'/'.$filename, 'w+');
        fputcsv($handle, array('Order ID','Customer Group','Customer Name','Email','Billing Address','Shipping Method','Shipping Address','Grand Total','Status','Purchase Date'));
        
        foreach($orders as $row) {
        
            $orderId = $row['order_id'];
            $custGroup = $row['customer_group'];
            $custName = $row['first_name'].' '.$row['last_name'];
            $custEmail = $row['customerEmail'];
            $billingAddr = $row['s_address_line_1'].','.$row['b_address_line_2'].','.$row['b_city'].','.$row['b_state'].','.$row['b_pincode'].' '.$row['b_country'];
            $shippingMethod = $row['shipping_method'];
            $shippingAddr = $row['s_address_line_1'].','.$row['s_address_line_2'].','.$row['s_city'].','.$row['s_state'].','.$row['s_pincode'].' '.$row['s_country'];
        
            $grandTotal = $row['total'];
            $status = $row['status'];
            $purchaseDate = $row['orderCreateddate'];

            fputcsv($handle, array($orderId, $custGroup, $custName,$custEmail,$billingAddr,$shippingMethod,$shippingAddr,$grandTotal,$status,$purchaseDate));
        }

        fclose($handle);

        $headers = array(
            'Content-Type' => 'text/csv',
        );
        $path = $this->getBaseUrl().'/public/'.$filename;
        // dd($path);
        // return response()->download($path, $filename, $headers);
        // dd($filename);
        // Response::download($filename, $filename, $headers);
        
        return $path;
    }
    // public  function exportAttributeGroup($attributeGroups, $sheetTitle)
    // {
    //     // echo "in trait"; die;
    //     $now = date("Y-m-d H:i:s");
    //     $filename = "attributeGroups"."_".$now.".csv";
    //     $handle = fopen("attributeGroups.csv", 'w+');
    //     fputcsv($handle, array('Id', 'Name', 'Display Name','Sort Order'));
        
    //     foreach($attributeGroups as $row) {
        
    //         $id = $row['Id'];
    //         $name = $row['Name'];
    //         $display_name = $row['Display Name'];
    //         $sort_order = $row['Sort Order'];

    //         fputcsv($handle, array($id, $name, $display_name,$sort_order));
    //     }

    //     fclose($handle);

    //     $headers = array(
    //         'Content-Type' => 'text/csv',
    //     );

    //     Response::download('attributeGroups.csv', $filename, $headers);
    //     return "attributeGroups.csv";
    // }

    // public  function exportAttribute($attributes, $sheetTitle)
    // {
    //     $now = date("Y-m-d H:i:s");
    //     $filename = "attributes"."_".$now.".csv";
    //     $handle = fopen("attributes.csv", 'w+');
    //     fputcsv($handle, array('Id', 'Name', 'Internal Name','Sort Order','AttributeGroup Name','Attribute Type'));
        
    //     foreach($attributes as $row) {
        
    //         $id = $row['Id'];
    //         $name = $row['Name'];
    //         $display_name = $row['Internal Name'];
    //         $sort_order = $row['Sort Order'];
    //         $attrGroup_name = $row['AttributeGroup Name'];
    //         $attribute_type = $row['Attribute Type'];

    //         fputcsv($handle, array($id, $name, $display_name,$sort_order,$attrGroup_name,$attribute_type));
    //     }

    //     fclose($handle);

    //     $headers = array(
    //         'Content-Type' => 'text/csv',
    //     );

    //     Response::download('attributes.csv', $filename, $headers);
    //     return "attributes.csv";
    // }
    // public  function exportEvents($events, $sheetTitle)
    // {
    //     $now = date("Y-m-d H:i:s");
    //     $filename = "events"."_".$now.".csv";
    //     $handle = fopen("events.csv", 'w+');
    //     fputcsv($handle, array('Id', 'Event Name', 'Event Description'));
        
    //     foreach($events as $row) {
        
    //         $id = $row['Id'];
    //         $event_name = $row['Event Name'];
    //         $event_desc = $row['Event Description'];
        
    //         fputcsv($handle, array($id, $event_name, $event_desc));
    //     }

    //     fclose($handle);

    //     $headers = array(
    //         'Content-Type' => 'text/csv',
    //     );

    //     Response::download('events.csv', $filename, $headers);
    //     return "events.csv";
    // }

    // public  function exportPackages($packages, $sheetTitle)
    // {
    //     $now = date("Y-m-d H:i:s");
    //     $filename = "packages"."_".$now.".csv";
    //     $handle = fopen("packages.csv", 'w+');
    //     fputcsv($handle, array('Id', 'Event Name', 'Package Name','Price','Discounted Price','Other Details'));
        
    //     foreach($packages as $row) {
        
    //         $id = $row['Id'];
    //         $event_name = $row['Event Name'];
    //         $pkg_name = $row['Package Name'];
    //         $price = $row['Price'];
    //         $discounted_price = $row['Discounted Price'];
    //         $other_details = $row['Other Details'];
        
    //         fputcsv($handle, array($id, $event_name, $pkg_name,$price,$discounted_price,$other_details));
    //     }

    //     fclose($handle);

    //     $headers = array(
    //         'Content-Type' => 'text/csv',
    //     );

    //     Response::download('packages.csv', $filename, $headers);
    //     return "packages.csv";
    // }

}


