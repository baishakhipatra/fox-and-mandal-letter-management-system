@extends('layouts.app')

@section('content')


<div class="container mt-5">
        <div class="row">
            <div class="col-md-12">

                @if ($errors->any())
                <ul class="alert alert-warning">
                    @foreach ($errors->all() as $error)
                        <li>{{$error}}</li>
                    @endforeach
                </ul>
                @endif

                <div class="card data-card">
                    <div class="card-header">
                        <h4 class="d-flex">Flight Booking Detail
                            <a href="{{ url('flight-booking/list') }}" class="btn btn-cta ms-auto">Back</a>
                            
                        </h4>
                    </div>
                    @can('flight booking status update')
                   <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="btn-group" role="group" aria-label="Basic outlined example">
                                <a href="{{ url('flight-booking/status/change/'.$data->id.'/1') }}"
                                   type="button"
                                   class="btn btn-outline-secondary btn-sm {{ $data->status == 1 ? 'active' : '' }}">
                                   Pending
                                </a>
                    
                                <a href="{{ url('flight-booking/status/change/'.$data->id.'/2') }}"
                                   type="button"
                                   class="btn btn-outline-success btn-sm {{ $data->status == 2 ? 'active' : '' }}">
                                   Confirmed
                                </a>
                    
                                <button
                                   id="approveBtn"
                                   type="button"
                                   class="btn btn-outline-primary btn-sm {{ $data->status == 3 ? 'active' : '' }}">
                                   Booked
                                </button>
                    
                                <button type="button"
                                   id="cancelBtn"
                                   class="btn btn-outline-danger btn-sm {{ $data->status == 4 ? 'active' : '' }}">
                                   Cancelled
                                </button>
                            </div>
                    
                            {{-- Remarks box (initially hidden) --}}
                            <div class="mt-3" id="remarksBox" style="display: none;">
                                <form method="GET" action="{{ url('flight-booking/status/change/'.$data->id.'/4') }}">
                                    @csrf
                                    <div class="form-group">
                                        <label for="remarks">Remarks</label>
                                        <textarea name="remarks" class="form-control" rows="3" placeholder="Enter cancellation reason..."></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-danger btn-sm mt-2">Submit Cancellation</button>
                                </form>
                            </div>
                            
                            
                            
                            {{-- if booked --}}
                            
                            <div class="mt-3" id="pnrBox" style="display: none;">
                                <form method="GET" action="{{ url('flight-booking/status/change/'.$data->id.'/3') }}">
                                    @csrf
                                    <div class="form-group">
                                        <label for="remarks">Enter PNR</label>
                                        <input type="text" name="pnr" class="form-control" placeholder="Enter PNR No...">
                                    </div>
                                    <button type="submit" class="btn btn-danger btn-sm mt-2">Submit</button>
                                </form>
                            </div>
                        </div>
                    </div>
                                @php
                                        $travellers = explode(',', $data->traveller);
                                        $seatPreferences = explode(',', $data->seat_preference ?? '');
                                        $foodPreferences = explode(',', $data->food_preference ?? '');
                            
                                        $formattedTravellers = [];
                                        foreach ($travellers as $i => $traveller) {
                                            if (trim($traveller) !== '') {
                                                $formattedTravellers[] = [
                                                    'name' => trim($traveller),
                                                    'seat_preference' => $seatPreferences[$i] ?? 'N/A',
                                                    'food_preference' => $foodPreferences[$i] ?? 'N/A',
                                                ];
                                            }
                                        }
                                       // dd($formattedTravellers);
                                    @endphp
                    @endcan
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-3 col-lg-2 col-12"></div>
                            <div class="col-xl-6 col-lg-8 col-12">
                                <div class="table-responsive">
                                    <table class="table">
                                         <div class="user-info">
                                            <tr>
                                                <td class="text-muted">Unique Code: </td>
                                                <td>{{$data->order_no}}</td>
                                            </tr>
                                         </div>
                                        <tr>
                                            <td class="text-muted">Member: </td>
                                            <td><a href="{{ url('members/'.$data->user->id) }}">{{ $data->user->name }}</a></td>
                                        </tr>
                                        
                                        <tr>
                                            <td class="text-muted">From :  </td>
                                            <td>{{ $data->from }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">To : </td>
                                            <td>{{ $data->to }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Trip Type : </td>
                                            <td>{{ $data->trip_type == 1 ? 'One way' : ($data->trip_type == 2 ? 'Round Trip' : '') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Departure Date : </td>
                                            <td>{{ $data->departure_date }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Preffered Departure Time : </td>
                                            <td>{{ $data->arrival_time }}</td>
                                        </tr>
                                        
                                        <tr>
                                            <td class="text-muted">Return Date : </td>
                                            <td>{{ $data->trip_type == 2 ? ($data->return_date ?? '') : '' }}</td>
                                        </tr>
                                       @if (count($formattedTravellers) > 0)
                                            @foreach ($formattedTravellers as $key => $traveller)
                                              
                                        <tr>
                                            <td class="text-muted">Traveller : </td>
                                            <td>{{ $traveller['name'] ?? 'N/A'}}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Seat Preference : </td>
                                            <td>{{ $traveller['seat_preference'] ?? 'N/A'}}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Food Preference : </td>
                                            <td>{{ $traveller['food_preference'] ?? 'N/A'}}</td>
                                        </tr>
                                       
                                         @endforeach
                                         @endif
                                        <tr>
                                            <td class="text-muted">Bill to : </td>
                                            <td>{{ $data->bill_to == 1 ? 'Firm' : ($data->bill_to == 2 ? 'Third Party' : 'Matter Expenses') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Matter Code : </td>
                                            <td>{{ $data->matter_code }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">PNR : </td>
                                            <td>{{ $data->pnr }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Purpose/description : </td>
                                            <td>{{ $data->purpose_description ??'' }}</td>
                                        </tr>
                                       
                                        <tr>
                                            <td class="text-muted">Created At: </td>
                                            <td>{{ date('d-m-Y', strtotime($data->created_at)) }}</td>
                                        </tr>
                                        @if($data->status==4)
                                        <tr>
                                            <td class="text-muted">Cancellation Reason: </td>
                                            <td>{{ $data->cancellation_remarks }}</td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-2 col-12"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
                
@endsection


@section('script')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- printThis Plugin -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/printThis/1.15.0/printThis.min.js"></script>
<script>

   $(document).ready(function() {
            $('#basic').on('click', function() {
                $('#print-code').show();
                $('#print-code').printThis({
                importCSS: true,        // Import page CSS
                importStyle: true,      // Import style tags
                loadCSS: "",            // Load an additional CSS file
                pageTitle: "Books Info", // Title for the printed document
                removeInline: false,    // Keep the inline styles
                printDelay: 333,        // Delay before printing to allow images to load
                afterPrint: function() {
                    $('#print-code').hide(); // Hide the table again after printing
                }
            });
            });
        });
        
        
        function printQRCode(itemId,uid, qrText,bookNo) {
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
                        <img id="qr-code-img" src="${qrSrc}" style="height: 80px; width: 80px;">
                    </div>
                     <div class="uid-text">
                        <img style="width:190px" src="{{asset('backend/images/logo.png')}}">
                        <h2>${bookNo}</h2>
                        <h2 class="tt">${uid}</h2>
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
<script>
    const cancelBtn = document.getElementById('cancelBtn');
    const remarksBox = document.getElementById('remarksBox');

    cancelBtn.addEventListener('click', function () {
        remarksBox.style.display = 'block';
    });
    
    
     const approveBtn = document.getElementById('approveBtn');
    const pnrBox = document.getElementById('pnrBox');

    approveBtn.addEventListener('click', function () {
        pnrBox.style.display = 'block';
    });
</script>
@endsection