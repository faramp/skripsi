@extends('adminlte::page')

@section('title', 'Forcasting')

@section('content_header')
    <h1>Exponential Smoothing</h1>
@stop

@section('content')
    <div class="box box-primary">
        <form role="form" method="post" action="{{url('/forecasting')}}" enctype="multipart/form-data"  class="form-horizontal">
          <input type="hidden" name="_token" value="{{ csrf_token() }}">
          <div class="box-body">
          	<div class="form-group">                   
              <div class="col-xs-4">
                <label>Obat</label>
                <select class="form-control" name="obat" required>
                  <option value="">- Pilih Obat -</option>
                  @foreach($obat as $i)
                  <option value="{{$i->id_obat}}">{{$i->nama_obat}}</option>
                  @endforeach
                </select>
              </div>   
              <div class="col-xs-4">
                <label>Periode</label>
                <select class="form-control" name="periode" required>
                  <option value="">- Pilih Periode -</option>
                  <option value="1">1 Hari</option>
                  <option value="2">2 Hari</option>
                  <option value="3">3 Hari</option>
                  <option value="4">4 Hari</option>
                  <option value="5">5 Hari</option>
                  <option value="6">6 Hari</option>
                  <option value="7">7 Hari</option>
                </select>
              </div>                             
            </div>
          </div>
          <!-- /.box-body -->

          <div class="box-footer">
            <button type="submit" class="btn btn-primary">Submit</button>
          </div>
        </form>
    </div>
@stop