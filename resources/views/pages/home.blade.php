@extends('master')

@section('content')
    <form class="form-inline" id="torshot-form">
        <div class="form-group">
            <label for="magnet_url">Magnet URL</label>
            <input type="text" name="magnet_url" class="form-control" id="magnet_url">
        </div>
        <div class="form-group">
            <label for="time">Time</label>
            <input type="text" name="time" class="form-control" id="time" placeholder="00:01:00">
        </div>
        <div class="form-group">
            <label for="amount">Amount</label>
            <input type="number" name="amount" class="form-control" id="amount" min="0" max="10">
        </div>
        <button class="btn btn-default">Shoot</button>
    </form>
    <div id="app">
        <torshot-frame :frame="frames"></torshot-frame>
    </div>

    <template id="frame-template">
        <div>
            @{{frame.src}}
        </div>
    </template>
@endsection

@section('footer_scripts')
    <script src="//js.pusher.com/2.2/pusher.min.js"></script>

    <script>
        $('#torshot-form').submit(function (event) {
            event.preventDefault();
            $.post('/api/frame', $(this).serialize(), function (data, status) {
                console.log(data);
                console.log(status);
            });
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
