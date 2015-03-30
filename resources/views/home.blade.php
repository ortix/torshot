@extends('layouts.master')

@section('content')

    {{Form::open(['route' => 'shoot.store','class' => 'form-inline'])}}

    <div class="form-group">
        {{Form::label('Magnet URL')}}
        {{Form::text('magnet',null, ['class' => 'form-control'])}}
    </div>
    <div class="form-group">
        {{Form::submit('Shoot', ['class' => 'btn btn-default'])}}
    </div>
    {{Form::close()}}
@endsection
