@extends('errors.layout')

@section('code', '419')
@section('title', 'Session Expired')

@section('message')
    Your session has expired. Please refresh the page and try again.
@endsection

@section('illustration')
<svg viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
    <circle cx="100" cy="100" r="80" fill="#f3e8fb"/>
    <circle cx="100" cy="100" r="60" fill="#ede4f7"/>
    <circle cx="100" cy="95" r="30" stroke="#662c87" stroke-width="4" fill="none"/>
    <line x1="100" y1="78" x2="100" y2="95" stroke="#662c87" stroke-width="3" stroke-linecap="round"/>
    <line x1="100" y1="95" x2="112" y2="102" stroke="#8e44ad" stroke-width="3" stroke-linecap="round"/>
    <circle cx="100" cy="95" r="3" fill="#662c87"/>
    <path d="M120 65 L125 55 L130 65" stroke="#e67e22" stroke-width="2" fill="none" stroke-linecap="round" opacity="0.7"/>
    <path d="M70 65 L75 55 L80 65" stroke="#e67e22" stroke-width="2" fill="none" stroke-linecap="round" opacity="0.7"/>
</svg>
@endsection
