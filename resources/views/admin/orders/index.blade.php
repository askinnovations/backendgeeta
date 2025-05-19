@extends('admin.layouts.app')
@section('title', 'Order | KRL')
@section('content')
<div class="page-content">
   <div class="container-fluid">
      <!-- start page title -->
      <div class="row">
         <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
               <h4 class="mb-sm-0 font-size-18">Order Booking</h4>
               <div class="page-title-right">
                  <ol class="breadcrumb m-0">
                     <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                     <li class="breadcrumb-item active">Order Booking</li>
                  </ol>
               </div>
            </div>
         </div>
      </div>
      <!-- end page title -->
      <!-- Order Booking listing Page -->
      <div class="row listing-form">
         <div class="col-12">
            <div class="card">
               <div class="card-header d-flex justify-content-between align-items-center">
                  <div>
                     <h4 class="card-title">📦 Order Booking</h4>
                     <p class="card-title-desc">View, edit, or delete order details below.</p>
                  </div>
                  @if (hasAdminPermission('create order_booking'))
                  <a href="{{ route('admin.orders.create') }}" class="btn" id="addOrderBtn"
                     style="background-color: #ca2639; color: white; border: none;">
                  <i class="fas fa-plus"></i> Add Order Booking
                   </a>
                   @endif
                 
               </div>
               <div class="card-body">
                  <table id="datatable" class="table table-bordered dt-responsive nowrap w-100">
                     <thead>
                        <tr>
                           <th>S.No</th>
                           <th>Order ID</th>
                           <th>Lr Number</th>
                           <th>Consignment Details</th>
                           <th>Consignment Pickup Date</th>
                           <th>Status   </th>
                          @if (hasAdminPermission('edit order_booking') || hasAdminPermission('delete order_booking')|| hasAdminPermission('view order_booking'))
                           <th>Action</th>@endif
                        </tr>
                     </thead>
                    <tbody>
                        @foreach($orders as $key => $order)
                        <tr class="order-row" data-id="{{ $order->id }}">
                            <td>{{ $key + 1 }}</td> <!-- Serial Number -->
                            <td>{{ $order->order_id }}</td>
                            <td>
                                 @if(is_array($order->lr))
                                    @foreach($order->lr as $lr)
                                          {{ $lr['lr_number'] ?? '-' }}<br>
                                    @endforeach
                                 @elseif(is_string($order->lr))
                                    @php
                                          $decodedLr = json_decode($order->lr, true);
                                    @endphp
                                    @if(is_array($decodedLr))
                                          @foreach($decodedLr as $lr)
                                             {{ $lr['lr_number'] ?? '-' }}<br>
                                          @endforeach
                                    @else
                                          {{ $order->lr }}
                                    @endif
                                 @else
                                    -
                                 @endif
                           </td>

                            <td>{{ $order->description }}</td>
                            <td>{{ $order->order_date }}</td>

                           <td id="status-{{ $order->order_id }}">
                              @php
                                 $currentStatus = '-';
                                 $decodedStatus = is_array($order->status) ? $order->status : json_decode($order->status, true);
                                 if (is_array($decodedStatus)) {
                                    foreach ($decodedStatus as $stat) {
                                    if (!empty($stat['timestamp'])) {
                                       $currentStatus = $stat['status'];
                                    }
                                    }
                                 }
                              @endphp

                              <span class="badge bg-primary status-cell" style="cursor:pointer;"
                                    data-order_id="{{ $order->order_id }}"
                                    data-status="{{ $currentStatus }}">
                                 {{ $currentStatus }}
                              </span>
                              </td>





                        @if (hasAdminPermission('edit order_booking') || hasAdminPermission('delete order_booking')|| hasAdminPermission('view order_booking'))

                            <td>
                              @if (hasAdminPermission('view order_booking'))
                            <a href="{{ route('admin.orders.documents', $order->order_id) }}" class="btn btn-sm btn-light view-btn"><i class="fas fa-file-alt text-primary"></i>
                              @endif
                            </a>
                            @if (hasAdminPermission('view order_booking'))
                            <a href="{{ route('admin.orders.view', $order->order_id) }}" class="btn btn-sm btn-light view-btn"><i class="fas fa-eye text-primary"></i>
                            </a>
                            @endif
                            @if (hasAdminPermission('edit order_booking'))
                            <a href="{{ route('admin.orders.edit', $order->order_id) }}" class="btn btn-sm btn-light edit-btn">
                                 <i class="fas fa-pen text-warning"></i>
                              </a>
                              @endif
                              @if (hasAdminPermission('delete order_booking'))
                              <a href="{{ route('admin.orders.delete', $order->order_id) }}" class="btn btn-sm btn-light delete-btn"><i class="fas fa-trash text-danger"></i></a>
                              @endif
                            </td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                  </table>
               </div>
            </div>
         </div>
      </div>
      
     

   </div>
   <!-- End Page-content -->
</div>
<!-- end main content-->


<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function (e) {
                e.preventDefault();

                if (!confirm('Are you sure you want to delete all LR entries under this order ID?')) return;

                const url = this.getAttribute('href');

                fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    if (data.status === 'success') {
                        location.reload(); // or redirect
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Something went wrong.');
                });
            });
        });
    });
</script>
<!-- Status Update Modal -->
<!-- Status Update Modal -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="statusUpdateForm">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="statusModalLabel">Update Order Status</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="orderIdInput" name="order_id" value="">

          <div class="mb-3">
            <label for="statusSelect" class="form-label">Select Status</label>
            <select class="form-select" id="statusSelect" name="status" required>
              <option value="Material Collected">Material Collected</option>
              <option value="In Transit">In Transit</option>
              <option value="Delivered">Delivered</option>
              <!-- आप जितने status चाहते हैं यहाँ डाल सकते हैं -->
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Update Status</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
  $(document).ready(function() {
  // Status badge पर क्लिक पर modal खोलना
  $('.status-cell').click(function() {
    var orderId = $(this).data('order_id'); // order_id लें
    var currentStatus = $(this).data('status');

    $('#orderIdInput').val(orderId);
    $('#statusSelect').val(currentStatus);
    $('#statusModal').modal('show');
  });

  // Modal form submit AJAX से
  $('#statusUpdateForm').submit(function(e) {
    e.preventDefault();

    var orderId = $('#orderIdInput').val();
    var newStatus = $('#statusSelect').val();

    $.ajax({
      url: '/admin/orders/update-status/' + orderId,  // order_id के साथ कॉल
      method: 'POST',
      data: {
        _token: '{{ csrf_token() }}',
        status: newStatus
      },
      success: function(response) {
        if(response.success) {
          $('#statusModal').modal('hide');

          // टेबल में नया status दिखाएं और data-status अपडेट करें
          $('#status-' + orderId + ' .status-cell').text(newStatus);
          $('#status-' + orderId + ' .status-cell').data('status', newStatus);
        } else {
          alert('Status update failed!');
        }
      },
      error: function() {
        alert('Error occurred while updating status.');
      }
    });
  });
});

</script>

@endsection