@extends('adminlte::page')

@section('title', 'Upload')

@section('content_header')
    <h1>Upload</h1>
@stop

@section('content')
  <div class="box box-primary">
    <form role="form" method="post" action="{{url('/fileupload')}}" enctype="multipart/form-data"  class="form-horizontal">
      <input type="hidden" name="_token" value="{{ csrf_token() }}">
      <div class="box-body">
        <label>Select</label>              
        <select class="form-control" name="obat" required>
        	<option value="">- Pilih Obat -</option>
        	@foreach($obat as $i)
          <option value="{{$i->id_obat}}">{{$i->nama_obat}}</option>
          @endforeach
        </select>
      	<p style="font-size: 15px;">Silahkan upload file penjualan dalam format Excel (.xlsx atau .xls)</p>
        	<label for="exampleInputFile">File input</label>
        	<input name="excel" type="file" id="excel" type="excel"type="file" id="exampleInputFile">
      </div>
      <!-- /.box-body -->
      <div class="box-footer">
        <button type="submit" class="btn btn-primary">Upload</button>
      </div>
    </form>
  </div>
@stop