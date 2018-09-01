
@extends('adminlte::page')

@section('title', 'Exponetial Smoothing')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
    <div class="container">

      <h1>Laravel Bootstrap Datepicker</h1>

      <input class="date form-control" type="text">

    </div>
@stop

@section('js')
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.5.0/css/bootstrap-datepicker.css" rel="stylesheet">

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.5.0/js/bootstrap-datepicker.js"></script>
<script type="text/javascript">

    $('.date').datepicker({  

       format: 'dd MM yyyy'

     });  

</script>
@stop