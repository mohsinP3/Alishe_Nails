@extends('layouts.app')
@section('title', 'Unsubscribed — Alishe Nails')

@section('content')
    <div class="container" style="max-width:480px;text-align:center;padding-block:100px;">
        <i class="fa-solid fa-circle-check" style="font-size:2.5rem;color:var(--rose);"></i>
        <h2 style="margin-top:16px;">You've been unsubscribed</h2>
        <p>You won't receive any more newsletter emails from Alishe Nails.</p>
        <a href="{{ route('home') }}" class="btn btn-primary">Back to Home</a>
    </div>
@endsection
