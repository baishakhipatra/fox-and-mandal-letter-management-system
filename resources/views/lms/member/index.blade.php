@extends('layouts.app')

@section('content')

<div class="container mt-2">
        <div class="row">
            <div class="col-md-12">

                @if (session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                @endif

                <div class="card data-card mt-3">
                    <div class="card-header">
                        <h4 class="d-flex">
                            Members
                            @can('member csv export')
                            <a href="{{ url('members/export/csv',['keyword'=>$request->keyword]) }}" class="btn btn-sm btn-cta ms-auto" data-bs-toggle="tooltip" title="Export data in CSV">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-download"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                CSV
                            </a>
                            @endcan
                            @can('member csv upload')
                            <a href="#csvModal" data-bs-toggle="modal" class="btn btn-sm btn-cta"> Bulk Upload</a>
                            @endcan
                             @can('create member')
                                <a href="{{ url('members/create') }}" class="btn btn-sm btn-cta">Add Member</a>
                             @endcan
                        </h4>
                        <div class="search__filter mb-0">
                            <div class="row">
                                <div class="col-md-2">
                                    <p class="text-muted mt-1 mb-0 entries-text">Showing {{$users->count()}} out of {{$users->total()}} Entries</p>
                                </div>
                            </div>
                            <div class="row">
                                
                                <div class="col-md-12 text-end">
                                    <form class="row align-items-end" action="">
                                        <div class="col">
                                            <input type="search" name="keyword" id="term" class="form-control form-control-sm" placeholder="Search by keyword." value="{{app('request')->input('keyword')}}" autocomplete="off">
                                        </div>
                                        <div class="col">
                                            <!--<div class="btn-group">-->
                                            <button type="submit" class="btn btn-cta btn-sm">
                                                Filter
                                            </button>
                                            <a href="{{ url()->current() }}" class="btn btn-sm btn-cta" data-bs-toggle="tooltip" title="Clear Filter">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                            </a>
                                            <!--</div>-->
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                            <thead>
                                <tr>
                                    <th class="index-col">#</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Mobile</th>
                                    <th>Status</th>
                                    <th>Qrcode</th>
                                    <th class="action_btn">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($users as $index=> $user)
                                @php
                                  $assignedPermissions = DB::table('user_permission_categories')
                                    ->where('user_id', $user->id)
                                    ->pluck('name')
                                    ->toArray();
                                  
                                @endphp
                                
                                <tr>
                                    <td class="index-col">{{ $index+1 }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        {{ $user->mobile }}
                                    </td>
                                    <td> @can('member status change')<a href="{{ url('members/'.$user->id.'/status/change') }}" ><span class="badge badge-status bg-{{($user->status == 1) ? 'success' : 'danger'}}">{{($user->status == 1) ? 'Active' : 'Inactive'}}</span></a>@endcan</td>
                                    @if($user->type=='authorized member')
                                    @if(!empty($user->qrcode))
                                    <td><!-- QR Code Display -->
                                            <img src="https://bwipjs-api.metafloor.com/?bcid=qrcode&text={{$user->qrcode}}&height=13&textsize=10&scale=3&includetext" 
                                                 alt="QR Code" 
                                                 style="height: 83px;width:83px" 
                                                 id="qr-{{$user->id}}" 
                                                 data-qrcode="{{ $user->qrcode }}">
                                        
                                            <!-- Print Button -->
                                            <a class="btn btn-sm btn-danger print_btn" data-bs-toggle="tooltip" title="Print QR" onclick="printQRCode('{{ $user->id }}','{{ $user->qrcode }}')">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-download">
                                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                                    <polyline points="7 10 12 15 17 10"></polyline>
                                                    <line x1="12" y1="15" x2="12" y2="3"></line>
                                                </svg>
                                            </a>
                                    </td>
                                    @else
                                    <td></td>
                                     @endif
                                    @else
                                    <td></td>
                                    @endif
                                   
                                    <td style="white-space: nowrap;">
                                        @can('update member')
                                        <a href="{{ url('members/'.$user->id.'/edit') }}" class="btn btn-cta">
                                            <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="512" height="512" x="0" y="0" viewBox="0 0 492.493 492" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="M304.14 82.473 33.165 353.469a10.799 10.799 0 0 0-2.816 4.949L.313 478.973a10.716 10.716 0 0 0 2.816 10.136 10.675 10.675 0 0 0 7.527 3.114 10.6 10.6 0 0 0 2.582-.32l120.555-30.04a10.655 10.655 0 0 0 4.95-2.812l271-270.977zM476.875 45.523 446.711 15.36c-20.16-20.16-55.297-20.14-75.434 0l-36.949 36.95 105.598 105.597 36.949-36.949c10.07-10.066 15.617-23.465 15.617-37.715s-5.547-27.648-15.617-37.719zm0 0" fill="#ffffff" opacity="1" data-original="#000000" class=""></path></g></svg>
                                        </a>
                                        @endcan
                                        @can('view member')
                                        <a href="{{ url('members/'.$user->id) }}" class="btn btn-cta">
                                            <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="512" height="512" x="0" y="0" viewBox="0 0 511.999 511.999" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="M508.745 246.041c-4.574-6.257-113.557-153.206-252.748-153.206S7.818 239.784 3.249 246.035a16.896 16.896 0 0 0 0 19.923c4.569 6.257 113.557 153.206 252.748 153.206s248.174-146.95 252.748-153.201a16.875 16.875 0 0 0 0-19.922zM255.997 385.406c-102.529 0-191.33-97.533-217.617-129.418 26.253-31.913 114.868-129.395 217.617-129.395 102.524 0 191.319 97.516 217.617 129.418-26.253 31.912-114.868 129.395-217.617 129.395z" fill="#ffffff" opacity="1" data-original="#000000" class=""></path><path d="M255.997 154.725c-55.842 0-101.275 45.433-101.275 101.275s45.433 101.275 101.275 101.275S357.272 311.842 357.272 256s-45.433-101.275-101.275-101.275zm0 168.791c-37.23 0-67.516-30.287-67.516-67.516s30.287-67.516 67.516-67.516 67.516 30.287 67.516 67.516-30.286 67.516-67.516 67.516z" fill="#ffffff" opacity="1" data-original="#000000" class=""></path></g></svg>
                                        </a>
                                        @endcan
                                            
                                        @can('delete member')
                                        <a href="{{ url('members/'.$user->id.'/delete') }}" class="btn btn-cta">
                                            <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="512" height="512" x="0" y="0" viewBox="0 0 24 24" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="M19 7a1 1 0 0 0-1 1v11.191A1.92 1.92 0 0 1 15.99 21H8.01A1.92 1.92 0 0 1 6 19.191V8a1 1 0 0 0-2 0v11.191A3.918 3.918 0 0 0 8.01 23h7.98A3.918 3.918 0 0 0 20 19.191V8a1 1 0 0 0-1-1ZM20 4h-4V2a1 1 0 0 0-1-1H9a1 1 0 0 0-1 1v2H4a1 1 0 0 0 0 2h16a1 1 0 0 0 0-2ZM10 4V3h4v1Z" fill="#ffffff" opacity="1" data-original="#000000" class=""></path><path d="M11 17v-7a1 1 0 0 0-2 0v7a1 1 0 0 0 2 0ZM15 17v-7a1 1 0 0 0-2 0v7a1 1 0 0 0 2 0Z" fill="#ffffff" opacity="1" data-original="#000000" class=""></path></g></svg>
                                        </a>
                                        @endcan
                                        @can('member issue list')
                                        <!--<a href="{{ url('members/'.$user->id.'/issue/list') }}" class="btn btn-cta">Issue List</a>-->
                                        @endcan
                                        
                                        @can('give app permission')
                                        <!--<a href="{{ url('members/'.$user->id.'/issue/list') }}" class="btn btn-primary mx-2">App Permission</a>-->
                                        <!--<a href="#url" data-bs-toggle="modal" data-bs-target="#permissionModal{{ $user->id }}" class="btn btn-cta permission_btn" data-id="{{ $user->id }}">App Permission</a>-->
                                        <div class="modal action-modal fade" id="permissionModal{{ $user->id }}" tabindex="-1" aria-labelledby="permissionModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                <div class="modal-header">
                                    <span id="permissionModalLabel">Give access to members</span>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form id="permissionForm{{ $user->id }}" method="POST" action="{{route('members.getPermissionsAndMembers',$user->id)}}">
                                        @csrf
                                    <div class="modal-body">
                                        <input type="hidden" name="id" value="{{ $user->id }}">
                                        <!-- Communication Medium -->
                                        <h6>Select:</h6>
                                        
                                        <div class="form-check">
                                            <input 
                                                class="form-check-input medium-checkbox" 
                                                type="checkbox" 
                                                name="name[]" 
                                                value="Lms" 
                                                id="mediumLMS"
                                                {{ isset($assignedPermissions) && in_array('Lms', $assignedPermissions) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="mediumLMS">LMS</label>
                                        </div>
                                        <div class="form-check">
                                            <input 
                                                class="form-check-input medium-checkbox" 
                                                type="checkbox" 
                                                name="name[]" 
                                                value="Fms" 
                                                id="mediumFMS"
                                                {{ isset($assignedPermissions) && in_array('Fms', $assignedPermissions) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="mediumFMS">FMS</label>
                                        </div>
                                       
                                        <div class="form-check">
                                            <input 
                                                class="form-check-input medium-checkbox" 
                                                type="checkbox" 
                                                name="name[]" 
                                                value="Cave" 
                                                id="mediumOther"
                                                {{ isset($assignedPermissions) && in_array('Cave', $assignedPermissions) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="mediumOther">Cavity</label>
                                        </div>
                                        <div class="cta-row">
                                            <button type="button" class="btn btn-cta" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-cta" >Save</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                                        @endcan
                                        
                                    </td>
                                    
                                    
                                </tr>
                               
                                  @empty
                                    <tr>
                                        <td colspan="9" class="text-center">No record found</td>
                                    </tr>
                               
                                @endforelse
                            </tbody>
                        </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<div class="modal action-modal fade" id="csvModal" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                Bulk Upload
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="post" action="{{ url('members/upload/csv') }}" enctype="multipart/form-data">@csrf
                    <input type="file" name="file" class="form-control" accept=".csv">
                    
                    <div class="cta-row">
                    <a href="{{ asset('backend/csv/sample-member.csv') }}" class="btn-cta">Download Sample CSV</a>
                    <button type="submit" class="btn btn-cta" id="csvImportBtn">Import <i class="fas fa-upload"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>



@endsection
@section('script')
<script>
$(document).ready(function () {
        var selectedItemId; // Variable to store the selected item ID
    
        // Show the modal when "Send Reminder" is clicked
        $(document).on('click', '.permission_btn', function (e) {
           e.preventDefault();
           selectedItemId = $(this).data('id');
          //  console.log(selectedItemId);// Retrieve the item ID
             $('#permissionModal').modal('show'); // Show the modal
        });
    
        // Handle the "Send Reminder" button in the modal
         //$(document).on('click', '#permissionBtn', function () {
             $('#permissionBtn').on('click', function () {
             //selectedItemId = $(this).data('id');// Get the user ID from the button ID
            let selectedMediums = [];

        // Collect selected permissions (checkboxes)
            $('.medium-checkbox:checked').each(function () {
                selectedMediums.push($(this).val());
            });
    
            // Check if any checkbox is selected
            if (selectedMediums.length === 0) {
                //alert('Please select at least one access.');
                toastFire("Error!", "Please select at least one.");
                return;
            }
    
            // Perform AJAX request
            $.ajax({
                url: '{{ route("members.getPermissionsAndMembers", ":id") }}'.replace(':id', selectedItemId), // Replace ":id" with the actual ID
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}" // CSRF token for Laravel
                },
                data: {
                    id: selectedItemId,
                    name: selectedMediums
                },
                success: function (response) {
                    // Show success message and close the modal
                    toastFire("Success!", response.message, "success");
                    $('#permissionModal').modal('hide'); // Hide the modal
    
                    // Optional: Reload or update the UI if needed
                    setTimeout(function () {
                        window.location.reload();
                    }, 3000);
                },
                error: function (xhr) {
                    // Handle error response
                    toastFire("Error!", "Something went wrong!", "error");
                }
            });
        });
    });
    
     
</script>
<script>
    function printQRCode(itemId,qrText) {
        console.log(itemId);
       
        
        console.log(qrText);
        // Open a new window for printing
        const printWindow = window.open('', '', 'width=600,height=400');
        const qrSrc = `https://bwipjs-api.metafloor.com/?bcid=qrcode&text=${qrText}&height=6&textsize=10&scale=6&includetext`;

        printWindow.document.write(`
            <html>
            <head>
                <title>Print QR</title>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        text-align: center;
                    }
                    .sticker-container {
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        justify-content:center;
                        width: 400px;  /* Adjust width to match sticker size */
                        height: 150px; /* Adjust height to match sticker size */
                        margin-top: 20px;
                    }
                    .print-container {
                        margin-top: 20px;
                    }
                    .book-title {
                        font-size: 20px;
                        font-weight: bold;
                        margin-bottom: 10px;
                    }
                    .qr-code {
                        width: 40%; /* QR code on the left half */
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        position:relative;
                        top:10px;
                       
                    }
                    .uid-text {
                        width: 50%; /* UID on the right half */
                        font-size: 17px;
                        
                        position:relative;
                        top:10px;
                        line-height:1;
                        text-align:left;
                        
                    }
                        .uid-text h2 {
                            line-height:0;
                            
                        }
                        .uid-text h2.tt {
                            position:relative;
                            top:10px;
                        }
                   
                </style>
            </head>
            <body>
                <div class="sticker-container">
                    <!-- Book Name -->
                    
                    
                    <!-- QR Code Placeholder -->
                    <div class="qr-code">
                        <img id="qr-code-img" src="${qrSrc}" style="height: 120px; width: 120px;">
                    </div>
                     <div class="uid-text">
                        <img style="width:190px" src="{{asset('backend/images/logo.png')}}">
                        
                    </div>
                </div>
            </body>
            </html>
        `);

        // Wait for the image to load before printing
        const qrCodeImg = printWindow.document.getElementById('qr-code-img');
        qrCodeImg.onload = function () {
            // Once the image is loaded, trigger the print
            printWindow.document.close();
            printWindow.focus();
            printWindow.print();
            printWindow.close();
        };

        // If the image fails to load, close the window
        qrCodeImg.onerror = function () {
            alert('QR code could not be loaded.');
            printWindow.close();
        };
    }
</script>
@endsection