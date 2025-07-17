@extends('layouts.app')

@section('content')
<div class="container mt-2">
        <div class="col-md-12">
            <div class="row">
                <div class="container mt-2">
                    <div class="col-md-12">
                        <div class="row">

                            <div class="col-md-4 mb-4">
                                <div class="card shadow-sm rounded p-3 d-flex flex-row align-items-center justify-content-between">
                                    <div>
                                        <div class="text-muted">Total Letters</div>
                                        <div class="h5 mb-0 font-weight-bold text-dark">{{ $total }}</div>
                                    </div>
                                    <div class="bg-dark text-white rounded-circle p-2">
                                        <i class="fas fa-file-alt"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 mb-4">
                                <div class="card shadow-sm rounded p-3 d-flex flex-row align-items-center justify-content-between">
                                    <div>
                                        <div class="text-muted">Total Received</div>
                                        <div class="h5 mb-0 font-weight-bold text-dark">{{ $delivered }}</div>
                                    </div>
                                    <div class="bg-success text-white rounded-circle p-2">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 mb-4">
                                <div class="card shadow-sm rounded p-3 d-flex flex-row align-items-center justify-content-between">
                                    <div>
                                        <div class="text-muted">Pending to Receive</div>
                                        <div class="h5 mb-0 font-weight-bold text-dark">{{ $pending }}</div>
                                    </div>
                                    <div class="bg-warning text-white rounded-circle p-2">
                                        <i class="fas fa-file-contract"></i>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>  
                <div class="card mt-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Letter List</h5>
                        {{-- <a href="{{ route('home', ['status' => 'Delivered']) }}" class="btn btn-outline-dark btn-sm">
                            Show Only Delivered
                        </a> --}}
                        @if(request('status') === 'Delivered')
                            <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                                Show All
                            </a>
                        @else
                            <a href="{{ route('home', ['status' => 'Delivered']) }}" class="btn btn-outline-dark">
                                Show Only Delivered
                            </a>
                        @endif
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th>Letter ID</th>
                                    <th>Subject/Document Name</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($letters as $letter)
                                    <tr>
                                        <td>{{ $letter->letter_id }}</td>
                                        <td>{{ ucwords($letter->subject ?? 'N/A') }}</td>
                                        <td>
                                            <span class="badge {{ $letter->status == 'Delivered' ? 'bg-success' : 'bg-warning text-dark' }}">
                                                {{ $letter->status }}
                                            </span>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($letter->created_at)->format('d-m-Y') }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#letterViewModal-{{ $letter->id }}">
                                                <i class="fa fa-eye"></i>
                                            </button>
                                            <a href="{{ route('admin.delivery.report', $letter->id) }}" class="btn btn-outline-dark btn-sm" target="_blank">
                                                <i class="fa fa-download"></i> PDF
                                            </a>
                                        </td>
                                    </tr>

                                @empty
                                    <tr><td colspan="5" class="text-center">No letters found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="modal fade" id="letterViewModal-{{ $letter->id }}" tabindex="-1" aria-labelledby="letterModalLabel-{{ $letter->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content p-4">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="modal-title fw-bold" id="letterModalLabel-{{ $letter->id }}">Letter Details</h5>
                                        <a href="{{ route('admin.delivery.download', $letter->id) }}" class="btn btn-outline-dark btn-sm" target="_blank">
                                            <i class="fa fa-download"></i> Download Report
                                        </a>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-2"><strong>ID:</strong> {{ $letter->letter_id }}</div>
                                        <div class="col-md-6 mb-2 text-end">
                                            <strong>Status:</strong>
                                            <span class="badge {{ $letter->status == 'delivered' ? 'bg-dark' : 'bg-warning text-dark' }}">
                                                {{ $letter->status }}
                                            </span>
                                        </div>
                                    </div>

                                    <hr>

                                    <div class="row">
                                        <div class="col-md-6 mb-2"><strong>Received From:</strong> {{ ucwords($letter->received_from) }}</div>
                                        <div class="col-md-6 mb-2"><strong>Send To:</strong>
                                            @php
                                                $sendTo = $letter->send_to;
                                                $sendToName = '';

                                                if(Str::startsWith($sendTo, 'member_')) {
                                                    $memberId = Str::after($sendTo, 'member_');
                                                    $member = \App\Models\User::find($memberId);
                                                    $sendToName = $member ? ucwords($member->name) . ' (Member)' : 'Unknown Member';
                                                } elseif(Str::startsWith($sendTo, 'team_')) {
                                                    $teamId = Str::after($sendTo, 'team_');
                                                    $team = \App\Models\Team::find($teamId);
                                                    $sendToName = $team ? ucwords($team->name)  : 'Unknown Team';
                                                }
                                            @endphp

                                            {{ $sendToName }} 
                                        </div>
                                        <div class="col-md-6 mb-2"><strong>Document Reference No:</strong> {{ $letter->document_reference_no ?? 'N/A' }}</div>
                                        <div class="col-md-6 mb-2"><strong>Document Date:</strong> {{ \Carbon\Carbon::parse($letter->created_at)->format('d-m-y') }}</div>
                                        <div class="col-md-6 mb-2"><strong>Subject/Document Name:</strong> {{ ucwords($letter->subject ?? 'N/A') }}</div>
                                        <div class="col-md-6 mb-2"><strong>Handed Over By:</strong> {{ ucwords(optional($letter->handedOverByUser)->name ?? 'Unassigned') }}</div>
                                        <div class="col-md-6 mb-2"><strong>Delivered Date:</strong>
                                            {{ $letter->delivery ? \Carbon\Carbon::parse($letter->delivery->delivered_at)->format('d-m-y') : 'Not Delivered' }}
                                        </div>
                                    </div>
                                    @if ($letter->status === 'Delivered' && $letter->delivery && $letter->delivery->signature_image_path)
                                        <hr>
                                        <div class="text-center">
                                            <h6 class="fw-bold mb-2">Signature</h6>
                                            <img src="{{ asset($letter->delivery->signature_image_path) }}" alt="Signature" class="img-fluid border rounded shadow-sm" style="max-width: 300px;">
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>
@endsection