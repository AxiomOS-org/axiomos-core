@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">{{ $title ?? 'Reporting' }}</h1>
</div>
<div class="row g-3">
    @foreach ($cards ?? [] as $card)
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="text-muted small">{{ $card['label'] }}</div>
                    <div class="display-6">{{ $card['count'] }}</div>
                    <a href="{{ $card['path'] }}" class="stretched-link">Open</a>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection
