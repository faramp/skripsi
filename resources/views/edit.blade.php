@extends('adminlte::page')

@section('title', 'Edit Obat')

@section('content_header')
    <h1>Edit Obat</h1>
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
    <form role="form" method="post" action="{{url('/datatable/edit')}}" enctype="multipart/form-data"  class="form-horizontal">
      <input type="hidden" name="_token" value="{{ csrf_token() }}">
      <div class="box-body">
        <div class="col-xs-4">
          <label>Obat</label>  
          @if($penjualan->id_obat==2 || $penjualan->id_obat==4 || $penjualan->id_obat==7 || $penjualan->id_obat==10)
          <input type="text" class="form-control" name='obat' id="obat" value="{{$penjualan->nama_obat}} (biji)" disabled="">
          @else
          <input type="text" class="form-control" name='obat' id="obat" value="{{$penjualan->nama_obat}} (strip)" disabled="">  
          @endif
          <input type="text" class="form-control hidden" name='id' id="id" value="{{$penjualan->id_penjualan}}">
        </div>
        <div class="col-xs-4">
          <label for="exampleInputFile">Tanggal</label>
          <input type="text" class="form-control" name='tanggal' id="tanggal" value="{{date('d F Y', strtotime($penjualan->tgl_penjualan))}}" disabled="">
        </div>
        <div class="col-xs-4">
        	<label for="exampleInputFile">Qty</label>
          <input type="text" class="form-control" name='qty' id="qty" value="{{$penjualan->qty}}" required>
        </div>
      </div>
      <!-- /.box-body -->
      <div class="box-footer">
        <button type="submit" class="btn btn-primary">Edit</button>
      </div>
    </form>
  </div>
@stop