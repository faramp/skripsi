@extends('adminlte::page')

@section('title', 'Forcasting')

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
  <div class="box-body">
    <div id="curve_chart" style="width: 1100px; height: 400px"></div>
  </div>
  <!-- /.box-body-->
</div>

<div class="box">
  <div class="box-header">
    <h3 class="box-title">Exponential Smoothing</h3>
  </div>
  <!-- /.box-header -->
  <div class="box-body no-padding">
    <table class="table table-striped">
      <tr>
        <th style="width: 10px">#</th>
        <th>Metode</th>
        <th>Nilai Awal</th>
        <th>Parameter</th>
        <th style="width: 40px">MSE</th>
      </tr>
      <tr>
        @foreach($single as $s)
        <td>{!!$s!!}</td>
        @endforeach
      </tr>
      <tr>
        @foreach($double as $d)
        <td>{!!$d!!}</td>
        @endforeach
      </tr>
      <tr>
        @foreach($holt as $h)
        <td>{!!$h!!}</td>
        @endforeach
      </tr>
      <tr>
        @foreach($winter as $w)
        <td>{!!$w!!}</td>
        @endforeach
      </tr>
    </table>
    <p>Hasil peramalan untuk {{$periode}} hari adalah: </p>
    @for($p=1; $p<=$periode; $p++)
    <p>Hari ke-{{$p}} kedepan yaitu {{$hasil_forecast[$p]}}</p>
    @endfor
  </div>
  <!-- /.box-body -->
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
        };

        var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));

        chart.draw(data, options);
      }
    </script>
@stop