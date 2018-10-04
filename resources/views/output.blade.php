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
    <div id="data_asli_chart"></div>
    <div id="data_stasioner_chart"></div>
    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
      </button>
    </div>
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
        <th style="width: 15px">#</th>
        <th> Metode</th>
        <th> Rekap Training</th>
        <th> Nilai Awal</th>
        <th> Parameter</th>
        <th style="width: 40px"> MSE</th>        
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
    <p>MSE = {{$MSE}}</p>
  </div>

  <!-- MODAL SINGLE -->
  <div class="modal fade" id="modal-single">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Rekap Training Single</h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-lg-12">
              <table class="table nowrap thead-inverse" id="dataLaporan" width="100%">
                  <thead>
                  <tr>
                    <th> &Ycirc;<sub>1</sub></th>
                    <th> Alpha</th>
                    <th> MSE</th>
                  </tr>
                  </thead>
                  <tbody>
                    @if(!empty($kombinasiSingle))
                    @foreach($kombinasiSingle as $s)
                    <tr>
                      <td>{{$s[0]}}</td>
                      <td>{{$s[1]}}</td>
                      <td>{{$s[2]}}</td>
                    </tr>
                    @endforeach
                    @endif
                  </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>
  <!-- END MODAL SINGLE -->
  <!-- MODAL DOUBLE -->
  <div class="modal fade" id="modal-double">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Rekap Training Double</h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-lg-12">
              <table class="table nowrap thead-inverse" id="dataLaporan" width="100%">
                  <thead>
                  <tr>
                    <th> A<sub>0</sub></th>
                    <th> A'<sub>0</sub></th>
                    <th> Alpha</th>
                    <th> MSE</th>
                  </tr>
                  </thead>
                  <tbody>
                    @foreach($kombinasiDouble as $d)
                    <tr>
                      <td>{{$d[0]}}</td>
                      <td>{{$d[1]}}</td>
                      <td>{{$d[2]}}</td>
                      <td>{{$d[3]}}</td>
                    </tr>
                    @endforeach
                  </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>
  <!-- END MODAL DOUBLE -->
  <!-- MODAL HOLT -->
  <div class="modal fade" id="modal-holt">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Rekap Training Holt</h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-lg-12">
              <table class="table nowrap thead-inverse" id="dataLaporan" width="100%">
                  <thead>
                  <tr>
                    <th> A<sub>1</sub></th>
                    <th> T<sub>1</sub></th>
                    <th> Alpha</th>
                    <th> Beta</th>
                    <th> MSE</th>
                  </tr>
                  </thead>
                  <tbody>
                    @foreach($kombinasiHolt as $h)
                    <tr>
                      <td>{{$h[0]}}</td>
                      <td>{{$h[1]}}</td>
                      <td>{{$h[2]}}</td>
                      <td>{{$h[3]}}</td>
                      <td>{{$h[4]}}</td>
                    </tr>
                    @endforeach
                  </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>
  <!-- END MODAL HOLT -->
  <!-- MODAL WINTER -->
  <div class="modal fade" id="modal-winter">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Rekap Training Winter</h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-lg-12">
              <table class="table nowrap thead-inverse" id="dataLaporan" width="100%">
                  <thead>
                  <tr>
                    <th> A<sub>1</sub></th>
                    <th> T<sub>1</sub></th>
                    <th> S<sub>1</sub></th>
                    <th> Alpha</th>
                    <th> Beta</th>
                    <th> Miu</th>
                    <th> MSE</th>
                  </tr>
                  </thead>
                  <tbody>
                    @foreach($kombinasiWinter as $w)
                    <tr>
                      <td>{{$w[0]}}</td>
                      <td>{{$w[1]}}</td>
                      <td>{{$w[2]}}</td>
                      <td>{{$w[3]}}</td>
                      <td>{{$w[4]}}</td>
                      <td>{{$w[5]}}</td>
                      <td>{{$w[6]}}</td>
                    </tr>
                    @endforeach
                  </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>
  <!-- END MODAL WINTER -->

  <!-- /.box-body -->
</div>
@stop

@section('js')
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChartDataAsli);
      google.charts.setOnLoadCallback(drawChartDataStasioner);

      function drawChartDataAsli() {
        var data = google.visualization.arrayToDataTable([
          ['Year', 'Qty'],
          @foreach($search as $s)
          ['{{date('d-M-Y', strtotime($s->tgl_penjualan))}}', {{$s->qty}}],
          @endforeach
        ]);

        var options = {
          title: 'Data Asli',
          curveType: 'function',
          legend: { position: 'bottom' },
          pointSize: 5,
          width:1080,
          height:400,
        };

        var chart = new google.visualization.LineChart(document.getElementById('data_asli_chart'));

        chart.draw(data, options);
      }

      function drawChartDataStasioner() {
        var index = 0;
        var data = google.visualization.arrayToDataTable([
          ['Year', 'Stasioner'],
          @foreach($stasioner as $s)
          [index++, {{$s}}],
          @endforeach
        ]);

        var options = {
          title: 'Data Stasioner',
          curveType: 'function',
          legend: { position: 'bottom' },
          pointSize: 5,
          width:1080,
          height:400,
        };

        var chart = new google.visualization.LineChart(document.getElementById('data_stasioner_chart'));

        chart.draw(data, options);
      }
    </script>
@stop