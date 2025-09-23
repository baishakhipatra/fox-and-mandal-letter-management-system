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
                    </div>
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <form method="GET" action="{{ route('home') }}">
                                <div class="row g-2 align-items-end">

                                    <div class="col-md-4">
                                        <label class="form-label">Received Date Range</label>
                                        <input type="text" name="received_date_range" class="form-control" id="receivedDateRange" 
                                        placeholder="Received Date Range" value="{{ request('received_date_range') }}" autocomplete="off">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">Delivery Date Range</label>
                                        <input type="text" name="delivery_date_range" class="form-control" id="deliveryDateRange" 
                                        placeholder="Delivery Date Range"   value="{{ request('delivery_date_range') }}" autocomplete="off">
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">Received From</label>
                                        <input type="text" name="received_from" class="form-control" placeholder="Sender name" value="{{ request('received_from') }}">
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">Subject</label>
                                        <input type="text" name="subject" class="form-control" placeholder="Subject" value="{{ request('subject') }}">
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">Document Type</label>
                                        <select name="document_type" class="form-select">
                                            <option value="">-- All Types --</option>
                                            <option value="Letter" {{ request('document_type') == 'Letter' ? 'selected' : '' }}>Letter</option>
                                            <option value="Memo" {{ request('document_type') == 'Memo' ? 'selected' : '' }}>Memo</option>
                                            <option value="Notice" {{ request('document_type') == 'Notice' ? 'selected' : '' }}>Notice</option>
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">Status</label>
                                        <select name="status" class="form-select">
                                            <option value="">-- All --</option>
                                            <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="Delivered" {{ request('status') == 'Delivered' ? 'selected' : '' }}>Delivered</option>
                                        </select>
                                    </div>

                                    <div class="col-md-12 text-end mt-2">
                                        <button type="submit" class="btn btn-dark">Filter</button>
                                        <a href="{{ route('home') }}" class="btn btn-outline-secondary">Reset</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th>Letter ID</th>
                                    <th>Received Date</th>
                                    <th>Received From</th>
                                    <th>Subject/Document Name</th>
                                    <th>Document Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($letters as $letter)
                                    <tr>
                                        <td>{{ $letter->letter_id }}</td>
                                        <td>{{ \Carbon\Carbon::parse($letter->received_date)->format('d-m-Y') }}</td>
                                        <td>{{ ucwords($letter->received_from) }}</td>
                                        <td>{{ ucwords($letter->subject ?? 'N/A') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($letter->document_date)->format('d-m-Y') }}</td>
                                        <td>
                                            <span class="badge {{ $letter->status == 'Delivered' ? 'bg-success' : 'bg-warning text-dark' }}">
                                                {{ $letter->status }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.delivery.download', $letter->id) }}" class="btn btn-outline-dark btn-sm mb-2" target="_blank">
                                                <i class="fas fa-shipping-fast"></i>Delivery
                                            </a>
                                            @if(!empty($letter->document_image))
                                            <a href="{{ asset($letter->document_image) }}" class="btn btn-outline-dark btn-sm mb-2" target="_blank">
                                                <i class="fa fa-download"></i>Download
                                            </a>
                                            {{-- @else
                                                <span class="badge bg-secondary">No Image Available</span> --}}
                                            @endif
                                            @if($letter->status === 'Delivered')
                                                <button class="btn btn-sm btn-outline-primary mb-2" data-bs-toggle="modal" data-bs-target="#letterViewModal_{{ $letter->id }}">
                                                    <i class="fas fa-update"></i>Update Matter Code
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                    <div class="modal fade" id="letterViewModal_{{ $letter->id }}" tabindex="-1" aria-labelledby="letterModalLabel_{{ $letter->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-lg">
                                            <div class="modal-content p-4">
                                                <form action="{{ route('updateMatterCode', $letter->id) }}" method="POST">
                                                    @csrf
                                                    <div class="row">
                                                        <div class="col-md-6 mb-2"><strong>ID:</strong> {{ $letter->letter_id }}</div>
                                                        <div class="col-md-6 mb-2">
                                                            <label class="form-label">Matter Code</label>
                                                            <input type="text" name="matter_code" class="form-control" placeholder="Enter matter code" value="{{ $letter->matter_code }}">
                                                        </div>
                                                    </div>
                                                    <div class="text-end mt-3">
                                                        <button type="submit" class="btn btn-primary btn-sm">Save</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <tr><td colspan="7" class="text-center">No letters found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-center mt-3">
                            {{ $letters->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>
@endsection

@section('script')
    <script>
        $(function() {
            // Received Date
            $('#receivedDateRange').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear'
                }
            });

            $('#receivedDateRange').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('YYYY-MM-DD') + ' to ' + picker.endDate.format('YYYY-MM-DD'));
            });

            $('#receivedDateRange').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
            });

            // Delivery Date
            $('#deliveryDateRange').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear'
                }
            });

            $('#deliveryDateRange').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('YYYY-MM-DD') + ' to ' + picker.endDate.format('YYYY-MM-DD'));
            });

            $('#deliveryDateRange').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
            });
        });
    </script>
@endsection
