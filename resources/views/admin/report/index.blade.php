@extends('layouts.app')
@section('content')

<style>
    .peon-row {
        display: flex;
        justify-content: space-between;
        padding: 1rem;
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        margin-top: 1rem;
    }

    .peon-info .peon-name {
        font-weight: bold;
        font-size: 1.1rem;
    }

    .peon-metrics {
        display: flex;
        gap: 1.5rem;
        align-items: center;
    }

    .metric-value {
        font-size: 1.2rem;
        font-weight: bold;
        text-align: center;
    }

    .metric-label {
        font-size: 0.85rem;
        text-align: center;
        color: #666;
    }

</style>

<div class="container mt-2">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>System Reports</h5>
                </div>
                <form method="GET" action="{{ route('admin.report.index') }}">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="filterPeriod" class="form-label">Time Period</label>
                            <select class="form-select" id="filterPeriod" name="period">
                                <option value="">All Time</option>
                                <option value="today" {{ request('period') == 'today' ? 'selected' : '' }}>Today</option>
                                <option value="week" {{ request('period') == 'week' ? 'selected' : '' }}>This Week</option>
                                <option value="month" {{ request('period') == 'month' ? 'selected' : '' }}>This Month</option>
                            </select>
                        </div>

                        @if(Auth::user()->role === 'super admin')
                        <div class="col-md-3">
                            <label for="filterUser" class="form-label">User Filter</label>
                            <select class="form-select" id="filterUser" name="user_id">
                                <option value="">All Users</option>
                                @foreach($users as $u)
                                    <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>
                                        {{ $u->name }} ({{ $u->role }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @endif

                    
                        @if(Auth::user()->role === 'Receptionist')
                        <div class="col-md-3">
                            <label for="filterStatus" class="form-label">Status</label>
                            <select class="form-select" id="filterStatus" name="status">
                                <option value="">All</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                            </select>
                        </div>
                        @endif

                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">Filter</button>
                            <a href="{{ route('admin.report.index') }}" class="btn btn-outline-secondary">Reset</a>
                        </div>
                    </div>
                </form>

                <div id="user-performance-container">
                    @if(!empty($performanceData))
                        <div class="peon-row">
                            <div class="peon-info">
                                <div class="peon-name">{{ $performanceData['name'] }}</div>
                                <div class="peon-role">{{ ucfirst($performanceData['role']) }}</div>
                            </div>
                            <div class="peon-metrics">
                                <div>
                                    <div class="metric-value">{{ $performanceData['assigned'] }}</div>
                                    <div class="metric-label">Assigned</div>
                                </div>
                                <div>
                                    <div class="metric-value">{{ $performanceData['delivered'] }}</div>
                                    <div class="metric-label">Delivered</div>
                                </div>
                                <div>
                                    <div class="metric-value rate-value">{{ $performanceData['rate'] }}%</div>
                                    <div class="metric-label">Rate</div>
                                </div>
                            </div>
                        </div>
                    @else
                        <p class="text-muted">No performance data available.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>


@endsection

@section('script')

<script>

    document.querySelectorAll('.form-select, #filterDate').forEach(el => {
        el.addEventListener('change', () => el.form.submit());
    });
</script>

@endsection