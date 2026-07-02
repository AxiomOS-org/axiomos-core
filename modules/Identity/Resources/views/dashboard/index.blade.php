@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Identity Dashboard</h1>
        <p class="text-muted mb-0">Identity operations, user links, memberships, and security surfaces.</p>
    </div>
    <a class="btn btn-outline-primary" href="/identity/identities">
        <i class="bi bi-arrow-right-circle me-1"></i> Open Identity Admin
    </a>
</div>

<div class="row g-3 mb-4">
    @foreach ($cards as $card)
        <div class="col-md-6 col-xl-4">
            <a href="{{ $card['path'] }}" class="text-decoration-none">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="text-muted small">{{ $card['label'] }}</div>
                        <div class="display-6 fw-semibold mt-2">{{ $card['count'] }}</div>
                    </div>
                </div>
            </a>
        </div>
    @endforeach
</div>

<div class="card">
    <div class="card-body">
        <h2 class="h5">Platform Links</h2>
        <p class="text-muted mb-3">Quick access to adjacent modules connected to identity lifecycle flows.</p>
        <div class="d-flex flex-wrap gap-2">
            <a class="btn btn-sm btn-outline-secondary" href="/users">Users</a>
            <a class="btn btn-sm btn-outline-secondary" href="/memberships">Memberships</a>
            <a class="btn btn-sm btn-outline-secondary" href="/organizations">Organizations</a>
            <a class="btn btn-sm btn-outline-secondary" href="/companies">Companies</a>
            <a class="btn btn-sm btn-outline-secondary" href="/departments">Departments</a>
        </div>
    </div>
</div>
@endsection
