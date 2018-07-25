<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Obat extends Model
{
   protected $table = 'obat';    
   protected $primaryKey = 'id_obat';  
   // public $incrementing = false; 
   protected $fillable = [
   		'id_obat',
   		'nama_obat'
   ];
}