@extends('errors.layout')

@section('code', '403')
@section('title', 'Access Denied')

@section('message')
    You don't have permission to access this page. If you believe this is an error, please contact your administrator.
@endsection

@section('illustration')
<svg viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
    <circle cx="100" cy="100" r="80" fill="#f3e8fb"/>
    <circle cx="100" cy="100" r="60" fill="#ede4f7"/>
    <rect x="80" y="90" width="40" height="35" rx="4" fill="#662c87" opacity="0.9"/>
    <rect x="75" y="85" width="50" height="10" rx="5" fill="#8e44ad"/>
    <circle cx="100" cy="105" r="5" fill="#fff"/>
    <rect x="98" y="108" width="4" height="8" rx="2" fill="#fff"/>
    <path d="M88 85 V75 a12 12 0 0 1 24 0 V85" stroke="#662c87" stroke-width="4" fill="none" stroke-linecap="round"/>
    <circle cx="145" cy="65" r="16" fill="#e67e22" opacity="0.9"/>
    <rect x="143" y="56" width="4" height="12" rx="2" fill="#fff"/>
    <circle cx="145" cy="73" r="2.5" fill="#fff"/>
</svg>
@endsection
