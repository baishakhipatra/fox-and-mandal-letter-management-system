@extends('layouts.app')

@section('content')



<div class="container mt-2">

    <div class="row">

        <div class="col-md-12">

            <div class="card">

                <div class="card-header d-flex justify-content-between align-items-center">

                    <h3>Delivery Management</h3>

                </div>


            @if(Auth::user()->role === 'Peon' || Auth::user()->role === 'Receptionist')

                {{-- Pending Deliveries --}}

                <div class="mb-4">

                    <div class="card-body">

                        <div class="card-header">

                            <h5>Pending Deliveries <span class="badge bg-secondary">{{ $letters->where('status', 'Pending')->count() }}</span></h5>

                        </div>

                        <div class="table-responsive">

                            <table class="table">

                                <thead>

                                    <tr>

                                        <th>ID</th>

                                        <th>Handed Over By</th>

                                        <th>Subject/Document Name</th>

                                        <th>Send To</th>

                                        <th>Document Date</th>

                                        <th>Status</th>

                                        <th>Actions</th>

                                    </tr>

                                </thead>

                                <tbody>

                                    @forelse($letters->where('status', 'Pending') as $letter)

                                        <tr>

                                            <td>{{ $letter->letter_id }}</td>

                                            <td>{{ ucwords(optional($letter->handedOverByUser)->name) ?? '-' }}</td>

                                            <td>{{ ucwords($letter->subject) }}</td>

                                            @php
                                                $sendTo = $letter->send_to;
                                                $sendToName = 'Not specified';

                                                if (\Illuminate\Support\Str::startsWith($sendTo, 'member_')) {
                                                    $memberId = \Illuminate\Support\Str::after($sendTo, 'member_');
                                                    $member = \App\Models\User::find($memberId);
                                                    $sendToName = $member ? ucwords($member->name) . ' (Member)' : 'Unknown Member';
                                                } elseif (\Illuminate\Support\Str::startsWith($sendTo, 'team_')) {
                                                    $teamId = \Illuminate\Support\Str::after($sendTo, 'team_');
                                                    $team = \App\Models\Team::find($teamId);
                                                    $sendToName = $team ? ucwords($team->name) . ' (Team)' : 'Unknown Team';
                                                }
                                            @endphp
                                            <td>{{ $sendToName }}</td>


                                            <td>{{ \Carbon\Carbon::parse($letter->created_at)->format('m/d/Y') }}</td>

                                            <td><span class="badge bg-warning text-dark">Pending</span></td>

                                            <td>

                                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#letterViewModal-{{ $letter->id }}" data-bs-toggle="tooltip" data-bs-title="View">

                                                    <i class="fa fa-eye"></i>

                                                </button>

                                                @php
                                                    $disableButton = is_null($letter->handedOverByUser) || empty($letter->send_to);
                                                @endphp

                                                <button 
                                                    class="btn btn-sm btn-success deliverBtn" 
                                                    data-id="{{ $letter->id }}" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#confirmDeliveryModal" 
                                                    {{ $disableButton ? 'disabled' : '' }}>
                                                    Deliver Now
                                                </button>


                                            </td>

                                        </tr>



                                        {{-- View Modal --}}

                                        <div class="modal fade" id="letterViewModal-{{ $letter->id }}" tabindex="-1" aria-labelledby="letterModalLabel-{{ $letter->id }}" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                                <div class="modal-content p-4 rounded-3 shadow-sm">

                                                    <!-- Header -->
                                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                                        <h5 class="modal-title fw-bold" id="letterModalLabel-{{ $letter->id }}">Letter Details</h5>
                                                        <a href="{{ route('admin.delivery.download', $letter->id) }}" class="btn btn-outline-dark btn-sm" target="_blank">
                                                            <i class="fa fa-download"></i> Download Report
                                                        </a>
                                                    </div>

                                                    <!-- Letter ID and Status -->
                                                    <div class="row mb-3">
                                                        <div class="col-md-6"><strong>ID:</strong> {{ $letter->letter_id }}</div>
                                                        <div class="col-md-6 text-md-end mt-2 mt-md-0">
                                                            <strong>Status:</strong>
                                                            <span class="badge {{ $letter->status == 'Delivered' ? 'bg-dark' : 'bg-warning text-dark' }}">
                                                                {{ $letter->status }}
                                                            </span>
                                                        </div>
                                                    </div>

                                                    <hr>

                                                    <!-- Letter Information -->
                                                    <div class="row gy-2">
                                                        <div class="col-md-6"><strong>Received From:</strong> {{ ucwords($letter->received_from) }}</div>
                                                        
                                                        <div class="col-md-6"><strong>Send To:</strong>
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
                                                                    $sendToName = $team ? ucwords($team->name) : 'Unknown Team';
                                                                }
                                                            @endphp
                                                            {{ $sendToName }}
                                                        </div>
                                                        <div class="col-md-6"><strong>Document Reference No:</strong> {{ $letter->document_reference_no ?? 'N/A' }}</div>
                                                        <div class="col-md-6"><strong>Document Date:</strong> {{ \Carbon\Carbon::parse($letter->created_at)->format('d-m-y') }}</div>
                                                        <div class="col-md-6"><strong>subject/Document Name:</strong> {{ ucwords($letter->subject ?? 'N/A') }}</div>


                                                        <div class="col-md-6"><strong>Handed Over By:</strong> {{ ucwords(optional($letter->handedOverByUser)->name ?? 'Unassigned') }}</div>

                                                        <div class="col-md-6"><strong>Delivered Date:</strong>
                                                            {{ $letter->delivery ? \Carbon\Carbon::parse($letter->delivery->delivered_at)->format('d-m-y') : 'Not Delivered' }}
                                                        </div>
                                                    </div>

                                                    <!-- Signature -->
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


                                    @empty

                                        <tr><td colspan="7">No pending deliveries.</td></tr>

                                    @endforelse

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>



                {{-- Delivered Letters --}}

                <div class="">

                    <div class="card-body">

                        <div class="card-header">

                            <h5>Delivered Letters <span class="badge bg-dark">{{ $letters->where('status', 'Delivered')->count() }}</span></h5>

                        </div>



                        <div class="table-responsive">

                            <table class="table">

                                <thead>

                                    <tr>

                                        <th>ID</th>

                                        <th>Handed Over By</th>

                                        <th>Subject/Document Name</th>

                                        <th>Send To</th>

                                        <th>Delivered Date</th>

                                        <th>Delivered To</th>

                                        <th>Actions</th>

                                    </tr>

                                </thead>

                                <tbody>

                                    @forelse($letters->where('status', 'Delivered') as $letter)

                                        <tr>

                                            <td>{{ $letter->letter_id }}</td>

                                            <td>{{ ucwords($letter->handedOverByUser->name) ?? '-' }}</td>

                                            <td>{{ ucwords($letter->subject) }}</td>

                                            {{-- <td>{{ optional(optional($letter->delivery)->deliveredToUser)->name ? ucwords(optional($letter->delivery)->deliveredToUser->name) : '-' }}</td> --}}
                                            @php
                                                $sendTo = $letter->send_to;
                                                $sendToName = 'Not specified';

                                                if (Str::startsWith($sendTo, 'member_')) {
                                                    $memberId = Str::after($sendTo, 'member_');
                                                    $member = \App\Models\User::find($memberId);
                                                    $sendToName = $member ? ucwords($member->name) . ' (Member)' : 'Unknown Member';
                                                } elseif (Str::startsWith($sendTo, 'team_')) {
                                                    $teamId = Str::after($sendTo, 'team_');
                                                    $team = \App\Models\Team::find($teamId);
                                                    $sendToName = $team ? ucwords($team->name) . ' (Team)' : 'Unknown Team';
                                                }
                                            @endphp

                                            <td>{{ $sendToName }}</td>


                                            <td>

                                                {{ $letter->delivery ? \Carbon\Carbon::parse($letter->delivery->delivered_at)->format('m/d/Y') : '-' }}

                                            </td>

                                            {{-- <td>{{ ucwords($letter->delivery->deliveredToUser->name) ?? '-' }}</td> --}}
                                            <td>{{ optional(optional($letter->delivery)->deliveredToUser)->name ? ucwords(optional($letter->delivery)->deliveredToUser->name) : '-' }}</td>

                                            <td>

                                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#letterViewModal-{{ $letter->id }}" data-bs-toggle="tooltip" data-bs-title="View">

                                                    <i class="fa fa-eye"></i>

                                                </button>

                                                <a href="{{ route('admin.delivery.report', $letter->id) }}" class="btn btn-outline-dark btn-sm" target="_blank">

                                                    <i class="fa fa-download"></i> PDF

                                                </a>

                                            </td>

                                        </tr>
                                        <div class="modal fade" id="letterViewModal-{{ $letter->id }}" tabindex="-1" aria-labelledby="letterModalLabel-{{ $letter->id }}" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                                <div class="modal-content p-4 rounded-3 shadow-sm">

                                                    <!-- Header -->
                                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                                        <h5 class="modal-title fw-bold" id="letterModalLabel-{{ $letter->id }}">Letter Details</h5>
                                                        <a href="{{ route('admin.delivery.download', $letter->id) }}" class="btn btn-outline-dark btn-sm" target="_blank">
                                                            <i class="fa fa-download"></i> Download Report
                                                        </a>
                                                    </div>

                                                    <!-- ID and Status -->
                                                    <div class="row mb-3">
                                                        <div class="col-md-6"><strong>ID:</strong> {{ $letter->letter_id }}</div>
                                                        <div class="col-md-6 text-md-end mt-2 mt-md-0">
                                                            <strong>Status:</strong>
                                                            <span class="badge {{ $letter->status == 'Delivered' ? 'bg-dark' : 'bg-warning text-dark' }}">
                                                                {{ $letter->status }}
                                                            </span>
                                                        </div>
                                                    </div>

                                                    <hr>

                                                    <!-- Letter Information -->
                                                    <div class="row gy-2">
                                                        <div class="col-md-6"><strong>Received From:</strong> {{ ucwords($letter->received_from) }}</div>
                                                        <div class="col-md-6"><strong>Send To:</strong>
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
                                                                    $sendToName = $team ? ucwords($team->name) : 'Unknown Team';
                                                                }
                                                            @endphp
                                                            {{ $sendToName }}
                                                        </div>
                                                        <div class="col-md-6"><strong>Document Reference No:</strong> {{ $letter->document_reference_no ?? 'N/A' }}</div>
                                                        <div class="col-md-6"><strong>Document Date:</strong> {{ \Carbon\Carbon::parse($letter->created_at)->format('d-m-y') }}</div>
                                                        <div class="col-md-6"><strong>Subject/Document Name:</strong> {{ ucwords($letter->subject ?? 'N/A') }}</div>

                                                        

                                                        <div class="col-md-6"><strong>Handed Over By:</strong> {{ ucwords(optional($letter->handedOverByUser)->name ?? 'Unassigned') }}</div>

                                                       
                                                        

                                                        <div class="col-md-6"><strong>Delivered To:</strong> 
                                                            {{ optional(optional($letter->delivery)->deliveredToUser)->name ? ucwords(optional($letter->delivery)->deliveredToUser->name) : '-' }}
                                                        </div>

                                                        <div class="col-md-6"><strong>Delivered Date:</strong>
                                                            {{ $letter->delivery ? \Carbon\Carbon::parse($letter->delivery->delivered_at)->format('d-m-y') : 'Not Delivered' }}
                                                        </div>
                                                    </div>

                                                    <!-- Signature -->
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
                                    @empty

                                        <tr><td colspan="7">No delivered letters.</td></tr>

                                    @endforelse

                                </tbody>

                            </table>
                            {{-- <div class="modal fade" id="letterViewModal-{{ $letter->id }}" tabindex="-1" aria-labelledby="letterModalLabel-{{ $letter->id }}" aria-hidden="true">

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

                                                <span class="badge {{ $letter->status == 'Delivered' ? 'bg-dark' : 'bg-warning text-dark' }}">

                                                    {{ $letter->status }}

                                                </span>

                                            </div>

                                        </div>



                                        <hr>



                                        <div class="row">

                                            <div class="col-md-6 mb-2"><strong>Received From:</strong> {{ ucwords($letter->received_from) }}</div>

                                            <div class="col-md-6 mb-2"><strong>Subject/Document Name:</strong> {{ ucwords($letter->subject ?? 'N/A') }}</div>

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

                                            <div class="col-md-6 mb-2"><strong>Handed Over By:</strong> {{ ucwords(optional($letter->handedOverByUser)->name ?? 'Unassigned') }}</div>

                                            
                                            <div class="col-md-6 mb-2"><strong>Reference No:</strong> {{ $letter->document_reference_no ?? 'N/A' }}</div>

                                            <div class="col-md-6 mb-2"><strong>Document Date:</strong> {{ \Carbon\Carbon::parse($letter->created_at)->format('m/d/Y') }}</div>

                                            <div class="col-md-6 mb-2"><strong>Delivered To:</strong> {{ optional(optional($letter->delivery)->deliveredToUser)->name ? ucwords(optional($letter->delivery)->deliveredToUser->name) : '-' }}

                                            <div class="col-md-6 mb-2"><strong>Delivered Date:</strong>

                                                {{ $letter->delivery ? \Carbon\Carbon::parse($letter->delivery->delivered_at)->format('m/d/Y') : 'Not Delivered' }}

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

                            </div> --}}
                        </div>
                    </div>

                </div>



                {{-- Confirm Delivery Modal --}}

                <div class="modal fade" id="confirmDeliveryModal" tabindex="-1" aria-labelledby="confirmDeliveryModalLabel" aria-hidden="true">

                    <div class="modal-dialog">

                        <div class="modal-content">

                            <form id="confirmDeliveryForm" method="POST" action="javascript:void(0)">

                                @csrf

                                <div class="modal-header">

                                    <h5 class="modal-title">Confirm Delivery</h5>

                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>

                                </div>

                                <div class="modal-body">

                                    <input type="hidden" name="letter_id" id="deliveryLetterId">



                                    <div class="mb-3">

                                        <label for="memberSelect" class="form-label">Select Member to Deliver To</label>

                                        {{-- <select class="form-select" id="memberSelect" name="delivered_to_user_id" required>

                                            <option value="">Select a member</option>

                                            @foreach($members as $member)

                                                <option value="{{ $member->id }}">{{ ucwords($member->name) }}

                                                    @if($member->team->isNotEmpty())

                                                        ({{ $member->team->pluck('name')->join(', ') }})

                                                    @endif

                                                </option>

                                            @endforeach

                                        </select> --}}
                                        <select class="form-select" id="memberSelect" name="delivered_to_user_id" required>
                                            <option value="">Select a member</option>
                                        </select>

                                        <div class="invalid-feedback">Please select a member.</div>

                                    </div>



                                    <div class="mb-3">

                                        <label for="signaturePadCanvas" class="form-label">Signature</label>

                                        <div class="border p-2 rounded">

                                            <canvas id="signaturePadCanvas" class="w-100" height="200"></canvas>

                                        </div>

                                        <button type="button" class="btn btn-sm btn-secondary mt-2" id="clearSignature">Clear</button>

                                        <div class="invalid-feedback d-block" id="signatureError" style="display:none;">

                                            Please provide a signature.

                                        </div>

                                    </div>

                                </div>

                                <div class="modal-footer">

                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>

                                    <button type="button" class="btn btn-dark" id="confirmDeliveryBtn">Confirm Delivery</button>

                                </div>

                            </form>

                        </div>

                    </div>

                </div>



            @else

                <div class="card-body">

                    <form method="GET" action="{{ route('admin.delivery.index') }}"> 

                        <div class="row">

                            <div class="col-md-3">

                                <label for="filterStatus" class="form-label">Filter by Status</label>

                                <select class="form-select" id="filterStatus" name="status">

                                    <option value="">All Status</option>

                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>

                                    <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>

                                </select>

                            </div>





                            <div class="col-md-3">

                                <label for="filterPeon" class="form-label">Filter by User</label>

                                <select class="form-select" id="filterPeon" name="user_id">

                                    <option value="">All Users</option>

                                    @foreach($users as $user)

                                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>

                                            {{ ucwords($user->name) }} ({{ $user->role }})

                                        </option>

                                    @endforeach

                                </select>

                            </div>

                            <div class="col-md-3 align-self-end">

                                <a href="{{ route('admin.delivery.index') }}" class="btn btn-outline-secondary ms-2">Reset</a>

                            </div>

                        </div>

                    </form>

                </div>



                <div class="card-body">

                    <div class="table-responsive">

                        <table class="table">

                            <thead>

                                <tr>

                                    <th>ID</th>

                                    <th>Received From</th>

                                    <th>Subject/Document Name</th>

                                    <th>Handed Over By</th>

                                    <th>Send To (Member/Team)</th>

                                    <th>Document Date</th>

                                    <th>Delivered To</th>

                                    <th>Status</th>

                                    <th>Actions</th>

                                </tr>

                            </thead>

                            <tbody>

                                @foreach ($letters as $letter)

                                <tr>

                                    <td>{{ $letter->letter_id }}</td>

                                    <td>{{ ucwords($letter->received_from) }}</td>

                                    <td>{{ ucwords($letter->subject) }}</td>

                                    <td>{{ ucwords($letter->handedOverByUser->name ?? 'Unassigned') }}</td>

                                    <td>

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

                                    </td>

                                    <td>{{ \Carbon\Carbon::parse($letter->created_at)->format('d/m/Y') }}</td>

                                    <td>{{ optional(optional($letter->delivery)->deliveredToUser)->name ? ucwords(optional($letter->delivery)->deliveredToUser->name) : '-' }}</td>


                                    <td id="status-{{ $letter->id }}">

                                        @if ($letter->status === 'Delivered')

                                            <span class="badge bg-success text-white">Delivered</span>

                                        @else

                                            <span class="badge bg-warning text-dark">Pending</span>

                                        @endif

                                    </td>





                                    <td id="actions-{{ $letter->id }}">

                                        <button class="btn btn-sm btn-icon btn-outline-secondary toggle-actions"

                                                data-bs-toggle="modal"

                                                data-bs-target="#letterViewModal-{{ $letter->id }}" data-bs-toggle="tooltip" data-bs-title="View">

                                            <i class="fa fa-eye"></i>

                                        </button>






                                        @if ($letter->status !== 'Delivered')
                                            @php
                                                $disableButton = is_null($letter->handedOverByUser) || empty($letter->send_to);
                                            @endphp

                                            <button 
                                                type="button" 
                                                class="btn btn-sm btn-success deliverBtn" 
                                                data-id="{{ $letter->id }}" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#confirmDeliveryModal"
                                                {{ $disableButton ? 'disabled' : '' }}>
                                                Deliver Now
                                            </button>
                                        @endif




                                        @if ($letter->status === 'Delivered' && $letter->delivery)

                                            <a href="{{ route('admin.delivery.report', $letter->id) }}" class="btn btn-outline-dark" target="_blank">

                                                <i class="fa fa-download"></i> PDF

                                            </a>

                                        @endif

                                    </td>

                                </tr>

                                {{-- <div class="modal fade" id="letterViewModal-{{ $letter->id }}" tabindex="-1" aria-labelledby="letterModalLabel-{{ $letter->id }}" aria-hidden="true">

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

                                                <div class="col-md-6 mb-2"><strong>Subject/Document Name:</strong> {{ ucwords($letter->subject ?? 'N/A') }}</div>

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

                                                <div class="col-md-6 mb-2"><strong>Handed Over By:</strong> {{ ucwords(optional($letter->handedOverByUser)->name ?? 'Unassigned') }}</div>

                                                
                                                <div class="col-md-6 mb-2"><strong>Reference No:</strong> {{ $letter->document_reference_no ?? 'N/A' }}</div>

                                                <div class="col-md-6 mb-2"><strong>Document Date:</strong> {{ \Carbon\Carbon::parse($letter->created_at)->format('m/d/Y') }}</div>

                                                <div class="col-md-6 mb-2"><strong>Delivered To:</strong> {{ optional(optional($letter->delivery)->deliveredToUser)->name ? ucwords(optional($letter->delivery)->deliveredToUser->name) : '-' }}

                                                <div class="col-md-6 mb-2"><strong>Delivered Date:</strong>

                                                    {{ $letter->delivery ? \Carbon\Carbon::parse($letter->delivery->delivered_at)->format('m/d/Y') : 'Not Delivered' }}

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

                                </div> --}}

                                <div class="modal fade" id="letterViewModal-{{ $letter->id }}" tabindex="-1" aria-labelledby="letterModalLabel-{{ $letter->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <div class="modal-content p-4">

                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h5 class="modal-title fw-bold" id="letterModalLabel-{{ $letter->id }}">Letter Details</h5>
                                                <a href="{{ route('admin.delivery.download', $letter->id) }}" class="btn btn-outline-dark btn-sm" target="_blank">
                                                    <i class="fa fa-download"></i> Download Report
                                                </a>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-6"><strong>ID:</strong> {{ $letter->letter_id }}</div>
                                                <div class="col-md-6 text-end">
                                                    <strong>Status:</strong>
                                                    <span class="badge {{ $letter->status == 'delivered' ? 'bg-dark' : 'bg-warning text-dark' }}">
                                                        {{ $letter->status }}
                                                    </span>
                                                </div>
                                            </div>

                                            <hr>

                                            <div class="row mb-3">
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
                                                            $sendToName = $team ? ucwords($team->name) : 'Unknown Team';
                                                        }
                                                    @endphp
                                                    {{ $sendToName }}
                                                </div>
                                                <div class="col-md-6 mb-2"><strong>Document Reference No:</strong> {{ $letter->document_reference_no ?? 'N/A' }}</div>
                                                <div class="col-md-6 mb-2"><strong>Document Date:</strong> {{ \Carbon\Carbon::parse($letter->created_at)->format('d-m-y') }}</div>
                                                <div class="col-md-6 mb-2"><strong>Subject/Document Name:</strong> {{ ucwords($letter->subject ?? 'N/A') }}</div>
                                                <div class="col-md-6 mb-2"><strong>Handed Over By:</strong> {{ ucwords(optional($letter->handedOverByUser)->name ?? 'Unassigned') }}</div>

                                                <div class="col-md-6 mb-2"><strong>Delivered To:</strong> 
                                                    {{ optional(optional($letter->delivery)->deliveredToUser)->name ? ucwords(optional($letter->delivery)->deliveredToUser->name) : '-' }}
                                                </div>
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


                                @endforeach

                            </tbody>

                        </table>

                        
                        <div class="d-flex justify-content-end">
                            {{ $letters->withQueryString()->links() }}
                        </div>

                    </div>

                </div>
                

                <div class="modal fade" id="confirmDeliveryModal" tabindex="-1" aria-labelledby="confirmDeliveryModalLabel" aria-hidden="true">

                    <div class="modal-dialog">

                        <div class="modal-content">

                            <div class="modal-header">

                                <h5 class="modal-title" id="confirmDeliveryModalLabel">Confirm Delivery</h5>

                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

                            </div>

                            <form id="confirmDeliveryForm" method="POST" action="javascript:void(0)">

                                <div class="modal-body">

                                    @csrf 

                                    <input type="hidden" id="teamIdForLetter">

                                    <input type="hidden" id="deliveryLetterId" name="letter_id">



                                    <div class="mb-3">

                                        <label for="memberSelect" class="form-label">Select Member to Deliver To <span class="text-danger">*</span></label>

                                        <select class="form-select select2" id="memberSelect" name="delivered_to_user_id" required>
                                            <option value="">Select a member</option>
                                        </select>

                                        <div class="invalid-feedback">

                                            Please select a member.

                                        </div>

                                    </div>



                                    <div class="mb-3">

                                        <label for="signaturePadCanvas" class="form-label">Signature <span class="text-danger">*</span></label>

                                        <div class="signature-pad-container border border-secondary rounded p-2">

                                            <canvas id="signaturePadCanvas" class="w-100" height="200"></canvas>

                                        </div>

                                        <button type="button" class="btn btn-sm btn-secondary mt-2" id="clearSignature">Clear</button>

                                        <div class="invalid-feedback" id="signatureError">

                                            Please provide a signature.

                                        </div>

                                    </div>

                                </div>

                                <div class="modal-footer">

                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>

                                    <button type="button" class="btn btn-dark" id="confirmDeliveryBtn">Confirm Delivery</button>

                                </div>

                            </form>

                        </div>

                    </div>

                </div>



                <div class="modal fade" id="viewSignatureModal" tabindex="-1" aria-labelledby="viewSignatureModalLabel" aria-hidden="true">

                    <div class="modal-dialog modal-dialog-centered">

                        <div class="modal-content">

                            <div class="modal-header">

                                <h5 class="modal-title" id="viewSignatureModalLabel">View Signature</h5>

                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

                            </div>

                            <div class="modal-body text-center">

                                <img id="signatureImageView" src="" alt="Signature" class="img-fluid">

                            </div>

                        </div>

                    </div>

                </div>

            @endif

            </div>

        </div>

    </div>

