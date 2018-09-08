<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Khill\Lavacharts\Lavacharts;
use App\Models\Obat;
use App\Models\Penjualan;
use Datatables;
use DB;

class LaporanController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

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
        return view('tabel',$data);
    }
    public function datatable(Request $request, $id_obat, $tgl_dari, $tgl_sampai){
        $filter="";

        if(!empty($id_obat)){
            $filter = "AND P.ID_OBAT = $id_obat";
        }

        if(!empty($tgl_dari)){
            $tgl_dari = date_format(date_create($tgl_dari),"Y-m-d");
            $filter   = "$filter AND DATE(P.TGL_PENJUALAN) >= '$tgl_dari'";
        }

        if(!empty($tgl_sampai)){
            $tgl_sampai = date_format(date_create($tgl_sampai),"Y-m-d");
            $filter     = "$filter AND DATE(P.TGL_PENJUALAN) <= '$tgl_sampai'";
        }

        $sql = "SELECT P.ID_PENJUALAN, P.ID_OBAT, O.NAMA_OBAT, DATE_FORMAT(P.TGL_PENJUALAN, '%d %M %Y')AS TGL_PENJUALAN, P.QTY, U.USERNAME 
                    FROM PENJUALAN AS P 
                    JOIN OBAT AS O ON P.ID_OBAT = O.ID_OBAT
                    JOIN USERS AS U ON P.ID_USER = U.ID_USER
                    WHERE 1 $filter";
        $sql_qty = "SELECT SUM(P.QTY) AS TOTAL_QTY
                    FROM PENJUALAN AS P 
                    JOIN OBAT AS O ON P.ID_OBAT = O.ID_OBAT
                    JOIN USERS AS U ON P.ID_USER = U.ID_USER
                    WHERE 1 $filter";
        $query = DB::select($sql);
        $query_sum = DB::select($sql_qty)[0]->TOTAL_QTY;

        return Datatables::of($query)->with('totalQty',$query_sum)->make();
    }
}
