<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Vehicle;
use App\Models\VehicleType;
use App\Models\Destination;
use App\Models\PackageType;
use App\Models\User;
use Illuminate\Support\Facades\DB;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;



class ConsignmentNoteController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('admin.permission:manage lr_consignment', only: ['index']),
            new Middleware('admin.permission:create lr_consignment', only: ['create']),
            new Middleware('admin.permission:edit lr_consignment', only: ['edit']),
            new Middleware('admin.permission:delete lr_consignment', only: ['destroy']),
        ];
    }
   public function index(){
$orders = Order::whereJsonLength('lr', '>', 0)->get();



    return view('admin.consignments.index', compact('orders'));
   }

   public function create()
   {
   
   
    $vehicles = Vehicle::all();
    $vehiclesType = VehicleType::all();
    $destination = Destination::all();
    $package = PackageType::all();
    $users = User::all();
    return view('admin.consignments.create', compact('vehicles','users','vehiclesType','destination','package'));
    }
    
    
  




    
public function store(Request $request)
{
    $order = new Order();

    $order->order_id = 'ORD-' . time();
    $order->order_method = 'order';
    $order->byorder = $request->byOrder;

  
    $key = 1;

    // Cargo
    $cargoArray = [];
    if (isset($request->cargo) && is_array($request->cargo)) {
        foreach ($request->cargo as $cargo) {
            $documentFilePath = null;
            if (isset($cargo['document_file']) && is_object($cargo['document_file']) && $cargo['document_file']->isValid()) {
                $documentFile = $cargo['document_file'];
                $documentFilePath = $documentFile->store('orders/cargo_documents/', 'public');
            }

            $cargoArray[] = [
                'packages_no'         => $cargo['packages_no'] ?? null,
                'package_type'        => $cargo['package_type'] ?? null,
                'package_description' => $cargo['package_description'] ?? null,
                'actual_weight'       => $cargo['actual_weight'] ?? null,
                'charged_weight'      => $cargo['charged_weight'] ?? null,
                'document_no'         => $cargo['document_no'] ?? null,
                'document_name'       => $cargo['document_name'] ?? null,
                'document_date'       => $cargo['document_date'] ?? null,
                'eway_bill'           => $cargo['eway_bill'] ?? null,
                'valid_upto'          => $cargo['valid_upto'] ?? null,
                'declared_value'      => $cargo['declared_value'] ?? null,
                'unit'                => $cargo['unit'] ?? null,
                'document_file'       => $documentFilePath,
            ];
        }
    }

    // Vehicle
    $vehicleArray = [];
    if (isset($request->vehicle) && is_array($request->vehicle)) {
        foreach ($request->vehicle as $veh) {
            $vehicleArray[] = [
                'vehicle_no' => $veh['vehicle_no'] ?? null,
                'remarks'    => $veh['remarks'] ?? null,
            ];
        }
    }

    // Freight logic
    $freight_amount = $lr_charges = $hamali = $other_charges = $gst_amount = $total_freight = $less_advance = $balance_freight = null;
    if ($request->freightType !== 'to_be_billed') {
        $freight_amount = $request->freight_amount;
        $lr_charges = $request->lr_charges;
        $hamali = $request->hamali;
        $other_charges = $request->other_charges;
        $gst_amount = $request->gst_amount;
        $total_freight = $request->total_freight;
        $less_advance = $request->less_advance;
        $balance_freight = $request->balance_freight;
    }

    // LR Data with key
    $lrData = [
        'lr_number'           => $request->lr_number ?? ('LR-' . time() . '-' . $key),
        'lr_date'             => $request->lr_date,
        'vehicle_type'        => $request->vehicle_type,
        'vehicle_ownership'   => $request->vehicle_ownership,
        'delivery_mode'       => $request->delivery_mode,
        'from_location'       => $request->from_location,
        'to_location'         => $request->to_location,
        'insurance_description' => $request->insurance_description,
        'insurance_status'    => $request->insurance_status,
        'total_declared_value' => $request->total_declared_value,
        'order_rate'          => $request->order_rate,

        'consignor_id'        => $request->consignor_id,
        'consignor_gst'       => $request->consignor_gst,
        'consignor_loading'   => $request->consignor_loading,

        'consignee_id'        => $request->consignee_id,
        'consignee_gst'       => $request->consignee_gst,
        'consignee_unloading' => $request->consignee_unloading,

        'freightType'         => $request->freightType,
        'freight_amount'      => $freight_amount,
        'lr_charges'          => $lr_charges,
        'hamali'              => $hamali,
        'other_charges'       => $other_charges,
        'gst_amount'          => $gst_amount,
        'total_freight'       => $total_freight,
        'less_advance'        => $less_advance,
        'balance_freight'     => $balance_freight,

        'cargo'               => $cargoArray,
        'vehicle'             => $vehicleArray,
    ];

    // Store with key
    $order->lr = json_encode([$key => $lrData]);

    $order->save();

    return redirect()->route('admin.consignments.index')
        ->with('success', 'Single LR with multiple cargo stored successfully.');
}