</div>



<style>



    #signaturePadCanvas {

        border: 1px solid #ccc;

        border-radius: 4px;

        background-color: #f8f8f8;

    }



    #signaturePadCanvas.is-invalid-border {

        border-color: #dc3545 !important;

    }

</style>

@endsection



@section('script')

<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.6/dist/signature_pad.umd.min.js"></script>

<script>

    $(document).ready(function() {

        const letterTeamMap = @json($letterTeamMap); // {letter_id: team_id}
        const teamMemberUrlBase = "{{ route('admin.delivery.getTeamMembers', ['team' => 'TEAM_ID_PLACEHOLDER']) }}";
        const allMembersUrl = @json(route('admin.delivery.getAllMembers'));

        

        const canvas = document.getElementById('signaturePadCanvas');

        const signaturePad = new SignaturePad(canvas, {

            backgroundColor: 'rgb(248, 248, 248)', 

            penColor: 'rgb(0, 0, 0)'

        });



  

        $('#confirmDeliveryModal').on('shown.bs.modal', function () {

            const ratio = Math.max(window.devicePixelRatio || 1, 1);

            canvas.width = canvas.offsetWidth * ratio;

            canvas.height = canvas.offsetHeight * ratio;

            canvas.getContext("2d").scale(ratio, ratio);

            signaturePad.clear();

  

            $('#memberSelect').removeClass('is-invalid');

            $('#memberSelect').next('.invalid-feedback').hide();

            $('#signaturePadCanvas').removeClass('is-invalid-border');

            $('#signatureError').hide();



          

            $('#memberSelect').val(''); 

        });



        $('#clearSignature').on('click', function() {

            signaturePad.clear();

            $('#signaturePadCanvas').removeClass('is-invalid-border');

            $('#signatureError').hide();

        });


        $(document).on('click', '.deliverBtn', function () {
            const letterId = $(this).data('id');
            $('#deliveryLetterId').val(letterId);

            const teamId = letterTeamMap[letterId] ?? null;
            const memberSelect = $('#memberSelect');
            memberSelect.empty().append('<option value="">Select a member</option>');

            let url = allMembersUrl;

            if (teamId) {
                url = teamMemberUrlBase.replace('TEAM_ID_PLACEHOLDER', teamId);
            }

            $.ajax({
                url: url,
                type: 'GET',
                success: function (response) {
                    $.each(response.members, function (i, member) {
                        const formattedTeams = member.teams && member.teams.length
                            ? member.teams.map(name => name.replace(/\b\w/g, c => c.toUpperCase())).join(', ')
                            : 'No Team';
                        memberSelect.append(`<option value="${member.id}">${member.name} (${formattedTeams})</option>`);
                    });
                },
                error: function (xhr) {
                    console.error('Error loading members:', xhr.responseText);
                }
            });
        });


        $('#confirmDeliveryBtn').on('click', function(e) {

            e.preventDefault(); 



            const memberId = $('#memberSelect').val();

            const letterId = $('#deliveryLetterId').val();

            const signatureData = signaturePad.isEmpty() ? null : signaturePad.toDataURL('image/png'); 



            

            let isValid = true;

            if (!memberId) {

                $('#memberSelect').addClass('is-invalid');

                $('#memberSelect').next('.invalid-feedback').show();

                isValid = false;

            } else {

                $('#memberSelect').removeClass('is-invalid');

                $('#memberSelect').next('.invalid-feedback').hide();

            }



            if (!signatureData) {

                $('#signaturePadCanvas').addClass('is-invalid-border');

                $('#signatureError').show();

                isValid = false;

            } else {

                $('#signaturePadCanvas').removeClass('is-invalid-border');

                $('#signatureError').hide();

            }



            if (!isValid) {

                return;

            }



         

            const $submitBtn = $(this);

            $submitBtn.prop('disabled', true).text('Confirming...');



            $.ajax({

                url: "{{ route('admin.delivery.confirm') }}",

                method: 'POST',

                data: {

                    _token: $('meta[name="csrf-token"]').attr('content'), 

                    letter_id: letterId,

                    delivered_to_user_id: memberId, 

                    signature_data: signatureData

                },

                success: function(response) {

                    if (response.success) {

                        toastFire('success', response.message);

                        $('#confirmDeliveryModal').modal('hide');

                        setTimeout(() => location.reload(), 800);

                    } else {

                        toastFire('error', response.message);

                    }

                },

                error: function(xhr) {

                    console.error('Delivery confirmation error:', xhr.responseText);

                    let errorMessage = 'An error occurred during delivery confirmation.';

                    if (xhr.responseJSON && xhr.responseJSON.message) {

                        errorMessage = xhr.responseJSON.message;

                    } else if (xhr.status === 422) { 

                        errorMessage = 'Validation Error:';

                        $.each(xhr.responseJSON.errors, function(key, value) {

                            errorMessage += '\n- ' + value;

                            if (key === 'delivered_to_user_id') {

                                $('#memberSelect').addClass('is-invalid');

                                $('#memberSelect').next('.invalid-feedback').text(value).show();

                            }

                            if (key === 'signature_data') {

                                $('#signaturePadCanvas').addClass('is-invalid-border');

                                $('#signatureError').text(value).show();

                            }

                        });

                    }

                    alert(errorMessage);

                },

                complete: function() {

                    $submitBtn.prop('disabled', false).text('Confirm Delivery');

                }

            });

        });

    });





    function viewSignature(imageUrl) {

        $('#signatureImageView').attr('src', imageUrl);

        $('#viewSignatureModal').modal('show');

    }

    document.querySelectorAll('.form-select, #filterDate').forEach(el => {
        el.addEventListener('change', () => {
            if (el.form) {
                el.form.submit();
            }
        });
    });

    
    $(document).ready(function () {
        $('#memberSelect').select2({
            placeholder: 'Select member or team',
            allowClear: true,
            dropdownParent: $('#confirmDeliveryModal'),
            width: '100%'
        });
    });


</script>

@endsection





