@extends('layouts.app')
@section('content')
<div class="row">
    <div class="col-md-8 col-md-offset-2">
        @if (session('stage 2'))
        <form action=" {{ route('reset-security-2')}}" method="POST">
            {{ csrf_field() }}
            <div class="col-md-6">
                <div class="form-group">
                    <label for="sec_question"> Security- Question </label>
                    <select class="form-control input-lg" name="sec_question">
                        <option selected disabled>Pick Up A Question</option>
                        <option value="who_is_your_favorite_doctor_or_teacher">Who is your favorite Doctor Or Teacher ?</option>
                        <option value="where_are_you_from">Where Are you from?</option>
                        <option value="what_is_your_hobby">What is your hobby?</option>
                        <option value="what_is_your_favorite_car">What is your favorite car ?</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="sec_answer"> Answer Your Security- Question </label>
                        <input type="password" class="form-control input-lg" placeholder="The Answer goes here" name="sec_answer">
                </div>

            </div>
            <div class="col-md-12">
                <div class="form-group">

                    <button type="submit" class="btn btn-success form-control input-lg">
                        Go To Step 3
                    </button>
                </div>

            </div>
        </form>
        @elseif(session('stage 3'))
        <form action="{{ route('reset-security-3')}}" method="POST">
            {{csrf_field()}}
            <div class="col-md-6">
                <div class="form-group">
                    <label for="Password"> Password </label>
                        <input type="password" class="form-control input-lg" placeholder="Password" name="password">
                </div>

            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="Password"> Password Confirmation </label>
                        <input type="password" class="form-control input-lg" placeholder="Password" name="password_confirmation">
                </div>

            </div>
            <div class="col-md-12">

                <div class="form-group">

                    <button type="submit" class="btn btn-success form-control input-lg">
                    Change Password
                    </button>
                </div>
            </div>
        </form>
        @else
        <form action="{{ route('reset-security-1') }}" method="POST">
            {{csrf_field()}}
            <div class="col-md-6">
                <div class="form-group">
                    <label for="Email"> Email </label>
                    <input type="email" class="form-control input-lg" placeholder="example@example.com" name="email">
                </div>

            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="dob"> Date of Birthday </label>
                    <input type="date" class="form-control input-lg" name="dob">
                </div>

            </div>
            <div class="col-md-6">

                <div class="form-group">
                    <label for="location">Location </label>
                    <input type="text" class="form-control input-lg" name="location" placeholder="Ex : United States">
                </div>
            </div>
            <div class="col-md-12">

                <div class="form-group">

                    <button type="submit" class="btn btn-success form-control input-lg">
                    <i class="fa fa-spin fa-cog fa-lg" aria-hidden="true"></i>
                    Go To Step 2

                    </button>


                </div>
            </div>
        </form>
        @endif
    </div>
</div>
@endsection
