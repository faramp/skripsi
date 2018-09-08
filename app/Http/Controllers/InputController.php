<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Obat;
use App\Models\Penjualan;
use Carbon\Carbon;
use Auth;
use Session;
use Illuminate\Support\Facades\Redirect;

class InputController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [
            'obat' => Obat::all()
        ];
        return view('input',$data);
    }

    public function input(Request $request){
        $cek = Penjualan::where('id_obat',$request->input('obat'))->where('tgl_penjualan',Carbon::now('GMT+7')->toDateString())->first();
        if(!empty($cek)){
            $qty = $cek->qty + $request->input('qty');
            Penjualan::where('id_obat',$request->input('obat'))
                        -> where('tgl_penjualan',Carbon::now('GMT+7')->toDateString())
                        -> update(['qty' => $qty],
                                  ['id_user' => Auth::user()->id_user]);
        }
        else{
            Penjualan::create([
                'id_obat'       => $request->input('obat'),
                'tgl_penjualan' => NOW('GMT+7'),
                'qty'           => $request->input('qty'),
                'id_user'       => Auth::user()->id_user
            ]); 
        }
        Session::put('alert-success', 'Data Berhasil di Input');
        return Redirect::back();
    }
    
}
