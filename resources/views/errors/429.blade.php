@extends('errors.layout')

@section('code', '429')
@section('title', 'Too Many Requests')

@section('message')
    You've made too many requests in a short period. Please wait a moment and try again.
@endsection

@section('illustration')
<svg viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
    <circle cx="100" cy="100" r="80" fill="#f3e8fb"/>
    <circle cx="100" cy="100" r="60" fill="#ede4f7"/>
    <rect x="72" y="80" width="56" height="40" rx="6" fill="#662c87" opacity="0.9"/>
    <rect x="80" y="88" width="40" height="24" rx="3" fill="#fff"/>
    <line x1="88" y1="96" x2="96" y2="96" stroke="#662c87" stroke-width="2" stroke-linecap="round"/>
    <line x1="100" y1="96" x2="108" y2="96" stroke="#662c87" stroke-width="2" stroke-linecap="round"/>
    <line x1="112" y1="96" x2="114" y2="96" stroke="#662c87" stroke-width="2" stroke-linecap="round"/>
    <line x1="88" y1="103" x2="92" y2="103" stroke="#662c87" stroke-width="2" stroke-linecap="round"/>
    <line x1="96" y1="103" x2="108" y2="103" stroke="#662c87" stroke-width="2" stroke-linecap="round"/>
    <path d="M130 72 L135 62 M140 78 L148 72 M142 88 L150 88" stroke="#e74c3c" stroke-width="2" stroke-linecap="round" opacity="0.6"/>
</svg>
@endsection
