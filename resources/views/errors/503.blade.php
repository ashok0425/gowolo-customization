@extends('errors.layout')

@section('code', '503')
@section('title', 'Under Maintenance')

@section('message')
    We're performing scheduled maintenance to improve your experience. We'll be back shortly.
@endsection

@section('illustration')
<svg viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
    <circle cx="100" cy="100" r="80" fill="#f3e8fb"/>
    <circle cx="100" cy="100" r="60" fill="#ede4f7"/>
    <!-- Wrench -->
    <g transform="translate(100,95) rotate(-45)">
        <rect x="-4" y="-30" width="8" height="35" rx="4" fill="#662c87" opacity="0.9"/>
        <circle cy="-30" r="10" stroke="#662c87" stroke-width="4" fill="none" opacity="0.9"/>
        <rect x="-6" y="-36" width="12" height="6" fill="#ede4f7"/>
    </g>
    <!-- Progress dots -->
    <circle cx="75" cy="130" r="4" fill="#662c87" opacity="0.3"/>
    <circle cx="90" cy="130" r="4" fill="#662c87" opacity="0.5"/>
    <circle cx="105" cy="130" r="4" fill="#662c87" opacity="0.7"/>
    <circle cx="120" cy="130" r="4" fill="#662c87" opacity="0.9"/>
</svg>
@endsection

@section('actions')
<a href="javascript:location.reload()" class="btn btn-primary">
    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
    Refresh Page
</a>
@endsection