public function edit($order_id, $lr_number)
{
  
    $order = Order::findOrFail($order_id);

    
   $lrEntriesArray = $order->lr;

if (is_string($lrEntriesArray)) {
   
    $lrEntries = json_decode($lrEntriesArray, true);
} else {
   
    $lrEntries = is_object($lrEntriesArray) ? (array) $lrEntriesArray : $lrEntriesArray;
}


    $lrData = null;

   
    if (is_array($lrEntries)) {
        foreach ($lrEntries as $key => $lr) {
            if (isset($lr['lr_number']) && $lr['lr_number'] == $lr_number) {
                $lrData = $lr;
                break;
            }
        }
    }

    // Agar aur data chahiye to wo bhi load karlo
    $vehicles = Vehicle::all();
    $users = User::all();
    $vehiclesType = VehicleType::all();
    $destination = Destination::all();
    $package = PackageType::all();


    return view('admin.consignments.edit', compact('order', 'lrData', 'vehicles', 'users', 'vehiclesType', 'destination', 'package'));
}



    public function update(Request $request, $order_id, $lr_number)
{
    $order = Order::where('order_id', $order_id)->firstOrFail();
    $order->order_method = 'order';
    $order->byorder = $request->byOrder;

    $cargoArray = [];

    if (isset($request->cargo) && is_array($request->cargo)) {
        foreach ($request->cargo as $cargo) {
            $documentFilePath = null;

            if (isset($cargo['document_file']) && $cargo['document_file'] instanceof \Illuminate\Http\UploadedFile && $cargo['document_file']->isValid()) {
                $documentFilePath = $cargo['document_file']->store('orders/cargo_documents/', 'public');
            } elseif (isset($cargo['old_document_file'])) {
                $documentFilePath = $cargo['old_document_file'];
            }

            $cargoArray[] = [
                'packages_no'         => $cargo['packages_no'] ?? null,
                'package_type'        => $cargo['package_type'] ?? null,
                'package_description' => $cargo['package_description'] ?? null,
                'declared_value'      => $cargo['declared_value'] ?? null,
                'actual_weight'       => $cargo['actual_weight'] ?? null,
                'charged_weight'      => $cargo['charged_weight'] ?? null,
                'unit'                => $cargo['unit'] ?? null,
                'document_no'         => $cargo['document_no'] ?? null,
                'document_name'       => $cargo['document_name'] ?? null,
                'document_date'       => $cargo['document_date'] ?? null,
                'eway_bill'           => $cargo['eway_bill'] ?? null,
                'valid_upto'          => $cargo['valid_upto'] ?? null,
                'document_file'       => $documentFilePath,
            ];
        }
    }

    $vehicleArray = [];
    if (isset($request->vehicle) && is_array($request->vehicle)) {
        $selectedIndex = $request->input('selected_vehicle');
        foreach ($request->vehicle as $index => $vehicle) {
            $vehicleArray[] = [
                'vehicle_no'  => $vehicle['vehicle_no'] ?? null,
                'remarks'     => $vehicle['remarks'] ?? null,
                'is_selected' => ((string)$index === (string)$selectedIndex),
            ];
        }
    }

    $freight_amount = $lr_charges = $hamali = $other_charges = $gst_amount = $total_freight = $less_advance = $balance_freight = null;

    if ($request->freightType !== 'to_be_billed') {
        $freight_amount = $request->freight_amount;
        $lr_charges = $request->lr_charges;
        $hamali = $request->hamali;
        $other_charges = $request->other_charges;
        $gst_amount = $request->gst_amount;
        $total_freight = $request->total_freight;
        $less_advance = $request->less_advance;
        $balance_freight = $request->balance_freight;
    }

   
   
    $existingLrs = is_string($order->lr) ? json_decode($order->lr, true) : $order->lr;


    // Replace the matching LR by lr_number
    foreach ($existingLrs as $key => $lr) {
        if ($lr['lr_number'] === $lr_number) {
            $existingLrs[$key] = [
                'lr_number'              => $lr_number,
                'lr_date'                => $request->lr_date,
                'vehicle_type'           => $request->vehicle_type,
                'vehicle_ownership'      => $request->vehicle_ownership,
                'delivery_mode'          => $request->delivery_mode,
                'from_location'          => $request->from_location,
                'to_location'            => $request->to_location,
                'insurance_status'       => $request->insurance_status,
                'insurance_description'  => $request->insurance_description,

                // Consignor
                'consignor_id'           => $request->consignor_id,
                'consignor_gst'          => $request->consignor_gst,
                'consignor_loading'      => $request->consignor_loading,

                // Consignee
                'consignee_id'           => $request->consignee_id,
                'consignee_gst'          => $request->consignee_gst,
                'consignee_unloading'    => $request->consignee_unloading,

                // Charges
                'freightType'            => $request->freightType,
                'freight_amount'         => $freight_amount,
                'lr_charges'             => $lr_charges,
                'hamali'                 => $hamali,
                'other_charges'          => $other_charges,
                'gst_amount'             => $gst_amount,
                'total_freight'          => $total_freight,
                'less_advance'           => $less_advance,
                'balance_freight'        => $balance_freight,
                'total_declared_value'   => $request->total_declared_value,
                'order_rate'             => $request->order_rate,

                // Nested cargo and vehicle
                'cargo'                  => $cargoArray,
                'vehicle'                => $vehicleArray,
            ];
            break; // Stop after updating the matching LR
        }
    }

    // Save updated LRs back
    $order->lr = json_encode($existingLrs);
    $order->save();

    return redirect()->route('admin.consignments.index')
        ->with('success', 'Order updated successfully for selected LR.');
}

    




