@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">{{ $title ?? 'POS' }}</h1>
    <span class="badge text-bg-light">API: {{ $apiBase ?? '' }}</span>
</div>
<div class="card">
    <div class="card-body">
        <p class="text-muted mb-2">Entity: {{ $entityLabel ?? '' }}</p>
        <div class="row">
            <div class="col-md-6">
                <h6>Columns</h6>
                <ul class="mb-0">@foreach ($columns ?? [] as $column)<li><code>{{ $column }}</code></li>@endforeach</ul>
            </div>
            <div class="col-md-6">
                <h6>Fields</h6>
                <ul class="mb-0">@foreach ($fields ?? [] as $field)<li><code>{{ $field }}</code></li>@endforeach</ul>
            </div>
        </div>
    </div>
</div>
@endsection
