@extends('layouts.app')
@section('content')
<div class="row">
	<div class="col-md-8 col-md-offset-2">
		<form action="{{ route('reset-password')}}" method="POST">
			{{csrf_field()}}

			<div class="col-md-8">
				<div class="form-group">
					<label for="password">New Password</label>
					<input placeholder="password" name="password"  required type="password" class="form-control input-lg">
				</div>
			</div>
			<div class="col-md-8">
				<div class="form-group">
					<label for="password_confirmation">Confirm New Password</label>
					<input placeholder="Confirm Your Password" name="password_confirmation"  required type="password" class="form-control input-lg">
				</div>
			</div>
			<div class="col-md-8">
				<div class="form-group">
					<input type="submit" class="btn btn-primary btn-lg " value="Setup New Password">
					<input type="reset" class="btn btn-outline btn-lg " value="Reset">
				</div>
			</div>
		</form>
	</div>
</div>

@endsection
