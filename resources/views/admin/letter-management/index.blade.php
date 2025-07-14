@extends('layouts.app')

@section('content')



<div class="container mt-2">

    <div class="row">

        <div class="col-md-12">

            <div class="card">

                <div class="card-header">

                    <div class="d-flex justify-content-between align-items-center flex-wrap">

                        <h3 class="mb-0">Letter Management</h3>

                        <div class="d-flex flex-wrap gap-2 align-items-center">

                            <button class="btn btn-dark btn-sm" data-bs-toggle="modal" data-bs-target="#addLetterModal">+ Add Letter</button>

                            <a href="{{ route('admin.letter.export', request()->query()) }}" class="btn btn-outline-secondary btn-sm">Export Letters</a>

                        </div>

                    </div>

                </div>

                <div class="card-body">

                    <form method="GET" action="{{ route('admin.letter.management') }}" class="row g-2">

                        <div class="col-md-3">

                            <label for="from_date" class="form-label">From Date</label>

                            <input type="date" name="from_date" id="from_date" class="form-control" value="{{ request('from_date') }}">

                        </div>



                        <div class="col-md-3">

                            <label for="to_date" class="form-label">To Date</label>

                            <input type="date" name="to_date" id="to_date" class="form-control" value="{{ request('to_date') }}">

                        </div>


                        <div class="col-md-3">
                            <label for="created_by" class="form-label">Created By</label>
                            <select name="created_by" id="created_by" class="form-select">
                                <option value="">-- All Receptionist--</option>
                                @foreach($creators as $creator)
                                    <option value="{{ $creator->id }}" {{ request('created_by') == $creator->id ? 'selected' : '' }}>
                                        {{ ucwords($creator->name) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>


                        <div class="col-md-3">

                            <label for="status" class="form-label">Status</label>

                            <select name="status" id="status" class="form-select">

                                <option value="">-- All --</option>

                                <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>

                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>

                            </select>

                        </div>



                        <div class="col-md-3 d-flex align-items-end">

                            <button type="submit" class="btn btn-primary me-2">Filter</button>

                            <a href="{{ route('admin.letter.management') }}" class="btn btn-secondary">Reset</a>

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

                                    <th>Handed Over By</th>

                                    <th>Send To (Member/Team)</th>

                                    <th>Subject/Document Name</th>

                                    <th>Document Reference No</th>

                                    <th>Document Date </th>

                                    <th>Created By</th>

                                    <th>Created At</th>

                                    <th>Status</th>

                                    <th>Actions</th>

                                </tr>

                            </thead>

                            <tbody>

                                @foreach($letters as $letter)

                                    <tr>

                                        <td>{{ $letter->letter_id }}</td>

                                        <td>{{ ucwords($letter->received_from) }}</td>

                                        <td>{{ ucwords($letter->handedOverByUser->name ?? 'N\A') }}</td>

                                        {{-- <td>{{ $letter->send_to }}</td> --}}

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

                                        <td>{{ ucwords($letter->subject) }}</td>

                                        <td>{{ $letter->document_reference_no }}</td>

                                        <td>{{ $letter->document_date ? \Carbon\Carbon::parse($letter->document_date)->format('d-m-Y') : '' }}</td>

                                        <td>{{Optional($letter->createdBy)->name ?? '_' }}</td>

                                        <td>{{$letter->created_at ? \Carbon\Carbon::parse($letter->created_at)->format('d-m-Y') : '' }}</td>

                                        <td>

                                            <span class="badge bg-{{ $letter->status == 'Delivered' ? 'badge bg-success text-white' : 'badge bg-warning text-dark' }}">

                                                {{ $letter->status }}

                                            </span>

                                        </td>

                                        <td>
                                            <!-- View Button -->
                                            <button class="btn btn-sm btn-outline-info view-letter-btn"
                                                data-id="{{ $letter->id }}"
                                                data-received_from="{{ ucwords($letter->received_from) }}"
                                                data-send_to="{{ ucwords($sendToName) }}"
                                                data-document_reference_no="{{ $letter->document_reference_no }}"
                                                data-document_date="{{ $letter->document_date }}"
                                                data-subject="{{ ucwords($letter->subject) }}"
                                                data-handed_over_by="{{ ucwords(optional($letter->handedOverByUser)->name) ?? '-' }}"
                                                data-created_by="{{ ucwords(optional($letter->createdBy)->name) ?? '-' }}"
                                                data-created_at="{{ $letter->created_at }}"
                                                data-document_image="{{ $letter->document_image ? asset('uploads/letters/' . $letter->document_image) : '' }}"
                                                data-bs-toggle="tooltip"
                                                title="View">
                                                <i class="fa fa-eye"></i>
                                            </button>

                                            
                                            @if($letter->status !== 'Delivered')
                                            <button class="btn btn-sm btn-outline-primary edit-letter-btn"
                                                data-id="{{ $letter->id }}"
                                                data-received_from="{{ $letter->received_from }}"
                                                data-send_to="{{ $letter->send_to }}"
                                                data-document_reference_no="{{ $letter->document_reference_no }}"
                                                data-document_date="{{ $letter->document_date }}"
                                                data-subject="{{ $letter->subject }}"
                                                data-handed_over_by="{{ $letter->handed_over_by }}"
                                                data-document_image="{{ $letter->document_image }}"
                                                data-bs-toggle="tooltip"
                                                data-bs-title="Edit">
                                                <i class="fa fa-pen"></i>
                                            </button>
                                            @endif

                                            <button class="btn btn-sm btn-outline-danger deleteBtn" data-id="{{ $letter->id }}" data-bs-toggle="tooltip" data-bs-title="Delete"><i class="fa fa-trash"></i></button>

                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                        <div class="d-flex justify-content-end">

                            {{ $letters->withQueryString()->links() }}

                        </div>

                    </div>

                </div>


                <!-- Letter View Modal -->
                <div class="modal fade" id="viewLetterModal" tabindex="-1" aria-labelledby="viewLetterModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Letter Details</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <table class="table table-bordered">
                                    <tr><th>Received From</th><td id="view_received_from"></td></tr>
                                    <tr><th>Send To</th><td id="view_send_to"></td></tr>
                                    <tr><th>Document Ref No</th><td id="view_document_reference_no"></td></tr>
                                    <tr><th>Document Date</th><td id="view_document_date"></td></tr>
                                    <tr><th>Subject</th><td id="view_subject"></td></tr>
                                    <tr><th>Handed Over By</th><td id="view_handed_over_by"></td></tr>
                                    <tr><th>Created By</th><td id="view_created_by"></td></tr>
                                    <tr><th>Created At</th><td id="view_created_at"></td></tr>
                                    <tr><th>Document Image</th>
                                        <td>
                                            <span id="view_document_image_wrapper"></span>
                                        </td>
                                    </tr>

                                </table>
                            </div>
                        </div>
                    </div>
                </div>


                {{-- modal --}}

                <div class="modal fade" id="addLetterModal" tabindex="-1">

                    <div class="modal-dialog modal-lg"> 

                        <form id="letterForm" method="POST" action="{{ route('admin.letter.store') }}" enctype="multipart/form-data"
                            onsubmit="disableSubmitButton()">

                            @csrf

                            <div class="modal-content">

                                <div class="modal-header">

                                    <h5 class="modal-title">Add New Letter</h5>

                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>

                                </div>



                                <div class="modal-body">

                                    <div class="row g-3">

                                        

                                        <div class="col-md-6">

                                            <label class="form-label">Received From</label>

                                            <input type="text" name="received_from" class="form-control" placeholder="Enter senders name and address">

                                        </div>





                                        <div class="col-md-6">

                                            <label class="form-label">Send To (Member/Team)</label>

                                            <select name="send_to" class="form-select select2">
                                                <option value="">Select member or team</option>
                                                <optgroup label="Members">

                                                    @foreach($members as $member)

                                                        @php

                                                            $teamNames = $member->team->pluck('name')->implode(', ');

                                                        @endphp

                                                        <option value="member_{{ $member->id }}">

                                                            {{ ucwords($member->name) }}

                                                            @if($teamNames)

                                                                ({{ ucwords($teamNames) }} Member)

                                                            @endif

                                                        </option>

                                                    @endforeach

                                                </optgroup>





                                                <optgroup label="Teams">

                                                    @foreach($teams as $team)

                                                        <option value="team_{{ $team->id }}">

                                                            {{ ucwords($team->name) }}

                                                        </option>

                                                    @endforeach

                                                </optgroup>

                                            </select>

                                        </div>



                                        <div class="col-md-6">

                                            <label class="form-label">Document Reference No</label>

                                            <input type="text" name="document_reference_no" class="form-control" placeholder="Enter reference number">

                                        </div>



                                        <div class="col-md-6">

                                            <label class="form-label">Document Date</label>

                                            <input type="date" name="document_date" class="form-control" placeholder="Enter date">

                                        </div>



                                        

                                        <div class="col-md-6">

                                            <label class="form-label">Subject/Document Name</label>

                                            <input type="text" name="subject" class="form-control" placeholder="Enter subject or document name">

                                        </div>



                                        

                                        <div class="col-md-6">

                                            <label class="form-label">Handed Over By</label>

                                            <select name="handed_over_by" class="form-select">

                                                <option value="">Select Person</option>

                                                @foreach($users as $user)

                                                    @if(!(Auth::user()->role === 'Receptionist' && Auth::id() === $user->id))

                                                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->role }})</option>

                                                    @endif

                                                @endforeach

                                            </select>

                                        </div>



                                        

                                        <div class="col-md-6">

                                            <label class="form-label">Upload Image / PDF</label>

                                            <input type="file" name="document_image" class="form-control" accept="image/*,application/pdf">

                                            <small class="text-muted">(Accepts images or PDF files. Optional.)</small>

                                        </div>

                                    </div>

                                </div>



                                <div class="modal-footer">

                                    <button type="submit" id="addLetterSubmitBtn" class="btn btn-dark">Add Letter</button>

                                </div>

                            </div>

                        </form>

                    </div>

                </div>



                <div class="modal fade" id="editLetterModal" tabindex="-1">

                    <div class="modal-dialog modal-lg">

                        <form id="editLetterForm" method="POST" enctype="multipart/form-data">

                            @csrf

                            <div class="modal-content">

                                <div class="modal-header">

                                    <h5 class="modal-title">Edit Letter</h5>

                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>

                                </div>

                                <div class="modal-body">

                                    <input type="hidden" name="letter_id" id="editLetterId">

                                    <div class="row g-3">

                                        <div class="col-md-6">

                                            <label>Received From</label>

                                            <input type="text" name="received_from" id="editReceivedFrom" class="form-control" placeholder="Enter senders name and address">

                                        </div>

                                        

                                        <div class="col-md-6">

                                            <label class="form-label">Send To (Member/Team)</label>
                                            

                                            <select name="send_to" id="editSendTo" class="form-select">

                                                <option value="" disabled selected>Select member/team</option>

                                                <optgroup label="Members">

                                                    @foreach($members as $member)

                                                            @php

                                                                $teamNames = $member->team->pluck('name')->implode(', ');

                                                            @endphp

                                                            <option value="member_{{ $member->id }}">

                                                                {{ ucwords($member->name) }}

                                                                @if($teamNames)

                                                                    ({{ ucwords($teamNames) }} Member)

                                                                @endif

                                                            </option>

                                                    @endforeach

                                                </optgroup>

                                                <optgroup label="Teams">

                                                    @foreach($teams as $team)

                                                        <option value="team_{{ $team->id }}">{{ ucwords($team->name) }}</option>

                                                    @endforeach

                                                </optgroup>

                                            </select>

                                        </div>





                                        <div class="col-md-6">

                                            <label>Document Reference No</label>

                                            <input type="text" name="document_reference_no" id="editReference" class="form-control" placeholder="Enter reference number">

                                        </div>

                                        <div class="col-md-6">

                                            <label>Document Date</label>

                                            <input type="date" name="document_date" id="editDocumentDate" class="form-control" placeholder="Enter date">

                                        </div>

                                        <div class="col-md-6">

                                            <label>Subject/Document Name</label>

                                            <input type="text" name="subject" id="editSubject" class="form-control" placeholder="Enter subject or document name">

                                        </div>

                                        <div class="col-md-6">

                                            <label class="form-label">Handed Over By</label>

                                            <select name="handed_over_by" id="editHandedOverBy" class="form-select">

                                                <option value="" disabled selected>Select person</option>

                                                @foreach($users as $user)

                                                    <option value="{{ $user->id }}">{{ ucwords($user->name) }} ({{ ucfirst($user->role) }})</option>

                                                @endforeach

                                            </select>

                                        </div>

                                        <div class="col-md-6">

                                            <label>Upload Image / PDF</label>

                                            <input type="file" name="document_image" id="editDocumentImage" class="form-control">

                                            <small class="text-muted">(Accepts images or PDF files. Optional.)</small>

                                            <div id="existingDocumentPreview" class="mt-2"></div>

                                        </div>

                                    </div>

                                </div>

                                <div class="modal-footer">

                                    <button type="submit" class="btn btn-dark">Update Letter</button>

                                </div>

                            </div>

                        </form>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>



