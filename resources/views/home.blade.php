@extends('layouts.master')

@section('content')

    {{Form::open(['route' => 'shoot.store','class' => 'form-inline'])}}

    <div class="form-group">
        {{Form::label('Magnet URL')}}
        {{Form::text('magnet_url', null, ['class' => 'form-control'])}}
        {{Form::text('time', null, ['class' => 'form-control'])}}
        {{Form::selectRange('amount', '1', \Config::get('torshot.max_amount'))}}
    </div>
    <div class="form-group">
        {{Form::submit('Shoot', ['class' => 'btn btn-default'])}}
    </div>
    {{Form::close()}}

@endsection

@section('footer_scripts')
    <script src="//js.pusher.com/2.2/pusher.min.js"></script>
    <script>
        // Enable pusher logging - don't include this in production
        Pusher.log = function (message) {
            if (window.console && window.console.log) {
                window.console.log(message);
            }
        };

        var pusher = new Pusher('b747881bbf8da0631272');
        var channel = pusher.subscribe('test_channel');
        channel.bind('App\\Events\\FramesCaptured', function (data) {
            $('body').append('<img src="' + data.filenames[0] + '" />');
        });
    </script>
@endsection
