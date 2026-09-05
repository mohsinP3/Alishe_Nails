@extends('layouts.admin')

@section('title', 'Reviews — Alishe Nails Admin')

@section('content')

    <div class="admin-page-head">
        <div>
            <h1>Reviews</h1>
            <p>{{ $reviews->total() }} reviews — moderate what shows publicly.</p>
        </div>
    </div>

    <div class="admin-card">
        <form method="GET" action="{{ route('admin.reviews.index') }}" style="margin-bottom:20px;">
            <select class="select-sort" name="status" onchange="this.form.requestSubmit()">
                <option value="">All Reviews</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending Approval</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
            </select>
        </form>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Customer</th>
                    <th>Rating</th>
                    <th>Comment</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($reviews as $review)
                    <tr>
                        <td>{{ $review->product?->name ?? '—' }}</td>
                        <td>
                            {{ $review->customer_name }}
                            @if ($review->is_verified_purchase)
                                <span class="status-pill status-completed" style="margin-left:6px;">Verified</span>
                            @endif
                        </td>
                        <td>
                            @for ($i = 1; $i <= 5; $i++)
                                <i class="fa-{{ $i <= $review->rating ? 'solid' : 'regular' }} fa-star" style="color:var(--gold);font-size:.8rem;"></i>
                            @endfor
                        </td>
                        <td style="max-width:280px;">{{ \Illuminate\Support\Str::limit($review->comment, 80) }}</td>
                        <td>
                            <span class="status-pill {{ $review->is_approved ? 'status-completed' : 'status-pending' }}">
                                {{ $review->is_approved ? 'Approved' : 'Pending' }}
                            </span>
                        </td>
                        <td style="display:flex;gap:8px;">
                            @if (! $review->is_approved)
                                <form action="{{ route('admin.reviews.approve', $review) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-outline btn-sm">Approve</button>
                                </form>
                            @else
                                <form action="{{ route('admin.reviews.reject', $review) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-outline btn-sm">Hide</button>
                                </form>
                            @endif
                            <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" onsubmit="return confirm('Delete this review?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm" style="background:#B3261E;color:#fff;border:none;">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" style="text-align:center;padding:30px;">No reviews yet.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination" style="justify-content:flex-start;margin-top:20px;">
            {{ $reviews->links() }}
        </div>
    </div>
@endsection
