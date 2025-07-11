@extends('layouts.app')

@section('content')
<div class="container mt-2">
        <div class="col-md-12">
            <div class="row">

                <div class="col-md-3 mb-4">
                    <div class="card shadow-sm rounded p-3 d-flex flex-row align-items-center justify-content-between">
                        <div>
                            <div class="text-muted">Today's Letters</div>
                            <div class="h5 mb-0 font-weight-bold text-dark">{{ $todayLetters }}</div>
                        </div>
                        <div class="bg-dark text-white rounded-circle p-2">
                            <i class="fas fa-file-alt"></i>
                        </div>
                    </div>
                </div>

              
                <div class="col-md-3 mb-4">
                    <div class="card shadow-sm rounded p-3 d-flex flex-row align-items-center justify-content-between">
                        <div>
                            <div class="text-muted">Total Letters</div>
                            <div class="h5 mb-0 font-weight-bold text-dark">{{ $totalLetters }}</div>
                        </div>
                        <div class="bg-success text-white rounded-circle p-2">
                            <i class="fas fa-file"></i>
                        </div>
                    </div>
                </div>

                
                <div class="col-md-3 mb-4">
                    <div class="card shadow-sm rounded p-3 d-flex flex-row align-items-center justify-content-between">
                        <div>
                            <div class="text-muted">Total Delivered</div>
                            <div class="h5 mb-0 font-weight-bold text-dark">{{ $totalDelivered }}</div>
                        </div>
                        <div class="bg-success text-white rounded-circle p-2">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>

                
                @php $role = Auth::user()->role; @endphp

                @if($role === 'super admin')
                    <div class="col-md-3 mb-4">
                        <div class="card shadow-sm rounded p-3 d-flex flex-row align-items-center justify-content-between">
                            <div>
                                <div class="text-muted">System Users</div>
                                <div class="h5 mb-0 font-weight-bold text-dark">{{ $systemUsers }}</div>
                            </div>
                            <div class="bg-warning text-white rounded-circle p-2">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="card shadow-sm rounded p-3 d-flex flex-row align-items-center justify-content-between">
                            <div>
                                <div class="text-muted">Total Team</div>
                                <div class="h5 mb-0 font-weight-bold text-dark">{{ $totalTeam }}</div>
                            </div>
                            <div class="bg-warning text-white rounded-circle p-2">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
</div>

@endsection
