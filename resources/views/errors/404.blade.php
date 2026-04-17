@extends('errors.layout')

@section('code', '404')
@section('title', 'Page Not Found')

@section('message')
    The page you're looking for doesn't exist or has been moved. Let's get you back on track.
@endsection

@section('illustration')
<svg viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
    <circle cx="100" cy="100" r="80" fill="#f3e8fb"/>
    <circle cx="100" cy="100" r="60" fill="#ede4f7"/>
    <circle cx="100" cy="90" r="30" fill="#662c87" opacity="0.15"/>
    <path d="M80 105 Q100 75 120 105" stroke="#662c87" stroke-width="3" fill="none" stroke-linecap="round"/>
    <circle cx="88" cy="88" r="4" fill="#662c87"/>
    <circle cx="112" cy="88" r="4" fill="#662c87"/>
    <path d="M92 100 Q100 96 108 100" stroke="#662c87" stroke-width="2.5" fill="none" stroke-linecap="round"/>
    <path d="M60 130 Q100 118 140 130" stroke="#8e44ad" stroke-width="2" fill="none" stroke-dasharray="4 4" opacity="0.5"/>
    <circle cx="55" cy="132" r="4" fill="#8e44ad" opacity="0.6"/>
    <circle cx="145" cy="132" r="4" fill="#8e44ad" opacity="0.6"/>
</svg>
@endsection
