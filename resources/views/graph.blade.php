@extends('adminlte::page')

@section('title', 'Graph')

@section('content_header')
    <h1>Graph</h1>
@stop

@section('content')
  <div id="curve_chart" style="width: 1100px; height: 400px"></div>
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
        };

        var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));

        chart.draw(data, options);
      }
    </script>
@stop
