@extends('admin.layouts.app')
@section('title', 'Order | KRL')
@section('content')
<div class="page-content">
   <div class="container-fluid">
   <!-- start page title -->
      <div class="row">
         <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
               <h4 class="mb-sm-0 font-size-18"> Invoice</h4>
               <div class="page-title-right">
                  <ol class="breadcrumb m-0">
                     <li class="breadcrumb-item active"> Invoice</li>
                  </ol>
               </div>
            </div>
         </div>
      </div>
       <!-- Order Booking listing Page -->
        <div class="row listing-form">
            <div class="col-12">
                <div class="card">
                <div class="card-body">
                  <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                    <table class="table table-bordered dt-responsive nowrap w-100">
                         <thead>
                            <tr> 
                              <th>S.No</th>
                              <th>Item Description</th>
                              <th>Rate / Item</th>
                              <th>Qty</th>
                              <th>Taxable Value</th>
                              <th>Tax Amount</th>
                              <th>Total</th>
                              <th>Action</th>
                            </tr>
                         </thead>
                         <tbody>
                            <tr>
                                <td>1</td>
                                <td>TRANSPORTATION CHARGES
                                    <small>SAC: 996791</small>
                                </td>
                                <td>1,05,000.00</td>
                                <td>1</td>
                                <td>12,600.00 (12%)</td>
                                <td>1,17,600.00</td>
                                <td>1,20,500.00</td>
                                <td>
                                    <a href="{{ route('admin.consignments.invoice-view') }}" class="btn btn-sm btn-light view-btn">
                                    <i class="fas fa-eye text-primary"></i>
                                    </a>
                                </td>
                            </tr>

                         </tbody>
                    </table>
                  </div>
                </div>
                
                </div>
            </div>
        </div>
   </div>
</div>    
@endsection