public function show($order_id, $lr_number)
{
    $orders = DB::table('orders')->get();
    // dd($orders);

    foreach ($orders as $order) {
        $lrData = json_decode($order->lr, true);
        // dd($lrData);
       
        if (!is_array($lrData)) {
            $lrData = json_decode(json_decode($order->lr), true);
        }

       

        if (is_array($lrData)) {
            foreach ($lrData as $entry) {
                if (isset($entry['lr_number']) && $entry['lr_number'] == $lr_number) {
                    $lrEntries = $entry;

                    $vehicles = \App\Models\Vehicle::all();
                    $users = \App\Models\User::all();
                   $packageTypes = \App\Models\PackageType::all()->pluck('package_type', 'id')->toArray();

                    return view('admin.consignments.view', compact('packageTypes','orders', 'order', 'lrEntries', 'vehicles', 'users'));
                }
            }
        }
         

    }

    return redirect()->back()->with('error', 'LR Number not found.');
}


public function docView($order_id, $lr_number)
{
    $orders = DB::table('orders')->get();

    foreach ($orders as $order) {
        $lrData = json_decode($order->lr, true);

       
        if (!is_array($lrData)) {
            $lrData = json_decode(json_decode($order->lr), true);
        }

        // dd($lrData); 

        if (is_array($lrData)) {
            foreach ($lrData as $entry) {
                if (isset($entry['lr_number']) && $entry['lr_number'] == $lr_number) {
                    $lrEntries = $entry;
                    return view('admin.consignments.documents', compact( 'lrEntries'));
                }
            }
        }
    }

    return redirect()->back()->with('error', 'LR Number not found.');
}




   


   public function destroy($order_id, $lr_number)
{
    try {
        
        $order = Order::findOrFail($order_id);

        
        $lrEntriesArray = $order->lr;

        if (is_string($lrEntriesArray)) {
            $lrEntries = json_decode($lrEntriesArray, true);
        } else {
            $lrEntries = is_object($lrEntriesArray) ? (array) $lrEntriesArray : $lrEntriesArray;
        }

       
        if (!is_array($lrEntries) || empty($lrEntries)) {
            return redirect()->route('admin.consignments.index')
                ->with('error', 'No LR entries found for this Order.');
        }

        
        $filteredLrEntries = array_filter($lrEntries, function ($lr) use ($lr_number) {
            return isset($lr['lr_number']) && $lr['lr_number'] != $lr_number;
        });

       
        if (count($lrEntries) == count($filteredLrEntries)) {
            return redirect()->route('admin.consignments.index')
                ->with('error', 'No matching LR Number found to delete.');
        }

      
        $order->lr = json_encode(array_values($filteredLrEntries)); // reindex array
        $order->save();

        return redirect()->route('admin.consignments.index')
            ->with('success', 'LR entry deleted successfully.');
    } catch (\Exception $e) {
        return redirect()->route('admin.consignments.index')
            ->with('error', 'Error while deleting LR entry.');
    }
}

    
}