@endsection



@section('script')

<script>

    $('#letterForm').on('submit', function (e) {

        e.preventDefault();

        const form = this;

        const formData = new FormData(form);



        $.ajax({

            type: 'POST',

            url: form.action,

            data: formData,

            contentType: false,

            processData: false,

            success: function (res) {

                if (res.status) {

                    toastFire('success', res.message);

                    $('#addLetterModal').modal('hide');

                    form.reset();

                    setTimeout(() => location.reload(), 800);

                } else {

                    toastFire('error', res.message);

                }

            },

            error: function (xhr) {

                let err = xhr.responseJSON?.message || 'Something went wrong.';

                toastFire('error', err);

            }

        });

    });



    $('.edit-letter-btn').on('click', function () {

        const id = $(this).data('id');



        $('#editLetterId').val(id);

        $('#editReceivedFrom').val($(this).data('received_from'));

       // $('#editSendTo').val($(this).data('send_to'));

        $('#editSendTo').val('').trigger('change');

        const sendToValue = $(this).data('send_to');



        if (sendToValue) {

            $('#editSendTo').val(sendToValue).trigger('change');

        } else {

            $('#editSendTo').val('').trigger('change');

        }

        $('#editReference').val($(this).data('document_reference_no'));

        $('#editDocumentDate').val($(this).data('document_date'));

        $('#editSubject').val($(this).data('subject'));

        // $('#editHandedOverBy').val($(this).data('handed_over_by'));

        const handedOverBy = $(this).data('handed_over_by');



        if (handedOverBy) {

            $('#editHandedOverBy').val(handedOverBy).trigger('change');

        } else {

            $('#editHandedOverBy').val('').trigger('change');

        }

        

        const filename = $(this).data('document_image');

        if (filename) {

            const extension = filename.split('.').pop().toLowerCase();

            const baseUrl = "{{ asset('uploads/letters') }}";
            const fileUrl = `${baseUrl}/${filename}`;




            if (extension === 'pdf') {

                $('#existingDocumentPreview').html(

                    `<p class="text-muted">Existing File: <a href="${fileUrl}" target="_blank">${filename}</a></p>`

                );

            } else {

                $('#existingDocumentPreview').html(

                    `<p class="text-muted">Existing File:</p>

                    <img src="${fileUrl}" alt="Uploaded Image" class="img-fluid rounded" width="100">`

                );

            }

        } else {

            $('#existingDocumentPreview').html('');

        }

        

        const action = "{{ route('admin.letter.update', ['id' => '__id__']) }}".replace('__id__', id);

        $('#editLetterForm').attr('action', action);



        $('#editLetterModal').modal('show');

    });



    $('#editLetterForm').on('submit', function (e) {

        e.preventDefault();



        const form = this;

        const id = $('#editLetterId').val();

        const formData = new FormData(form); 



        $.ajax({
            type: 'POST',
            url: "{{ route('admin.letter.update', ':id') }}".replace(':id', id),
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                if (res.status) {
                    toastFire('success', res.message);
                    $('#editLetterModal').modal('hide');
                    setTimeout(() => location.reload(), 800);
                } else {
                    toastFire('error', res.message);
                }
            },
            error: function (xhr) {
                const err = xhr.responseJSON?.message || 'Something went wrong.';
                toastFire('error', err);
            }
        });


    });


    function disableSubmitButton() {
        const btn = document.getElementById('addLetterSubmitBtn');
        btn.disabled = true;
        btn.innerHTML = `<span class="spinner-border spinner-border-sm"></span> Saving...`;
    }




    $('.deleteBtn').on('click', function (e) {

        e.preventDefault();

        const btn = $(this);

        const userId = btn.data('id');



        Swal.fire({

            title: 'Are you sure?',

            icon: 'warning',

            showCancelButton: true,

            confirmButtonText: 'Yes, delete it!'

        }).then((result) => {

            if (result.isConfirmed) {

                $.post("{{ route('admin.letter.delete', ['id' => '__id__']) }}".replace('__id__', userId), {

                    _token: '{{ csrf_token() }}'

                }, function (res) {

                    if (res.status) {

                        toastFire('success', res.message);

                        btn.closest('tr').remove(); 

                    } else {

                        toastFire('error', res.message);

                    }

                });

            }

        });

    });

    $(document).on('click', '.view-letter-btn', function () {
        $('#view_received_from').text($(this).data('received_from'));
        $('#view_send_to').text($(this).data('send_to'));
        $('#view_document_reference_no').text($(this).data('document_reference_no'));
        $('#view_document_date').text($(this).data('document_date'));
        $('#view_subject').text($(this).data('subject'));
        $('#view_handed_over_by').text($(this).data('handed_over_by'));
        $('#view_created_by').text($(this).data('created_by'));
        $('#view_created_at').text($(this).data('created_at'));

        const imageUrl = $(this).data('document_image');

        if (imageUrl && imageUrl !== '') {
            $('#view_document_image_wrapper').html(
                `<a href="${imageUrl}" target="_blank" class="btn btn-sm btn-outline-info">View Image</a>`
            );
        } else {
            $('#view_document_image_wrapper').html('<span>No Image Available</span>');
        }

        const modal = new bootstrap.Modal(document.getElementById('viewLetterModal'));
        modal.show();
    
    });

</script>



@endsection

