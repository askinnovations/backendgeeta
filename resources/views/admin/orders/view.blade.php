@extends('admin.layouts.app')
@section('title', 'Employees | KRL')
@section('content')
<div class="page-content">
<div class="container-fluid">
   <!-- start page title -->
   <div class="row">
      <div class="col-12">
         <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">View Order Details</h4>
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show auto-dismiss" role="alert">
               {{ session('success') }}
               <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show auto-dismiss" role="alert">
               {{ session('error') }}
               <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
            <div class="page-title-right">
               <ol class="breadcrumb m-0">
                  <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                  <li class="breadcrumb-item active">View Order</li>
               </ol>
            </div>
         </div>
      </div>
   </div>
   <!-- end page title -->
   <!-- Tyre Listing Page -->
   <div class="row listing-form">
      <div class="col-12">
         <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
               <div>
                  <h4 class="card-title">🛞 View Order </h4>
                  <p class="card-title-desc">
                     View, edit, or delete permissions details below. This table supports search,
                     sorting, and pagination via DataTables.
                  </p>
               </div>
            </div>
            <div class="card-body">
               <form method="POST" action="{{ route('admin.orders.update', $order->order_id) }}">
                  @csrf
                  <div class="card">
                     <div class="card-header">
                        <h4>View  Order</h4>
                     </div>
                     <div class="card-body">
                        <div class="row">
                           <div class="col-md-3">
                              <label>📌 Order ID</label>
                              <input type="text" name="order_id" class="form-control"
                                 value="{{ $order->order_id }}" readonly required>
                           </div>
                           <div class="col-md-3">
                              <label>📝 Description</label>
                              <textarea name="description" class="form-control" readonly>{{ $order->description }}</textarea required>
                           </div>
                           <div class="col-md-3">
                              <label>📅 Date</label>
                              <input type="date" name="order_date" class="form-control" value="{{ $order->order_date }}" required readonly>
                           </div>
                           <div class="col-md-3">
                              <label>📊 Status</label>
                              <select name="status" class="form-select" required readonly>
                              <option value="Pending" {{ $order->status == 'Pending' ? 'selected' : '' }}>Pending</option>
                              <option value="Processing" {{ $order->status == 'Processing' ? 'selected' : '' }}>Processing</option>
                              <option value="Completed" {{ $order->status == 'Completed' ? 'selected' : '' }}>Completed</option>
                              <option value="Cancelled" {{ $order->status == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                              </select>
                           </div>
                           <!-- CUSTOMER NAME DROPDOWN -->
                           <div class="col-md-3">
                              <div class="mb-3">
                                 <label class="form-label">👤 CUSTOMER NAME</label>
                                 <input list="customers" name="customer_name" id="customer_name" class="form-control" onchange="setCustomerDetails()" value="{{ $order->customer->name ?? '' }}" readonly>
                                 <datalist id="customers">
                                    @foreach($users as $user)
                                    @php
                                    $addresses = json_decode($user->address, true);
                                    $formattedAddress = '';
                                    if (!empty($addresses) && is_array($addresses)) {
                                    $first = $addresses[0];
                                    $formattedAddress = trim(($first['full_address'] ?? '') . ', ' . ($first['city'] ?? '') . ', ' . ($first['pincode'] ?? ''));
                                    }
                                    @endphp
                                    <option 
                                       value="{{ $user->name }}"
                                       data-id="{{ $user->id }}"
                                       data-gst="{{ $user->gst_number }}"
                                       data-address="{{ $formattedAddress }}">
                                       @endforeach
                                 </datalist>
                              </div>
                           </div>
                           <!-- GST NUMBER -->
                           <div class="col-md-3">
                           <div class="mb-3">
                           <label class="form-label">🧾 GST NUMBER</label>
                           <input type="text" name="gst_number" id="gst_number" class="form-control" value="{{ old('gst_number', isset($order) ? $order->customer_gst : '') }}" readonly required>
                           </div>
                           </div>
                           <!-- CUSTOMER ADDRESS -->
                           <div class="col-md-3">
                           <div class="mb-3">
                           <label class="form-label">📍 CUSTOMER ADDRESS</label>
                           <input type="text" name="customer_address" id="customer_address" class="form-control" value="{{ old('customer_address', isset($order) ? $order->customer_address : '') }}" readonly required>
                           </div>
                           </div>
                           <!-- ORDER TYPE -->
                           <div class="col-md-3">
                           <div class="mb-3">
                           <label class="form-label">📊 Order Type</label>
                           <input type="text"    value="{{ $order->order_type ?? ''}}"  class="form-control" readonly >
                           </div>
                           </div>
                           <div class="col-md-3">
                           <div class="mb-3">
                           <label class="form-label">📍 PICKUP ADDRESS</label>
                           <input type="text" name="pickup_addresss" id="pickup_addresss" value="{{ old('pickup_addresss', isset($order) ? $order->pickup_addresss : '') }}" class="form-control"  placeholder="Pickup Addresss" readonly>
                           </div>
                           </div>
                           <!-- DEliver adddress   -->
                           <div class="col-md-3">
                           <div class="mb-3">
                           <label class="form-label">📍DELEIEIVER ADDRESS</label>
                           <input type="text" name="deleiver_addresss" id="deleiver_addresss" class="form-control" value="{{ old('deleiver_addresss', isset($order) ? $order->deleiver_addresss : '') }}"  placeholder="Deleiver Addresss" readonly>
                           </div>
                           </div>
                           @php
                           $method = old('order_method', $order->order_method ?? '');
                           $orderAmount = old('order_amount', $order->byorder ?? '');
                           $contractNumber = old('contract_number', $order->bycontract ?? '');
                           @endphp
                           {{-- @dd($order); --}}
                           <div class="col-md-3">
                           <div class="mb-3">
                           <label class="form-label">📑 Order Method</label><br>
                           <input type="text"  
                              value="{{ $order->order_method ? ucwords(str_replace('_', ' ', $order->order_method)) : '' }}" 
                              class="form-control" 
                              readonly 
                              placeholder=" Vehicle Ownership">
                           </div>
                           </div>
                           <!-- lr  -->
                           @php
                           $lrData = is_array($order->lr) ? $order->lr : json_decode($order->lr, true);
                           @endphp
                           @foreach($lrData as $index => $lr)
                           <div class="row mt-4" id="lr-section">
                           <!-- <h4 style="margin-bottom: 2%;">🚚 Update LR - Consignment Details</h4> -->
                           <div class="row g-3 mb-3 single-lr-row">
                           <div class="col-md-3">
                           <label class="form-label">Lr Number</label>
                           <input required
                              type="text" 
                              class="form-control" 
                              placeholder="Enter lr number" 
                              value="{{ $lr['lr_number'] ?? '' }}" readonly>
                           </div>
                           <div class="col-md-3">
                           <label class="form-label">Lr Date</label>
                           <input 
                              type="date" 
                              class="form-control" 
                              placeholder="Enter lr number" 
                              value="{{ $lr['lr_date'] ?? '' }}" readonly>
                           </div>
                           <!-- Consignor Dropdown -->
                           <div class="col-md-3">
                           <label class="form-label">🚚 Consignor Name</label>
                           @foreach($users as $user)
                           @if(($lr['consignor_id'] ?? '') == $user->id)
                           <input 
                              type="text" 
                              class="form-control" 
                              placeholder="Enter lr number" 
                              value="{{ $user->name ?? '' }}" 
                              readonly>
                           </div>
                           @endif
                           @endforeach
                           <!-- GST Field -->
                           <div class="col-md-3">
                           <label class="form-label">🧾 Consignor GST</label>
                           <input type="text" readonly
                              value="{{ $lr['consignor_gst'] ?? '' }}" 
                              class="form-control" readonly required>
                           </div>
                           <!-- Address Field -->
                           <div class="col-md-3">
                           <label class="form-label">📍 Loading Address</label>
                           <input type="text" readonly
                              value="{{ $lr['consignor_loading'] ?? ''}}" 
                              class="form-control" readonly required>
                           </div>
                           <!-- Consignee Name -->
                           <div class="col-md-3">
                           <label class="form-label">🏢 Consignee Name</label>
                           @foreach($users as $user)
                           @if(($lr['consignee_id'] ?? '') == $user->id)
                           <input 
                              type="text" 
                              class="form-control" 
                              placeholder="Enter lr number" 
                              value="{{ $user->name ?? '' }}" 
                              readonly>
                           </div>
                           @endif
                           @endforeach
                           <!-- Consignee GST -->
                           <div class="col-md-3">
                           <label class="form-label">🧾 Consignee GST</label>
                           <input type="text"  readonly
                           value="{{ old("lr.$index.consignee_gst", $lr['consignee_gst'] ?? '') }}" 
                           class="form-control" readonly >
                           </div>
                           <!-- Consignee Unloading Address -->
                           <div class="col-md-3">
                           <label class="form-label">📍 Unloading Address</label>
                           <input type="text" 
                           value="{{ old("lr.$index.consignee_unloading", $lr['consignee_unloading'] ?? '') }}" 
                           class="form-control" readonly >
                           </div>
                           </div>
                           @php
                           $selectedType = collect($vehiclesType)->firstWhere('id', old("lr.$index.vehicle_type", $lr['vehicle_type'] ?? ''));
                           @endphp
                           <div class="col-md-4">
                           <label class="form-label">🚛 Vehicle Type</label>
                           <input type="text"  
                              value="{{ $selectedType->vehicletype ?? '' }}" 
                              class="form-control" 
                              readonly 
                              placeholder="Select vehicle type">
                           </div>
                           {{-- <div class="row">
                           <!-- LR Date -->
                           <div class="col-md-4">
                           <label class="form-label">🚛 Vehicle Number</label>
                           <input type="text"  value="{{ $lr['vehicle_no'] ?? '' }}" 
                              class="form-control" readonly placeholder=" select vehicle no" >
                           </div>
                           --}}
                           <!-- Vehicle Ownership -->
                           <div class="col-md-4">
                           <div class="mb-3">
                           <label class="form-label">🚢 Delivery Mode</label>
                           <input type="text"  
                              value="{{ isset($lr['delivery_mode']) ? ucwords(str_replace('_', ' ', $lr['delivery_mode'])) : '' }}" 
                              class="form-control" 
                              readonly 
                              placeholder="Select Mode"></div>
                           </div>
                           <div class="col-md-4">
                           <label class="form-label">🛻 Vehicle Ownership</label>
                           <input type="text"  
                              value="{{ isset($lr['vehicle_ownership']) ? ucwords(str_replace('_', ' ', $lr['vehicle_ownership'])) : '' }}" 
                              class="form-control" 
                              readonly 
                              placeholder=" Vehicle Ownership">
                           </div>
                           </div>
                           <div class="row">
                           <!-- Delivery Mode -->
                           <!-- From Location -->
                           <div class="col-md-4">
                           @php
                           $selectedFrom = collect($destination)->firstWhere('id', old("lr.$index.from_location", $lr['from_location'] ?? ''));
                           @endphp
                           <div class="mb-3">
                           <label class="form-label">📍 From (Origin)</label>
                           <input type="text"  
                              value="{{ $selectedFrom->destination ?? '' }}" 
                              class="form-control"  
                              readonly 
                              placeholder="Select Origin">
                           </div>
                           </div>
                           <!-- To Location -->
                           <div class="col-md-4">
                           @php
                           $selectedTo = collect($destination)->firstWhere('id', old("lr.$index.to_location", $lr['to_location'] ?? ''));
                           @endphp
                           <div class="mb-3">
                           <label class="form-label">📍 To (Destination)</label>
                           <input type="text"  
                              value="{{ $selectedTo->destination ?? '' }}" 
                              class="form-control"  
                              readonly 
                              placeholder="Select Destination">
                           </div>
                           </div>
                           </div>
                           <div class="row mt-4">
                           @php
                           $lrIndex = $loop->index ?? 0;
                           $cargoData = isset($lr['cargo']) && is_array($lr['cargo'])
                           ? collect($lr['cargo'])->filter(fn($item) => isset($item['packages_no']) && $item['packages_no'] !== null)->values()
                           : collect();
                           @endphp
                           <div class="col-12" data-lr-index="{{ $lrIndex }}">
                           <h5 class="mb-3 pb-3">📦 Cargo Description (LR #{{ $lrIndex + 1 }})</h5>
                           <div class="table-responsive">
                           <table class="table table-bordered align-middle text-center">
                           <thead>
                           <tr>
                           <th>No. of Packages</th>
                           <th>Packaging Type</th>
                           <th>Description</th>
                           <th>Actual Weight (kg)</th>
                           <th>Charged Weight (kg)</th>
                           <th>&nbsp;Unit&nbsp;&nbsp;</th>
                           <th>Document No.</th>
                           <th>Document Name</th>
                           <th>Document Date</th>
                           <th>Eway Bill</th>
                           <th>Valid Upto</th>
                           <th>Declared value</th>
                           <th>Action</th>
                           </tr>
                           </thead>
                           <tbody id="cargoTableBody-{{ $lrIndex }}">
                           @foreach ($cargoData as $cargoIndex => $cargo)
                           <tr>
                           <td><input type="number" name="lr[{{ $lrIndex }}][cargo][{{ $cargoIndex }}][packages_no]" class="form-control" value="{{ $cargo['packages_no'] }}" required readonly></td>
                           @php
                           $selectedPackageType = collect($package)->firstWhere('id', $cargo['package_type'] ?? '');
                           @endphp
                           <td>
                           <input type="text" 
                              value="{{ $selectedPackageType->package_type ?? '' }}" 
                              class="form-control" 
                              readonly 
                              placeholder="Select Package Type">
                           </td>
                           <td><input type="text" name="lr[{{ $lrIndex }}][cargo][{{ $cargoIndex }}][package_description]" class="form-control" value="{{ $cargo['package_description'] }}" required readonly></td>
                           <td><input type="number" name="lr[{{ $lrIndex }}][cargo][{{ $cargoIndex }}][actual_weight]" class="form-control" value="{{ $cargo['actual_weight'] }}" required readonly></td>
                           <td><input type="number" name="lr[{{ $lrIndex }}][cargo][{{ $cargoIndex }}][charged_weight]" class="form-control" value="{{ $cargo['charged_weight'] }}" required readonly></td>
                           <td><input type="text" class="form-control" value="{{ $cargo['unit'] }}"  readonly></td>
                           <td><input type="text" name="lr[{{ $lrIndex }}][cargo][{{ $cargoIndex }}][document_no]" class="form-control" value="{{ $cargo['document_no'] }}" required readonly></td>
                           <td><input type="text" name="lr[{{ $lrIndex }}][cargo][{{ $cargoIndex }}][document_name]" class="form-control" value="{{ $cargo['document_name'] }}" required readonly></td>
                           <td><input type="date" name="lr[{{ $lrIndex }}][cargo][{{ $cargoIndex }}][document_date]" class="form-control" value="{{ $cargo['document_date'] }}" required readonly></td>
                           <td><input type="text" name="lr[{{ $lrIndex }}][cargo][{{ $cargoIndex }}][eway_bill]" class="form-control" value="{{ $cargo['eway_bill'] }}" required readonly></td>
                           <td><input type="date" name="lr[{{ $lrIndex }}][cargo][{{ $cargoIndex }}][valid_upto]" class="form-control" value="{{ $cargo['valid_upto'] }}" required readonly></td>
                           <td>
                           <input  name="lr[{{ $index }}][cargo][{{ $cargoIndex }}][declared_value]"
                              type="number" 
                              value="{{ $cargo['declared_value'] }}" 
                              class="form-control declared-value"  
                              readonly 
                              ></td>
                           <td>
                           <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">🗑</button>
                           </td>
                           </tr>
                           @endforeach
                           </tbody>
                           </table>
                           <!-- Add Row Button for this LR -->
                           <!-- <div class="text-end mt-2">
                              <button type="button" class="btn btn-sm btn-primary" onclick="addRow({{ $lrIndex }})">
                              ➕ Add Row
                              </button>
                              </div> -->
                           </div>
                           </div>
                           <!-- Freight Details Section  -->
                           <div class="row mt-4">
                           <div class="col-12">
                           <h5 class="pb-3">🚚 Freight Details (LR {{ $lrIndex + 1 }})</h5>
                           @php $freightType = $lr['freightType'] ?? 'paid'; @endphp
                           <div class="mb-3 d-flex gap-3">
                           <div class="form-check form-check-inline">
                           <input class="form-check-input freight-type"
                           type="radio"
                           name="lr[freightType]"
                           id="freightPaid"
                           value="paid"
                           onchange="toggleFreightTable()"
                           {{ $freightType === 'paid' ? 'checked' : '' }}>
                           <label class="form-check-label" for="freightPaid">Paid</label>
                           </div>
                           <div class="form-check form-check-inline">
                           <input class="form-check-input freight-type"
                           type="radio"
                           name="lr[freightType]"
                           id="freightToPay"
                           value="to_pay"
                           onchange="toggleFreightTable()"
                           {{ $freightType === 'to_pay' ? 'checked' : '' }}>
                           <label class="form-check-label" for="freightToPay">To Pay</label>
                           </div>
                           <div class="form-check form-check-inline">
                           <input class="form-check-input freight-type"
                           type="radio"
                           name="lr[freightType]"
                           id="freightToBeBilled"
                           value="to_be_billed"
                           onchange="toggleFreightTable()"
                           {{ $freightType === 'to_be_billed' ? 'checked' : '' }}>
                           <label class="form-check-label" for="freightToBeBilled">To Be Billed</label>
                           </div>
                           </div>
                           <div class="table-responsive">
                           <table class="table table-bordered align-middle text-center">
                           <thead>
                           <tr>
                           <th>Freight</th>
                           <th>LR Charges</th>
                           <th>Hamali</th>
                           <th>Other Charges</th>
                           <th>GST</th>
                           <th>Total Freight</th>
                           <th>Less Advance</th>
                           <th>Balance Freight</th>
                           </tr>
                           </thead>
                           <tbody>
                           <tr>
                           <td><input name="lr[{{ $lrIndex }}][freight_amount]" type="number" class="form-control"
                              value="{{ $lr['freight_amount'] ?? '' }}" placeholder="Enter Freight Amount" required readonly></td>
                           <td><input name="lr[{{ $lrIndex }}][lr_charges]" type="number" class="form-control"
                              value="{{ $lr['lr_charges'] ?? '' }}" placeholder="Enter LR Charges" required readonly></td>
                           <td><input name="lr[{{ $lrIndex }}][hamali]" type="number" class="form-control"
                              value="{{ $lr['hamali'] ?? '' }}" placeholder="Enter Hamali Charges" required readonly></td>
                           <td><input name="lr[{{ $lrIndex }}][other_charges]" type="number" class="form-control"
                              value="{{ $lr['other_charges'] ?? '' }}" placeholder="Enter Other Charges" required readonly></td>
                           <td><input name="lr[{{ $lrIndex }}][gst_amount]" type="number" class="form-control"
                              value="{{ $lr['gst_amount'] ?? '' }}" placeholder="Enter GST Amount" required readonly></td>
                           <td><input name="lr[{{ $lrIndex }}][total_freight]" type="number" class="form-control"
                              value="{{ $lr['total_freight'] ?? '' }}" placeholder="Total Freight" required readonly></td>
                           <td><input name="lr[{{ $lrIndex }}][less_advance]" type="number" class="form-control"
                              value="{{ $lr['less_advance'] ?? '' }}" placeholder="Less Advance Amount" required readonly></td>
                           <td><input name="lr[{{ $lrIndex }}][balance_freight]" type="number" class="form-control"
                              value="{{ $lr['balance_freight'] ?? '' }}" placeholder="Balance Freight Amount" required readonly></td>
                           </tr>
                           </tbody>
                           </table>
                           </div>
                           </div>
                           </div>
                           <div class="row mt-4">
                           @php
                           $vehicleData = isset($lr['vehicle']) && is_array($lr['vehicle']) ? collect($lr['vehicle'])->filter(fn($item) => isset($item['vehicle_no']) && $item['vehicle_no'] !== null)->values() : collect();
                           @endphp
                           <div class="col-12" data-lr-index="{{ $index }}">
                           <h5 class="mb-3 pb-3">📦 Vehicle Description (LR #{{ $index }})</h5>
                           <div class="table-responsive">
                           <table class="table table-bordered align-middle text-center">
                           <thead class="">
                           <tr>
                           <th>#</th>
                           <th>Vehicle No.</th>
                           <th>Remarks</th>
                           </tr>
                           </thead>
                           <tbody id="vehicleTableBody_{{ $index }}">
                           @foreach ($vehicleData as $vehicleIndex => $vehicle)
                           <tr>
                           <td>
                           <div class="form-check">
                           {{ $loop->iteration }}
                           </div>
                           </td>
                           <td>
                           <select name="lr[{{ $index }}][vehicle][{{ $vehicleIndex }}][vehicle_no]" class="form-select" required>
                           <option value="">Select Vehicle No</option>
                           @foreach ($vehicles as $v)
                           <option value="{{ $v->vehicle_no }}" {{ $vehicle['vehicle_no'] == $v->vehicle_no ? 'selected' : '' }}>
                           {{ $v->vehicle_no }}
                           </option>
                           @endforeach
                           </select>
                           </td>
                           <td>
                           <input type="text" 
                              class="form-control" value="{{ $vehicle['remarks'] ?? '' }}" readonly>
                           </td>
                           </tr>
                           @endforeach
                           </tbody>
                           </table>
                           </div>
                           </div>  
                           </div>
                           <!-- Declared Value -->
                           <div class="row mt-3">
                           <div class="col-md-6">
                           <div class="mb-3">
                           <label class="form-label" style="font-weight: bold;">💰 Declared Value (Rs.)</label>
                           <input type="text" id="totalDeclaredValue-{{ $index }}" name="lr[{{ $index }}][total_declared_value]" value="{{ $lr['total_declared_value'] ?? '' }}" class="form-control" readonly>
                           </div>
                           </div>
                           </div>
                           <!-- Remove / Add More LR Buttons -->
                           <div class="d-flex justify-content-end gap-2 mt-3">
                           <!-- <button type="button" class="btn btn-outline-warning btn-sm removeLRBtn" data-id="lrItem${counter}" onclick="removeLrRow(this)">
                              <i class="fas fa-trash-alt"></i> Remove
                              </button>
                              <button type="button" class="btn btn-sm addMoreLRBtn" data-id="lrItem${counter}" style="background-color: #ca2639; color: #fff;" onclick="addLrRow()">
                              <i class="fas fa-plus-circle"></i> Add More LR - Consignment
                              </button> -->
                           </div>
                           <div class="row">
                           <!-- Submit Button -->
                           <div class="row mt-4 mb-4">
                           <div class="col-12 text-center">
                           <!-- <button type="submit" class="btn btn-primary">
                              <i class="fas fa-save"></i> Update Consignment 
                              </button> -->
                           </div>
                           </div>
                           </div>
                           </div>
                           </div>
                           <!-- lr -->
                           @endforeach
                           <div id="lr-container"></div>
                        </div>
                     </div>
               </form>
               </div>
            </div>
         </div>
      </div>
      <!-- View Modal -->
   </div>
</div>
@endsection