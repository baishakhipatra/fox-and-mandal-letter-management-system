<?php

namespace App\Http\Controllers\Facility;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CabBooking;
use Illuminate\View\View; 
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use DB;
use Auth;
class CabBookingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
         $this->middleware('permission:view cab booking|cab booking list csv export', ['only' => ['index']]);
         $this->middleware('permission:cab booking detail|cab booking status update', ['only' => ['show']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
  public function index(Request $request): View
{
    // Capture inputs
    $keyword = $request->input('keyword');
    $issueDateFrom = $request->input('date_from');
    $issueDateTo = $request->input('date_to');
    $billTo = $request->input('bill_to');
    // Start the query
    $query = CabBooking::latest('id');
    
    // Apply the keyword search conditions
    if (!empty($keyword)) {
        $query->where(function ($query) use ($keyword) {
            $query->where('matter_code', 'LIKE', "%$keyword%")
                  ->orWhere('from_location', 'LIKE', "%$keyword%")
                  ->orWhere('to_location', 'LIKE', "%$keyword%")
                  ->orWhere('pickup_date', 'LIKE', "%$keyword%")
                  ->orWhere('pickup_time', 'LIKE', "%$keyword%")
                  ->orWhere('traveller', 'LIKE', "%$keyword%")
                  ->orWhere('bill_to', 'LIKE', "%$keyword%")
                  ->orWhereHas('user', function ($query) use ($keyword) {
                      $query->where('name', 'LIKE', "%$keyword%");
                  });
        });
    }

    // Apply the bill_to filter based on the keyword input (company, client, etc.)
    if (!empty($billTo)) {
        $query->where('bill_to', $billTo);
    }

    // Apply date range filter if both are provided
    if (!empty($issueDateFrom) && !empty($issueDateTo)) {
        $query->whereBetween('pickup_date', [
            Carbon::parse($issueDateFrom)->startOfDay(),
            Carbon::parse($issueDateTo)->endOfDay()
        ]);
    } 
    // Apply date filter if only 'date_from' is provided
    elseif (!empty($issueDateFrom)) {
        $query->whereDate('pickup_date', '>=', Carbon::parse($issueDateFrom)->startOfDay());
    } 
    // Apply date filter if only 'date_to' is provided
    elseif (!empty($issueDateTo)) {
        $query->whereDate('pickup_date', '<=', Carbon::parse($issueDateTo)->endOfDay());
    }

    // Execute the query and paginate results
    $data = $query->paginate(25);
     
    // Return the view with data
    return view('facility.cab-booking.index', compact('data', 'request'))
        ->with('i', (request()->input('page', 1) - 1) * 5);
}


 public function csvExport(Request $request)
{
		 // Capture inputs
    $keyword = $request->input('keyword');
    $issueDateFrom = $request->input('date_from');
    $issueDateTo = $request->input('date_to');
    $billTo = $request->input('bill_to');
    // Start the query
    $query = CabBooking::latest('id');
    
    // Apply the keyword search conditions
    if (!empty($keyword)) {
        $query->where(function ($query) use ($keyword) {
            $query->where('matter_code', 'LIKE', "%$keyword%")
                  ->orWhere('from_location', 'LIKE', "%$keyword%")
                  ->orWhere('to_location', 'LIKE', "%$keyword%")
                  ->orWhere('pickup_date', 'LIKE', "%$keyword%")
                  ->orWhere('pickup_time', 'LIKE', "%$keyword%")
                  ->orWhere('traveller', 'LIKE', "%$keyword%")
                  ->orWhere('bill_to', 'LIKE', "%$keyword%")
                  ->orWhereHas('user', function ($query) use ($keyword) {
                      $query->where('name', 'LIKE', "%$keyword%");
                  });
        });
    }

    // Apply the bill_to filter based on the keyword input (company, client, etc.)
    if (!empty($billTo)) {
        $query->where('bill_to', $billTo);
    }

    // Apply date range filter if both are provided
    if (!empty($issueDateFrom) && !empty($issueDateTo)) {
        $query->whereBetween('pickup_date', [
            Carbon::parse($issueDateFrom)->startOfDay(),
            Carbon::parse($issueDateTo)->endOfDay()
        ]);
    } 
    // Apply date filter if only 'date_from' is provided
    elseif (!empty($issueDateFrom)) {
        $query->whereDate('pickup_date', '>=', Carbon::parse($issueDateFrom)->startOfDay());
    } 
    // Apply date filter if only 'date_to' is provided
    elseif (!empty($issueDateTo)) {
        $query->whereDate('pickup_date', '<=', Carbon::parse($issueDateTo)->endOfDay());
    }

    // Execute the query and paginate results
        $data = $query->cursor();
        $book = $data->all();
        if (count($book) > 0) {
            $delimiter = ","; 
            $filename = "cab boking request.csv"; 

            // Create a file pointer 
            $f = fopen('php://memory', 'w'); 

            // Set column headers 
            // $fields = array('SR', 'QRCODE TITLE','CODE','DISTRIBUTOR','ASE','STORE NAME','STORE MOBILE','STORE EMAIL','STORE STATE','STORE ADDRESS','POINTS','DATE'); 
            $fields = array('SR','Unique Code', 'Member','From','To','Pickup Date & Time','Traveller','Bill to','Matter Code','Purpose Description','Creation Date'); 
            fputcsv($f, $fields, $delimiter); 

            $count = 1;

            foreach($book as $row) {
                $datetime = date('j F, Y h:i A', strtotime($row['created_at']));
				

                $lineData = array(
                    $count,
                    $row->order_no,
					$row['user']['name'] ?? 'NA',
                    $row['from_location'] ?? 'NA',
					$row->to_location ?? 'NA',
					$row->pickup_date . ' ' . $row->pickup_time ?? 'NA',
					$row->traveller ?? 'NA',
					$row->bill_to == 1 ? 'Firm' : ($row->bill_to == 2 ? 'Third Party' : 'Matter Expenses') ?? 'NA',
					$row->matter_code ?? 'NA',
					$row->purpose_description ?? 'NA',
					$datetime,
                );

                fputcsv($f, $lineData, $delimiter);

                $count++;
            }

            // Move back to beginning of file
            fseek($f, 0);

            // Set headers to download file rather than displayed
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . $filename . '";');

            //output all remaining data on a file pointer
            fpassthru($f);
        }
	}
	
	
	public function show(Request $request,$id): View
	{
	    
	    $data=CabBooking::where('id',$id)->first();
	    return view('facility.cab-booking.view', compact('data', 'request'));
	}
	
	
	public function status(Request $request,$id,$status)
    {
		//dd($request->status);
		$booking = CabBooking::findOrFail($id);
		 if ($booking->status == 4) {
            return redirect()->back()->with('failure', 'This booking has already been cancelled and cannot be updated.');
        }
         if($booking->status == 3 && $status != 4){
            return redirect()->back()->with('failure', 'Cab has already been booked and can only be cancelled.');
        }
		if($status==4){
		    $now = Carbon::now();
		    $updatedEntry = CabBooking::findOrFail($id);
		    $pickupDateTime = Carbon::parse($updatedEntry['pickup_date'] . ' ' . $updatedEntry['pickup_time']);

            if ($now->greaterThan($pickupDateTime->copy()->subHours(6))) {
                return redirect()->back()
                                ->with('failure','cancellations must be made at least 6 hours before the pickup time.');
                
            }else{
                $request->validate([
                    'remarks' => 'required|string|max:1000',
                ]);
        
                $booking->status = 4;
                $booking->cancellation_remarks = $request->remarks; // Make sure 'remarks' column exists in DB
                $booking->save();
            
                return redirect()->back()->with('success', 'Booking has been cancelled with remarks.');
            }
		}else{
		    
		
            $updatedEntry = CabBooking::findOrFail($id);
            
            $updatedEntry->status = $status;
            $updatedEntry->save();
            return redirect()->back()->with('success', 'Booking status updated');
		}
      
    }
    
    
    public function edit(Request $request,$id): View
	{
	    
	    $data=CabBooking::where('id',$id)->first();
	    return view('facility.cab-booking.edit', compact('data', 'request'));
	}
	
	
	 public function update(Request $request,$id)
    {
        
    $orderData = CabBooking::findOrFail($id);
    if (!$orderData) {
        return redirect()->back()
                        ->with('failure','Booking Data not found');
    }
    $now = Carbon::now();
    $currentHour = (int)$now->format('H');

    $today = Carbon::today();
    $pickupDate = Carbon::parse($orderData->pickup_date);
    if ($pickupDate->lessThan($today)) {
        return redirect()->back()->with('failure', 'Booking cannot be edited. Pickup date is before today.');
    }
    // ⛔ Restrict edits/cancellations less than 6 hours before pickup time
    $pickupDateTime = Carbon::parse($orderData['pickup_date'] . ' ' . $orderData['pickup_time']);

    if ($now->greaterThan($pickupDateTime->copy()->subHours(6))) {
        return redirect()->back()
                        ->with('failure','Edits or cancellations must be made at least 6 hours before the pickup time.');
        
    }

    if ($orderData->status==4) {
        return redirect()->back()
                        ->with('failure','Booking already cancelled, cannot be edited.');
    }

    // ✅ Update flow
   $newData = [
        'bill_to' => $request['bill_to'],
        'from_location' => $request['from_location'],
        'to_location' => $request->to_location ?? null,
        'pickup_date' => date('d-m-Y', strtotime($request['pickup_date'])),
        'pickup_time' => $request['pickup_time'],
        'matter_code' => $request->matter_code ?? null,
        'traveller' => is_array($request->traveller) ? implode(',', $request->traveller) : null,
        'purpose_description' => $request->purpose_description ?? null,
       'updated_at' => now()
    ];
    
     // Compare and insert into log
    foreach ($newData as $field => $newValue) {
        $oldValue = $orderData->$field ?? null;

        if ($newValue != $oldValue) {
            DB::table('edit_logs')->insert([
                'table_name' => 'cab_bookings',
                'record_id' => $request->id,
                'field' => $field,
                'old_value' => $oldValue,
                'new_value' => $newValue,
                'updated_by' => Auth::user()->id,
                'created_at' => now()
            ]);
        }
    }

    DB::table('cab_bookings')->where('id', $id)->update($newData);
     return redirect()->back()
                        ->with('success','Cab updated successfully.');
    
    }

}
