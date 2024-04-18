@extends('dashboard')

@section('content')
    <h1>Hari ini {{ now()->format('d M Y') }}</h1>
    <div class="badge">default</div>
    <div class="badge badge-neutral">neutral</div>
    <div class="badge badge-primary">primary</div>
    <div class="badge badge-secondary">secondary</div>
    <div class="badge badge-accent">accent</div>
    <div class="badge badge-ghost">ghost</div>
@endsection
