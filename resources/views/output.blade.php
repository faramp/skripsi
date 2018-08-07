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
    <div id="line-chart" style="height: 300px;"></div>
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
  </div>
  <!-- /.box-body -->
</div>
@stop

@section('js')
<script>
  $(function() {


/*
     * LINE CHART
     * ----------
     */
    //LINE randomly generated data

    var sin = [], cos = []
    for (var i = 0; i < 14; i += 0.5) {
      sin.push([i, Math.sin(i)])
      cos.push([i, Math.cos(i)])
    }
    var line_data1 = {
      data : sin,
      color: '#3c8dbc'
    }
    var line_data2 = {
      data : cos,
      color: '#00c0ef'
    }
    $.plot('#line-chart', [line_data1, line_data2], {
      grid  : {
        hoverable  : true,
        borderColor: '#f3f3f3',
        borderWidth: 1,
        tickColor  : '#f3f3f3'
      },
      series: {
        shadowSize: 0,
        lines     : {
          show: true
        },
        points    : {
          show: true
        }
      },
      lines : {
        fill : false,
        color: ['#3c8dbc', '#f56954']
      },
      yaxis : {
        show: true
      },
      xaxis : {
        show: true
      }
    })
    //Initialize tooltip on hover
    $('<div class="tooltip-inner" id="line-chart-tooltip"></div>').css({
      position: 'absolute',
      display : 'none',
      opacity : 0.8
    }).appendTo('body')
    $('#line-chart').bind('plothover', function (event, pos, item) {

      if (item) {
        var x = item.datapoint[0].toFixed(2),
            y = item.datapoint[1].toFixed(2)

        $('#line-chart-tooltip').html(item.series.label + ' of ' + x + ' = ' + y)
          .css({ top: item.pageY + 5, left: item.pageX + 5 })
          .fadeIn(200)
      } else {
        $('#line-chart-tooltip').hide()
      }

    })
    /* END LINE CHART */
  })
</script>
@stop