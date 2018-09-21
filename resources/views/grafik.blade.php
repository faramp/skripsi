@extends('adminlte::page')

@section('title', 'Exponential Forcasting')

@section('content_header')
    <h1>Exponential Smoothing</h1>
@stop

@section('content')
<div class="box box-primary">
  <div class="box-header with-border">
    <i class="fa fa-area-chart"></i>

    <h3 class="box-title">Line Chart</h3>

    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
      </button>
    </div>
  </div>
  <form role="form" method="post" action="{{url('/forecasting')}}" enctype="multipart/form-data"  class="form-horizontal">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
  <div class="box-body" id="body">    
    <div id="curve_chart" style="overflow-x: auto;"></div>
    <div class="col-xs-6">
      <label for="exampleInputFile">Periode Musiman</label>
      <input type="text" class="form-control" name='musiman' id="musiman" required>
      <input type="hidden" class="form-control" name='periode' id="periode" value="{{$periode}}">
      <input type="hidden" class="form-control" name='obat' id="obat" value="{{$obat}}">
    </div>    
  </div>
  <div class="box-footer">
    <button type="submit" class="btn btn-primary">Next</button>
  </div>
  <!-- /.box-body-->
</div>
@stop

@section('js')
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Year', 'Qty'],
          @foreach($search as $s)
          ['{{date('d-M-Y', strtotime($s->tgl_penjualan))}}', {{$s->qty}}],
          @endforeach
        ]);

        var options = {
          title: 'Data Penjualan',
          curveType: 'function',
          legend: { position: 'bottom' },
          pointSize: 5,
          width:1080,
          height:400,
        };

        var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));

        chart.draw(data, options);
      }
    </script>
@stop