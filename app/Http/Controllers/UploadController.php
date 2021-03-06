<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Obat;
use App\Models\Penjualan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Excel;
use Auth;
use Session;

class UploadController extends Controller
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
        return view('upload',$data);
    }

    public function upload(Request $request)
    {
        $nama = time() .'.'.$request->file('excel')->getClientOriginalExtension();
        $file = $request->file('excel')->move('fileupload/',$nama);
        $file = public_path('fileupload/'.$nama);
        $penjualan = Excel::load($file)->all()->toArray();

        foreach ($penjualan as $p) {
            if($p['tanggal']!=NULL){
                $cek = Penjualan::where('id_obat',$request->input('obat'))->where('tgl_penjualan',$p['tanggal'])->first(); 

                if(!empty($cek)){
                    Penjualan::where('id_obat',$request->input('obat'))
                            ->where('tgl_penjualan',$p['tanggal'])
                            ->update(['qty' => $p['total']],
                                    ['id_user' => 1]);
                }
                else{
                    Penjualan::create([
                        'id_obat'       => $request->input('obat'),
                        'tgl_penjualan' => $p['tanggal'],
                        'qty'           => $p['total'],
                        'id_user'       => 1
                    ]);
                }   
            } 
        }
        Session::put('alert-success', 'File Berhasil di Upload');
        return Redirect::back();
    }
}
