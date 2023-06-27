<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderInvoices extends Model
{
    protected $table = "order_invoices";
    protected $fillable = ['invoice_id','order_id','invoice_status','created_by'];
}
