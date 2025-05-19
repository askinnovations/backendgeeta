<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Order;
use App\Models\Vehicle;
use App\Models\VehicleType;
use App\Models\Destination;
use App\Models\PackageType;
use App\Models\User;
use App\Models\Contract;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;



class OrderController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('admin.permission:manage order_booking', only: ['index']),
            new Middleware('admin.permission:create order_booking', only: ['create']),
            new Middleware('admin.permission:edit order_booking', only: ['edit']),
            new Middleware('admin.permission:delete order_booking', only: ['destroy']),
        ];
    }
    public function index()
    {

        $orders = Order::latest()->get();
        //    dd($orders);

        return view('admin.orders.index', compact('orders'));
    }

    public function create()
    {
        $vehicles = Vehicle::all();
        $package = PackageType::all();
        $vehiclesType = VehicleType::all();
        $destination = Destination::all();
        $users = User::all();
        return view('admin.orders.create', compact('package', 'vehicles', 'users', 'vehiclesType', 'destination'));
    }

    public function edit($order_id)
    {
        $vehicles = Vehicle::all();
        $users = User::all();
        $order = Order::where('order_id', $order_id)->first();
        $vehiclesType = VehicleType::all();
        $destination = Destination::all();
        $package = PackageType::all();

        if (!empty($order) && isset($order->lr) && is_string($order->lr)) {
            $order->lr = json_decode($order->lr, true);
        }


        return view('admin.orders.edit', compact('package', 'order', 'vehicles', 'users', 'vehiclesType', 'destination'));
    }


    public function show($order_id)
    {
        $destination = Destination::all();
        $vehiclesType = VehicleType::all();
        $vehicles = Vehicle::all();
        $users = User::all();
        $package = PackageType::all();
        $order = Order::where('order_id', $order_id)->first();

        // Only decode if it's a string
        if (is_string($order->lr)) {
            $order->lr = json_decode($order->lr, true);
        }

        return view('admin.orders.view', compact('package', 'order', 'vehicles', 'users', 'vehiclesType','destination'));
    }
    public function docView($order_id)
    {

        $order = Order::where('order_id', $order_id)->first();

        // Only decode if it's a string
        if (is_string($order->lr)) {
            $order->lr = json_decode($order->lr, true);
        }

        return view('admin.orders.documents', compact('order'));
    }


    public function store(Request $request)
    {
        // return $request->all();
        $order = new Order();
        $order->order_id = 'ORD-' . time();
        $order->description = $request->description;
        $order->order_date = $request->order_date;
        // $order->status = $request->status;
        $order->order_type = $request->order_type;
        $order->cargo_description_type = $request->cargo_description_type;

        $order->customer_id = $request->customer_id;
        $order->customer_gst = $request->gst_number;
        $order->customer_address = $request->customer_address;

        $order->deleiver_addresss = $request->deleiver_addresss;
        $order->pickup_addresss = $request->pickup_addresss;
        $order->order_method = $request->order_method;
        $order->byorder = $request->byOrder;

        // 🔥 Default status chain
        // ✅ Default Status Chain
        $order->status = [
            ['status' => 'Material Collected', 'timestamp' => now()],
            ['status' => 'In Transit', 'timestamp' => null],
            ['status' => 'Delivered', 'timestamp' => null],
        ];


        // Prepare LR Data
        $lrArray = [];

        if (!empty($request->lr) && is_array($request->lr)) {
            foreach ($request->lr as $key => $lr) {
                $cargoArray = [];

                // Loop through each cargo inside LR
                if (isset($lr['cargo']) && is_array($lr['cargo'])) {
                    foreach ($lr['cargo'] as $cargo) {
                        $documentFilePath = null;

                        // Handle image file upload
                        if (isset($cargo['document_file']) && $cargo['document_file']->isValid()) {
                            $documentFile = $cargo['document_file'];
                            $documentFilePath = $documentFile->store('orders/cargo_documents/', 'public');
                        }

                        $cargoArray[] = [
                            'packages_no' => $cargo['packages_no'] ?? null,
                            'package_type' => $cargo['package_type'] ?? null,
                            'package_description' => $cargo['package_description'] ?? null,
                            'actual_weight' => $cargo['actual_weight'] ?? null,
                            'charged_weight' => $cargo['charged_weight'] ?? null,
                            'document_no' => $cargo['document_no'] ?? null,
                            'document_name' => $cargo['document_name'] ?? null,
                            'document_date' => $cargo['document_date'] ?? null,
                            'document_file' => $documentFilePath,
                            'declared_value' => $cargo['declared_value'] ?? null,
                            'eway_bill' => $cargo['eway_bill'] ?? null,
                            'valid_upto' => $cargo['valid_upto'] ?? null,
                            'unit' => $cargo['unit'] ?? null,
                        ];
                    }
                }

                // ✅ Handle Vehicle Data for this LR
                $vehicleArray = [];
                if (isset($lr['vehicle']) && is_array($lr['vehicle'])) {
                    foreach ($lr['vehicle'] as $veh) {
                        $vehicleArray[] = [
                            'vehicle_no' => $veh['vehicle_no'] ?? null,
                            'remarks' => $veh['remarks'] ?? null,
                        ];
                    }
                }

                $lrArray[$key] = [
                    'lr_number' => $lr['lr_number'] ?? ('LR-' . time() . '-' . $key),
                    'lr_date' => $lr['lr_date'] ?? null,
                    'vehicle_type' => $lr['vehicle_type'] ?? null,
                    'vehicle_ownership' => $lr['vehicle_ownership'] ?? null,
                    'delivery_mode' => $lr['delivery_mode'] ?? null,
                    'from_location' => $lr['from_location'] ?? null,
                    'to_location' => $lr['to_location'] ?? null,

                    // Consignor
                    'consignor_id' => $lr['consignor_id'] ?? null,
                    'consignor_gst' => $lr['consignor_gst'] ?? null,
                    'consignor_loading' => $lr['consignor_loading'] ?? null,

                    // Consignee
                    'consignee_id' => $lr['consignee_id'] ?? null,
                    'consignee_gst' => $lr['consignee_gst'] ?? null,
                    'consignee_unloading' => $lr['consignee_unloading'] ?? null,

                    // Charges
                    'freightType' => $lr['freightType'] ?? null,
                    'freight_amount' => $lr['freight_amount'] ?? null,
                    'lr_charges' => $lr['lr_charges'] ?? null,
                    'hamali' => $lr['hamali'] ?? null,
                    'other_charges' => $lr['other_charges'] ?? null,
                    'gst_amount' => $lr['gst_amount'] ?? null,
                    'total_freight' => $lr['total_freight'] ?? null,
                    'less_advance' => $lr['less_advance'] ?? null,
                    'balance_freight' => $lr['balance_freight'] ?? null,
                    'total_declared_value' => $lr['total_declared_value'] ?? null,
                    'order_rate' => $lr['order_rate'] ?? 0,
                    'insurance_description' => $lr['insurance_description'] ?? null,
                    'insurance_status' => $lr['insurance_status'] ?? null,
                    // Nested cargo
                    'cargo' => $cargoArray,
                    'vehicle' => $vehicleArray,
                ];
            }
        }


        $order->lr = $lrArray ?? [];

        $order->save();
        // dd($order);

        return redirect()->route('admin.orders.index')
            ->with('success', 'Order stored with nested LR and cargo arrays successfully!');
    }



    public function getRate(Request $request)
    {



        $rate = Contract::where('user_id', $request->customer_id)
            ->where('type_id', $request->vehicle_type)
            ->where('from_destination_id', $request->from_location)
            ->where('to_destination_id', $request->to_location)
            ->value('rate');

        if ($rate) {
            return response()->json([
                'rate' => $rate,
            ]);
        } else {
            return response()->json([
                'rate' => null,
            ]);
        }
    }




    public function update(Request $request, $order_id)
    {
        // Find the existing order by order_id
        $order = Order::where('order_id', $order_id)->firstOrFail();

        // Update order attributes
        $order->order_id = $request->order_id;
        $order->description = $request->description;
        $order->order_date = $request->order_date;
        // $order->status = $request->status;
        $order->order_type = $request->order_type;
        $order->cargo_description_type = $request->cargo_description_type;
        $order->customer_id = $request->customer_id;
        $order->customer_gst = $request->gst_number;
        $order->customer_address = $request->customer_address;
        $order->deleiver_addresss = $request->deleiver_addresss;
        $order->pickup_addresss = $request->pickup_addresss;
        $order->order_method = $request->order_method;
        $order->byorder = $request->byOrder;
        $order->bycontract = $request->byContract;

        // Prepare LR Data
        $lrArray = [];
        foreach ($request->lr as $key => $lr) {
            $cargoArray = [];
            $vehicleArray = [];

            // Handle nested cargo
            if (isset($lr['cargo']) && is_array($lr['cargo'])) {
                foreach ($lr['cargo'] as $cargo) {
                    $documentFilePath = null;

                    if (isset($cargo['document_file']) && $cargo['document_file'] instanceof UploadedFile && $cargo['document_file']->isValid()) {
                        $documentFile = $cargo['document_file'];
                        $documentFilePath = $documentFile->store('orders/cargo_documents/', 'public');
                    }

                    $cargoArray[] = [
                        'packages_no' => $cargo['packages_no'] ?? null,
                        'package_type' => $cargo['package_type'] ?? null,
                        'package_description' => $cargo['package_description'] ?? null,
                        'actual_weight' => $cargo['actual_weight'] ?? null,
                        'charged_weight' => $cargo['charged_weight'] ?? null,
                        'document_no' => $cargo['document_no'] ?? null,
                        'document_name' => $cargo['document_name'] ?? null,
                        'document_date' => $cargo['document_date'] ?? null,
                        'document_file' => $documentFilePath,
                        'declared_value' => $cargo['declared_value'] ?? null,
                        'eway_bill' => $cargo['eway_bill'] ?? null,
                        'valid_upto' => $cargo['valid_upto'] ?? null,
                        'unit' => $cargo['unit'] ?? null,
                    ];
                }
            }

            // ✅ Handle nested vehicles
            // ✅ Handle nested vehicles
            if (isset($lr['vehicle']) && is_array($lr['vehicle'])) {
                $selectedVehicleIndex = $lr['selected_vehicle'] ?? null;

                foreach ($lr['vehicle'] as $vehicleIndex => $vehicle) {
                    $vehicleArray[] = [
                        'vehicle_no' => $vehicle['vehicle_no'] ?? null,
                        'remarks' => $vehicle['remarks'] ?? null,
                        'is_selected' => ($selectedVehicleIndex !== null && (int) $selectedVehicleIndex === (int) $vehicleIndex),
                    ];
                }
            }


            // Charges
            $freight_amount = $lr_charges = $hamali = $other_charges = $gst_amount = $total_freight = $less_advance = $balance_freight = null;

            if (($lr['freightType'] ?? null) !== 'to_be_billed') {
                $freight_amount = $lr['freight_amount'] ?? null;
                $lr_charges = $lr['lr_charges'] ?? null;
                $hamali = $lr['hamali'] ?? null;
                $other_charges = $lr['other_charges'] ?? null;
                $gst_amount = $lr['gst_amount'] ?? null;
                $total_freight = $lr['total_freight'] ?? null;
                $less_advance = $lr['less_advance'] ?? null;
                $balance_freight = $lr['balance_freight'] ?? null;
            }

            $lrArray[$key] = [
                'lr_number' => $lr['lr_number'] ?? ('LR-' . time() . '-' . $key),
                'lr_date' => $lr['lr_date'] ?? null,
                'vehicle_no' => $lr['vehicle_no'] ?? null,
                'vehicle_type' => $lr['vehicle_type'] ?? null,
                'vehicle_ownership' => $lr['vehicle_ownership'] ?? null,
                'delivery_mode' => $lr['delivery_mode'] ?? null,
                'from_location' => $lr['from_location'] ?? null,
                'to_location' => $lr['to_location'] ?? null,
                'consignor_id' => $lr['consignor_id'] ?? null,
                'consignor_gst' => $lr['consignor_gst'] ?? null,
                'consignor_loading' => $lr['consignor_loading'] ?? null,
                'consignee_id' => $lr['consignee_id'] ?? null,
                'consignee_gst' => $lr['consignee_gst'] ?? null,
                'consignee_unloading' => $lr['consignee_unloading'] ?? null,
                'freightType' => $lr['freightType'] ?? null,
                'freight_amount' => $freight_amount,
                'lr_charges' => $lr_charges,
                'hamali' => $hamali,
                'other_charges' => $other_charges,
                'gst_amount' => $gst_amount,
                'total_freight' => $total_freight,
                'less_advance' => $less_advance,
                'balance_freight' => $balance_freight,
                'total_declared_value' => $lr['total_declared_value'] ?? null,
                'insurance_description' => $lr['insurance_description'] ?? null,
                'insurance_status' => $lr['insurance_status'] ?? null,
                'order_rate' => $lr['order_rate'] ?? null,
                'cargo' => $cargoArray,
                'vehicle' => $vehicleArray,
            ];
        }


        // Update LR data
        $order->lr = $lrArray ?? [];
        $order->save();

        return redirect()->route('admin.orders.index')
            ->with('success', 'Order updated with nested LR and cargo arrays successfully!');
    }












    public function destroy($order_id)
    {
        // Get all orders with the same order_id
        $orders = Order::where('order_id', $order_id)->get();

        if ($orders->isEmpty()) {
            return response()->json(['status' => 'error', 'message' => 'No entries found for this order_id.'], 404);
        }

        try {
            // Delete all related LRs
            foreach ($orders as $order) {
                $order->delete();
            }
  return redirect()->route('admin.orders.index')
            ->with('success', 'All entries under this Order ID deleted successfully');
        } catch (\Exception $e) {
              return redirect()->route('admin.orders.index')
            ->with('success', 'Error while deleting entries!');
           
        }
    }



    public function updateStatus(Request $request, $order_id)
    {
        // order_id से order ढूंढें
        $order = Order::where('order_id', $order_id)->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.'
            ], 404);
        }

      
        $currentStatusArray = is_array($order->status) ? $order->status : json_decode($order->status, true);

        if (!is_array($currentStatusArray)) {
            $currentStatusArray = [];
        }

      
        $currentStatusArray[] = [
            'status' => $request->status,
            'timestamp' => now()->toDateTimeString(),
        ];

       
        $order->status = json_encode($currentStatusArray);
        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully.',
            'status_history' => $currentStatusArray, 
        ]);
    }



}