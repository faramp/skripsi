@extends('adminlte::page')

@section('title', 'Input Obat')

@section('content_header')
    <h1>Input Obat</h1>
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
    <form role="form" method="post" action="{{url('/input')}}" enctype="multipart/form-data"  class="form-horizontal">
      <input type="hidden" name="_token" value="{{ csrf_token() }}">
      <div class="box-body">
        <div class="col-xs-4">
          <label>Select</label>              
          <select class="form-control " name="obat" required>
          	<option value="">- Pilih Obat -</option>
          	@foreach($obat as $i)
            @if($i->id_obat==2 || $i->id_obat==4 || $i->id_obat==7 || $i->id_obat==10)
            <option value="{{$i->id_obat}}">{{$i->nama_obat}} (biji)</option>
            @else
            <option value="{{$i->id_obat}}">{{$i->nama_obat}} (strip)</option>
            @endif
            @endforeach
          </select>
        </div>
        <div class="col-xs-4">
        	<label for="exampleInputFile">Qty</label>
          <input type="text" class="form-control" name='qty' id="qty" required>
        </div>
      </div>
      <!-- /.box-body -->
      <div class="box-footer">
        <button type="submit" class="btn btn-primary">Submit</button>
      </div>
    </form>
  </div>
@stop