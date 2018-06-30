@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Invalid Invitation</div>
                    @if(isset($sent) && $sent === true)
                        <div id="welcome" class="card-body">
                            <p>
                                A new Invitation has been sent, please check your email.
                            </p>
                        </div>
                        @elseif(!isset($sent) && isset($user_id))
                            <div id="welcome" class="card-body">
                                <p>
                                    Your Invitation is not valid. Please click below to generate a new Invitation.
                                </p>
                                <form action="/api/reinvite" class="validation" method="post">
                                    <input type="hidden" name="userId" value="{{ $user_id }}">
                                    {{ csrf_field() }}
                                    <button type="submit" class="btn-danger">Create new Invitation</button>
                                </form>
                            </div>
                        @else
                        <div id="welcome" class="card-body">
                            <p>
                                Invalid Invitation
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection
