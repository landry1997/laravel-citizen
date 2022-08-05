@extends('layouts.app')

@section('content')
    <div id="app" class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">

                @if(Session::has('message'))
                    <div class="alert alert-success">
                        {{session('message')}}
                    </div>
                @endif
                <div class="card">
                    <div class="card-header">Send push to Users</div>

                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                <th scope="col">Name</th>
                                <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $users = App\Models\User::all();
                                @endphp
                                @foreach ($users as $user)
                                    <tr>
                                        <td>{{ $user->name }}</td>
                                        <td>
                                            <form action="{{ route('sendPush') }}" method="post">
                                                @csrf
                                                <input type="hidden" name="id" value="{{$user->id}}" />

                                                <input class="btn btn-primary" type="submit" value="Send Push">
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script
    src="https://code.jquery.com/jquery-3.4.1.min.js"
    integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
    crossorigin="anonymous"></script>
    <script src="https://www.gstatic.com/firebasejs/6.3.4/firebase.js"></script>
    <script>
        $(document).ready(function(){
            const config = {
                apiKey: "AIzaSyALSh2rOpEgexolPLIh7Vnmsy_BSHOhd18",
                authDomain: "comaid-472c8.firebaseapp.com",
                projectId: "comaid-472c8",
                storageBucket: "comaid-472c8.appspot.com",
                messagingSenderId: "869541657964",
                appId: "1:869541657964:web:b050fc8a0c5b432b00c749",
                measurementId: "G-L0HG2S42JG"
            };
            firebase.initializeApp(config);
            const messaging = firebase.messaging();

            messaging
                .requestPermission()
                .then(function () {
                    return messaging.getToken()
                })
                .then(function(token) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: '{{ URL::to('/saveToken') }}',
                        type: 'POST',
                        data: {
                            user_id: {!! json_encode($user->id ?? '') !!},
                            fcm_token: token
                        },
                        dataType: 'JSON',
                        success: function (response) {
                            console.log(response)
                        },
                        error: function (err) {
                            console.log(" Can't do because: " + err);
                        },
                    });
                })
                .catch(function (err) {
                    console.log("Unable to get permission to notify.", err);
                });

            messaging.onMessage(function(payload) {
                const noteTitle = payload.notification.title;
                const noteOptions = {
                    body: payload.notification.body,
                    icon: payload.notification.icon,
                };
                new Notification(noteTitle, noteOptions);
            });
        });
    </script>
@endsection
