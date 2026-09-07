@extends('layouts.admin')
@section('title', 'Campaign Pitches — Alishe Nails Admin')

@section('content')

    <div class="admin-page-head">
        <div>
            <h1>Campaign Pitches</h1>
            <p>{{ $pitches->total() }} pitches — creator &amp; influencer campaign requests from /work-with-us.</p>
        </div>
    </div>

    <div class="admin-card">
        <form method="GET" action="{{ route('admin.campaign-pitches.index') }}" style="margin-bottom:20px;">
            <select class="select-sort" name="status" onchange="this.form.requestSubmit()">
                <option value="">All Pitches</option>
                @foreach (\App\Models\CampaignPitch::STATUS_LABELS as $value => $label)
                    <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </form>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Name / Brand</th>
                    <th>Handle</th>
                    <th>Followers</th>
                    <th>Campaign</th>
                    <th>Budget</th>
                    <th>Message</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pitches as $pitch)
                    <tr>
                        <td>{{ $pitch->name }}</td>
                        <td>{{ $pitch->handle }}</td>
                        <td>{{ $pitch->followerCountLabel() }}</td>
                        <td>{{ $pitch->campaignTypeLabel() }}</td>
                        <td>{{ $pitch->budgetRangeLabel() }}</td>
                        <td style="max-width:220px;">
                            {{ \Illuminate\Support\Str::limit($pitch->message, 80) }}
                            @if ($pitch->portfolio_links)
                                <div style="font-size:.78rem;color:rgba(43,29,29,.6);">Portfolio: {{ \Illuminate\Support\Str::limit($pitch->portfolio_links, 60) }}</div>
                            @endif
                        </td>
                        <td>
                            <span class="status-pill {{ match ($pitch->status) {
                                'contacted' => 'status-processing',
                                'closed' => 'status-completed',
                                default => 'status-pending',
                            } }}">
                                {{ $pitch->statusLabel() }}
                            </span>
                        </td>
                        <td style="display:flex;gap:8px;">
                            @if ($pitch->status !== 'new')
                                <form action="{{ route('admin.campaign-pitches.status', $pitch) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="new">
                                    <button type="submit" class="btn btn-outline btn-sm">New</button>
                                </form>
                            @endif
                            @if ($pitch->status !== 'contacted')
                                <form action="{{ route('admin.campaign-pitches.status', $pitch) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="contacted">
                                    <button type="submit" class="btn btn-outline btn-sm">Contacted</button>
                                </form>
                            @endif
                            @if ($pitch->status !== 'closed')
                                <form action="{{ route('admin.campaign-pitches.status', $pitch) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="closed">
                                    <button type="submit" class="btn btn-outline btn-sm">Closed</button>
                                </form>
                            @endif
                            <form action="{{ route('admin.campaign-pitches.destroy', $pitch) }}" method="POST" onsubmit="return confirm('Delete this pitch?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm" style="background:#B3261E;color:#fff;border:none;">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" style="text-align:center;padding:30px;">No campaign pitches yet.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination" style="justify-content:flex-start;margin-top:20px;">
            {{ $pitches->links() }}
        </div>
    </div>
@endsection