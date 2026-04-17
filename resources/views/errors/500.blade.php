@extends('errors.layout')

@section('code', '500')
@section('title', 'Something Went Wrong')

@section('message')
    We're experiencing a technical issue on our end. Our team has been notified. Please try again in a few moments.
@endsection

@section('illustration')
<svg viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
    <circle cx="100" cy="100" r="80" fill="#f3e8fb"/>
    <circle cx="100" cy="100" r="60" fill="#ede4f7"/>
    <!-- Gear 1 -->
    <g transform="translate(85,90)">
        <circle r="15" fill="#662c87" opacity="0.9"/>
        <circle r="7" fill="#ede4f7"/>
        <rect x="-3" y="-20" width="6" height="10" rx="3" fill="#662c87" opacity="0.9"/>
        <rect x="-3" y="10" width="6" height="10" rx="3" fill="#662c87" opacity="0.9"/>
        <rect x="-20" y="-3" width="10" height="6" rx="3" fill="#662c87" opacity="0.9"/>
        <rect x="10" y="-3" width="10" height="6" rx="3" fill="#662c87" opacity="0.9"/>
    </g>
    <!-- Gear 2 (smaller) -->
    <g transform="translate(120,78)">
        <circle r="10" fill="#8e44ad" opacity="0.8"/>
        <circle r="5" fill="#ede4f7"/>
        <rect x="-2" y="-14" width="4" height="8" rx="2" fill="#8e44ad" opacity="0.8"/>
        <rect x="-2" y="6" width="4" height="8" rx="2" fill="#8e44ad" opacity="0.8"/>
        <rect x="-14" y="-2" width="8" height="4" rx="2" fill="#8e44ad" opacity="0.8"/>
        <rect x="6" y="-2" width="8" height="4" rx="2" fill="#8e44ad" opacity="0.8"/>
    </g>
    <!-- Lightning bolt (error) -->
    <path d="M108 105 L115 115 L110 115 L114 128 L105 117 L110 117 Z" fill="#e74c3c" opacity="0.85"/>
    <!-- Smoke puffs -->
    <circle cx="75" cy="68" r="5" fill="#bbb" opacity="0.3"/>
    <circle cx="82" cy="62" r="4" fill="#bbb" opacity="0.2"/>
    <circle cx="70" cy="63" r="3" fill="#bbb" opacity="0.2"/>
</svg>
@endsection
