<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
   protected $table = 'penjualan';    
   protected $primaryKey = 'id_penjualan';  
   // public $incrementing = false; 
   protected $fillable = [
   		'id_penjualan',
   		'id_obat',
   		'tgl_penjualan',
   		'qty',
   		'id_user'
   ];
}