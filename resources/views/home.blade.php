@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            {{--<div class="card">--}}

                @if($verified === 1)
                    {{--<div class="card-header">Dashboard</div>--}}
                    <div id="here" class="card-body">
                        <auth-clients></auth-clients>
                        <clients></clients>
                        <access-tokens></access-tokens>
                    </div>
                @elseif($verified !== 1)
                    <div class="card-header">Welcome to Evenz!</div>
                    <div id="welcome" class="card-body">
                        <p>
                            Please verify your Email Address to register a new Client
                        </p>
                    </div>
                @else
                    <div class="card-header">Welcome to Evenz!</div>
                    <div id="welcome" class="card-body">
                        <p>
                            Problem processing your registration
                        </p>
                    </div>
                @endif
            {{--</div>--}}
        </div>
    </div>
</div>

@endsection
