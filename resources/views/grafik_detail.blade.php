@extends('adminlte::page')

@section('title', 'Detail Grafik')

@section('content_header')
    <h1>Detail Grafik</h1>
@stop

@section('content')
<div class="box box-primary">
  <div id="curve_chart" style="overflow-y: auto"></div>
  <form role="form" method="post" action="{{url('/forecasting')}}" enctype="multipart/form-data"  class="form-horizontal">
  <input type="hidden" name="_token" value="{{ csrf_token() }}">
  <div class="box-body" id="body">    
    
    <div class="col-xs-6">
      <label for="exampleInputFile">Periode Musiman</label>
      <input type="number" class="form-control" name='musiman' id="musiman" required>
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
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'x');
        data.addColumn({type:'string', role:'annotation'});
        data.addColumn('number', 'Qty');
        @foreach($query as $s)
        @if($s['index']%7==0)
        data.addRow(['{{$s['index']}}',' ',{{$s['qty']}}]);
        @else
        data.addRow(['{{$s['index']}}',null,{{$s['qty']}}]);
        @endif
        @endforeach
         
        var options = {
          title: 'Detail Grafik dari {{$tgl_dari}} sampai {{$tgl_sampai}}',
          curveType: 'function',
          legend: { position: 'bottom' },
          pointSize: 5,
          width:1100,
          height:350,
          annotations: {
            style: 'line'
          }
        };

        var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));

        chart.draw(data, options);
      }
    </script>
@stop