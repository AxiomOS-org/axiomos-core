@extends('layouts.admin')

@section('content')
    <div class="card">
        <div class="card-body p-4">
            <h1 class="h4 mb-3">Reset password</h1>
            <form method="post" action="/api/auth/password/reset">
                <div class="mb-3">
                    <label class="form-label">Token</label>
                    <input type="text" name="token" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">New password</label>
                    <input type="password" name="new_password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Reset password</button>
            </form>
        </div>
    </div>
@endsection
