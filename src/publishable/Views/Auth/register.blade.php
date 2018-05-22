@extends('layouts.app')

@section('content')
<div class="container">
 <div class="row">
    <div class="col-md-8 col-md-offset-2">
        <form action="{{ route('register') }}" method="POST" autocomplete="off">
            {{ csrf_field() }}
            <div class="col-md-6">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input placeholder="johndoe" name="username" id="name" type="text" class="form-control input-lg" value="{{ old('username') }}" required autofocus>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input placeholder="John" name="first_name" id="name" type="text" class="form-control input-lg" value="{{ old('first_name') }}" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input placeholder="Doe" name="last_name" id="name" type="text" class="form-control input-lg" value="{{ old('last_name') }}" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input placeholder="example@example.com" name="email" id="name" type="text" class="form-control input-lg" value="{{ old('email') }}" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="password">Password</label>
                    <input placeholder="password" name="password" id="name" type="password" class="form-control input-lg" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="password_confirmation">Password Confirmation</label>
                    <input placeholder="Password Confirmation" id="name" name="password_confirmation" type="password" class="form-control input-lg" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="sec_question">Security Question</label>
                    <select name="sec_question" class="form-control input-lg">
                        <option value="where_are_you_from">Where Are You From ?</option>
                        <option value="what_is_your_hobby">What Is Your Hobby ?</option>
                        <option value="what_is_your_favorite_car">What Is Your Favorite Car ?</option>
                        <option value="who_is_your_favorite_doctor_or_teacher">Who Is Your Favorite Doctor/Teacher</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="sec_answer">Security Answer</label>
                    <input placeholder="Security Answer" name="sec_answer" id="name" type="password" class="form-control input-lg" value="{{ old('sec_answer') }}" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="location">Location</label>
                    <input placeholder="Location" name="location" id="name" type="text" class="form-control input-lg" required value="{{ old('location') }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="dob">Date Of Birth</label>
                    <input id="name" name="dob" type="date" class="form-control input-lg" required value="{{ old('dob') }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <input type="submit" class="btn btn-primary btn-lg " value="Register">
                    <input type="reset" class="btn btn-outline btn-lg " value="Reset">
                </div>
            </div>
        </form>
    </div>
</div>
</div>
@endsection
