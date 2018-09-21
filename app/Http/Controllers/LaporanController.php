<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Khill\Lavacharts\Lavacharts;
use App\Models\Obat;
use App\Models\Penjualan;
use Datatables;
use Session;
use DB;
use Illuminate\Support\Facades\Redirect;

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

    public function datatableLaporan(Request $request, $id_obat, $tgl_dari, $tgl_sampai){
        $column = array(
            0 => 'P.ID_PENJUALAN',
            1 => 'TGL_PENJUALAN',
            2 => 'O.NAMA_OBAT',
            3 => 'P.QTY'
        );

        $sortColumn = isset($request['order'][0]['column']) ? $request['order'][0]['column'] : 0;
        $kolom      = $column[$sortColumn];
        $sortDir    = isset($request['order'][0]['dir']) ? $request['order'][0]['dir'] : 'ASC';

        $searchData = "";
        if(!empty($request['search']['value'])) {
            $searchData = " AND (O.NAMA_OBAT LIKE '%{$request['search']['value']}%' OR
                             DATE_FORMAT(P.TGL_PENJUALAN, '%d %M %Y') LIKE '%{$request['search']['value']}%') ";
        }
          
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

        $no     = $request['start'];
        $sampai = $request['length'];

        $sql = "SELECT P.ID_PENJUALAN, P.ID_OBAT, O.NAMA_OBAT, DATE_FORMAT(P.TGL_PENJUALAN, '%d %M %Y')AS TGL_PENJUALAN, P.QTY, U.USERNAME 
                    FROM PENJUALAN AS P 
                    JOIN OBAT AS O ON P.ID_OBAT = O.ID_OBAT
                    JOIN USERS AS U ON P.ID_USER = U.ID_USER
                    WHERE 1 $filter $searchData
                    ORDER BY $kolom $sortDir
                    LIMIT $no,$sampai";

        $sql_filter = "SELECT COUNT(P.ID_PENJUALAN) AS TOTAL_DATA 
                    FROM PENJUALAN AS P 
                    JOIN OBAT AS O ON P.ID_OBAT = O.ID_OBAT
                    WHERE 1 $filter $searchData";

        $sql_total = "SELECT COUNT(P.ID_PENJUALAN) AS TOTAL_DATA 
                    FROM PENJUALAN AS P 
                    JOIN OBAT AS O ON P.ID_OBAT = O.ID_OBAT
                    WHERE 1";

        $sql_qty = "SELECT SUM(P.QTY) AS TOTAL_QTY
                    FROM PENJUALAN AS P 
                    JOIN OBAT AS O ON P.ID_OBAT = O.ID_OBAT
                    JOIN USERS AS U ON P.ID_USER = U.ID_USER
                    WHERE 1 $filter $searchData";

        $query = DB::select($sql);
        $totalData = DB::select($sql_total)[0]->TOTAL_DATA;
        $totalFiltered = DB::select($sql_filter)[0]->TOTAL_DATA;
        $totalQty = DB::select($sql_qty)[0]->TOTAL_QTY;
        
        $data = array();

        foreach($query AS $temp){
            $no++;
            $nestedData                        = array();
            $nestedData['NOMOR']               = $no;
            $nestedData['TGL_PENJUALAN']       = $temp->TGL_PENJUALAN;
            $nestedData['NAMA_OBAT']           = $temp->NAMA_OBAT;
            $nestedData['QTY']                 = $temp->QTY;
            $nestedData['ACTION']              = "<a href='/datatable/edit/$temp->ID_PENJUALAN'><button type='button' class='btn btn-primary'>Edit Item</button></a>    <a href='/datatable/delete/$temp->ID_PENJUALAN'><button type='button' class='btn btn-danger'>Delete Item</button></a>";
            $data[] = $nestedData;
           }

          $json_data = array(
                      "draw"            => intval( $request['draw'] ),
                      "recordsTotal"    => intval( $totalData ),
                      "recordsFiltered" => intval($totalFiltered),
                      "totalQty"        => $totalQty,
                      "data"            => $data
                      );

          echo json_encode($json_data);
    }

    public function delete(Request $request,$id_penjualan){
        Penjualan::find($id_penjualan)->delete();
        Session::put('alert-success', 'Data Berhasil di Hapus');
        return Redirect::back();
    }

    public function edit(Request $request,$id_penjualan){
        $data = [
        'penjualan' => Penjualan::where('id_penjualan',$id_penjualan)
                ->join('obat','penjualan.id_obat','=','obat.id_obat')->first()
        ];
        return view('edit',$data);
    }

    public function editAction(Request $request){
        Penjualan::find($request->input('id'))->update(['qty' => $request->input('qty')]);
        
        Session::put('alert-success', 'Data Berhasil di Edit');
        return Redirect::to('/tabel');
    }

}
