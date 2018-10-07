@extends('adminlte::page')

@section('title', 'Grafik Data Penjualan')

@section('content_header')
    <h1>Grafik Data Penjualan</h1>
@stop

@section('content')
<div class="box box-primary">
  <div id="curve_chart"></div>
  <form role="form" method="post" action="{{url('/grafikDetail')}}" enctype="multipart/form-data"  class="form-horizontal">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
  <div class="box-body" id="body"> 
    <div class="col-xs-12">
      <h3>Pilih Tanggal untuk Detail Grafik</h3>
    </div>  
    <br>  
    <div class="col-xs-6">
      <label>Tanggal dari</label>
      <select class="form-control select2" name="tgl_dari" style="width: 100%;">
        <option value="">-Pilih Tanggal Dari-</option>
        @foreach($search as $s)
        <option value="{{date('d-M-Y',strtotime($s->tgl_penjualan))}}">{{date('d-M-Y',strtotime($s->tgl_penjualan))}}</option>
        @endforeach
      </select>
    </div>

    <div class="col-xs-6">
      <label>Tanggal sampai</label>
      <select class="form-control select2" name="tgl_sampai" style="width: 100%;">
        <option value="">-Pilih Tanggal Sampai-</option>
        @foreach($search as $s)
        <option value="{{date('d-M-Y',strtotime($s->tgl_penjualan))}}">{{date('d-M-Y',strtotime($s->tgl_penjualan))}}</option>
        @endforeach
      </select>
    </div>
    <br>
    <div class="col-xs-6">
      <input type="hidden" class="form-control" name='periode' id="periode" value="{{$periode}}">
      <input type="hidden" class="form-control" name='obat' id="obat" value="{{$obat}}">
    </div>   
  </div>
  <div class="box-footer">
    <button type="submit" class="btn btn-primary">Next</button>
  </div>
</div>

@stop

@section('js')
    <script src="../../bower_components/select2/dist/js/select2.full.min.js"></script>
    <link rel="stylesheet" href="../../bower_components/select2/dist/css/select2.min.css">
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);

      var datatable;
      $(function () {
    //Initialize Select2 Elements
        $('.select2').select2()
      })
      
      function drawChart() {
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Year');
        data.addColumn('number', 'Qty');
        @foreach($search as $s)
        data.addRow(['{{date('d-M-Y',strtotime($s->tgl_penjualan))}}',{{$s['qty']}}]);
        @endforeach
         
        var options = {
          title: 'Data Penjualan {{$nama_obat}}',
          curveType: 'function',
          legend: { position: 'bottom' },
          pointSize: 5,
          width:1100,
          height:450,
          annotations: {
            style: 'line'
          }
        };

        var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));

        chart.draw(data, options);
      }
    </script>
@stop