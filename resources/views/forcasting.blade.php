@extends('adminlte::page')

@section('title', 'Forcasting')

@section('content_header')
    <h1>Exponential Smoothing</h1>
@stop

@section('content')
    <div class="box box-primary">
        <form role="form" method="post" action="{{url('/forcasting')}}" enctype="multipart/form-data"  class="form-horizontal">
          <input type="hidden" name="_token" value="{{ csrf_token() }}">
          <div class="box-body">
          	<div class="form-group">
              <label>Select</label>              
              <select class="form-control" name="obat" required>
              	<option value="">- Pilih Obat -</option>
              	@foreach($obat as $i)
                <option value="{{$i->id_obat}}">{{$i->nama_obat}}</option>
                @endforeach
              </select>
            </div>
          </div>
          <!-- /.box-body -->

          <div class="box-footer">
            <button type="submit" class="btn btn-primary">Submit</button>
          </div>
        </form>
    </div>
@stop