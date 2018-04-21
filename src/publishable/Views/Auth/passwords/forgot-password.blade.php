@extends('layouts.app')
@section('content')
<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <form action="{{ route('forgot-password')}}" method="POST">
            {{csrf_field()}}
            <div class="col-md-12">
                <div class="form-group">
                    <label for="email">Email | Username</label>
                    <input autofocus required value="{{ old('email') }}" placeholder="Valid Email Or Username" name="email" type="text" class="form-control input-lg">
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <input type="submit" class="btn btn-primary btn-lg " value="Send Code">
                    <input type="reset" class="btn btn-outline btn-lg " value="Reset">
                </div>
                <small><p><a href="{{ route('reset-security') }}" >Reset By Security Question ? </a></p></small>
            </div>
        </form>
    </div>
</div>
</div>
@endsection
