@extends('errors.layout')

@section('code', '400')
@section('title', 'Bad Request')

@section('message')
    The server couldn't understand that request. Please check the URL or form data and try again.
@endsection

@section('illustration')
<svg viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
    <circle cx="100" cy="100" r="80" fill="#f3e8fb"/>
    <circle cx="100" cy="100" r="60" fill="#ede4f7"/>
    <rect x="70" y="75" width="60" height="50" rx="8" fill="#662c87" opacity="0.9"/>
    <rect x="78" y="83" width="44" height="34" rx="4" fill="#fff"/>
    <line x1="85" y1="93" x2="115" y2="93" stroke="#662c87" stroke-width="2" stroke-linecap="round" opacity="0.3"/>
    <line x1="85" y1="100" x2="108" y2="100" stroke="#662c87" stroke-width="2" stroke-linecap="round" opacity="0.3"/>
    <line x1="85" y1="107" x2="100" y2="107" stroke="#662c87" stroke-width="2" stroke-linecap="round" opacity="0.3"/>
    <circle cx="140" cy="70" r="18" fill="#e74c3c" opacity="0.9"/>
    <line x1="133" y1="63" x2="147" y2="77" stroke="#fff" stroke-width="3" stroke-linecap="round"/>
    <line x1="147" y1="63" x2="133" y2="77" stroke="#fff" stroke-width="3" stroke-linecap="round"/>
</svg>
@endsection
