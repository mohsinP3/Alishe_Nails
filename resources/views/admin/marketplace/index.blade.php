@extends('layouts.admin')
@section('title', 'Marketplace — Alishe Nails Admin')
@section('content')
<div class="admin-page-head"><div><h1>Marketplace</h1><p>Seller marketplace billing is now managed through subscriptions.</p></div><a href="{{ route('admin.subscriptions.index') }}" class="btn btn-primary">Manage Subscriptions</a></div>
@endsection
