@extends('layouts.master')

@section('content')
    <div class="container">
        {{Form::open(['route' => 'shoot.store','class' => 'form-inline', 'id' => 'torshot-form'])}}

        <div class="form-group">
            {{Form::label('Magnet URL')}}
            {{Form::text('magnet_url', null, ['class' => 'form-control'])}}
            {{Form::text('time', null, ['class' => 'form-control'])}}
            {{Form::selectRange('amount', '0', \Config::get('torshot.max_amount'))}}
        </div>
        <div class="form-group">
            {{Form::submit('Shoot', ['class' => 'btn btn-default'])}}
        </div>
        {{Form::close()}}
    </div>

@endsection

@section('footer_scripts')
    <script src="//js.pusher.com/2.2/pusher.min.js"></script>

    <script>
        $('#torshot-form').submit(function (event) {
            event.preventDefault();
            $.post('shoot', $(this).serialize());
        })
    </script>


    <script>
        // Enable pusher logging - don't include this in production
        Pusher.log = function (message) {
            if (window.console && window.console.log) {
                window.console.log(message);
            }
        };

        var pusher = new Pusher('b747881bbf8da0631272');
        var channel = pusher.subscribe('test_channel');
        channel.bind('App\\Events\\SingleFrameCaptured', function (data) {
            $('body').append('<img style="width:200px" src="' + data.filename + '" />');
        });
    </script>
@endsection
