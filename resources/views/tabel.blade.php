@extends('adminlte::page')

@section('title', 'Laporan Penjualan')

@section('content_header')
    <h1>Laporan Penjualan</h1>
@stop

@section('content')
<div class="flash-message" style="margin-left: -16px;margin-right: -16px; margin-top: 13px;">
  @foreach (['danger', 'warning', 'success', 'info'] as $msg)
  @if(Session::has('alert-' . $msg))
<div class="alert alert-{{ $msg }}">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  <p class="" style="border-radius: 0">{{ Session::get('alert-' . $msg) }}</p>
</div>
  {!!Session::forget('alert-' . $msg)!!}
  @endif
  @endforeach
</div>
    <div class="box box-primary">
      <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <div class="col-xs-4">
            <label> Obat </label>
            <select class="form-control" name="obat" id="obat">
              <option value="0">- Pilih Obat -</option>
              @foreach ($obat as $data)
              <option value = "{{ $data->id_obat }}" > {{ $data->nama_obat }} </option>
              @endforeach
            </select>
        </div>
        <div class="col-xs-4">
          <label> Tanggal Dari </label>
          <input type="text"class="form-control pull-right datepicker" id="tgl_dari" placeholder="From">
        </div>
        <div class="col-xs-4">
          <label> Tanggal Sampai </label>
          <input type="text" class="form-control pull-right datepicker" id="tgl_sampai" placeholder="To">
        </div>
        <div class="row">
          <center>
            <button class="btn btn-primary ladda-button margin-inline filterLaporan" id="filterLaporan" data-style="expand-left"><span class="ladda-label"><i class="icmn-filter"></i> Filter</span></button>
          </center>
        </div>
          
        {{-- END FILTER --}}
      <hr>
      <div class="row">
          <div class="col-lg-12">
            <table class="table nowrap thead-inverse" id="dataLaporan" width="100%">
                <thead>
                <tr>
                  <th> No</th>
                  <th> Tanggal</th>
                  <th> Nama Obat</th>
                  <th> Qty</th>
                  <th> Action</th>
                </tr>
                </thead>
                <tfoot>
                <tr>
                  <th></th>
                  <th></th>                  
                  <th> Total Qty</th>
                  <th></th>
                  <th></th>
                </tr>
                </tfoot>
                <tbody>
                </tbody>
            </table>
          </div>
      </div>
    </div>
@stop
@section('js')
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.5.0/css/bootstrap-datepicker.css" rel="stylesheet">
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.5.0/js/bootstrap-datepicker.js"></script>
<script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css"></script>
<script>

var datatable;
var idobat = $("#obat");
var tgldari = $("#tgl_dari");
var tglsampai = $("#tgl_sampai");

    $(function(){

      $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        }
      })

      $('.datepicker').datepicker({
        format: 'dd MM yyyy'
      });

      datatable = $('#dataLaporan').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: 'datatable/0/0/0',
        columns: [
          {data: 'NOMOR', name: 'NOMOR'},
          {data: 'TGL_PENJUALAN', name: 'TGL_PENJUALAN'},
          {data: 'NAMA_OBAT', name: 'NAMA_OBAT'},
          {data: 'QTY', name: 'QTY'},
          {data: 'ACTION', name: 'ACTION'},
        ],
        'footerCallback': function( tfoot, data, start, end, display ) {
          var response = this.api().ajax.json();
             if(response){
                var $th = $(tfoot).find('th');
                $th.eq(3).html("<b>"+response['totalQty']+"</b>");
             }
         }
      });  
      
      $(document).on('click', '.filterLaporan', function() {
        var generateUrl = generate_url_filter();
        console.log('id_obat'+generate_url_filter().brand);
        datatable.ajax.url(generateUrl).load();
      });
    });

    function generate_url_filter(){

      var alamatURL = "";
      var obat          = idobat.val();
      var tanggal_from  = tgldari.val();
      var tanggal_to    = tglsampai.val();

        if(tanggal_from===""){
          tanggal_from = 0;
        }
        if(tanggal_to===""){
          tanggal_to = 0;
        }

      alamatURL = 'datatable/'+obat+'/'+tanggal_from+'/'+tanggal_to+'';

      return alamatURL;
    }

</script>
@stop