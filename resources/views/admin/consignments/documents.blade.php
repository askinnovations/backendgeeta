@extends('admin.layouts.app')
@section('title', 'Order | KRL')
@section('content')

<div class="page-content">
   <div class="container-fluid">
      <!-- start page title -->
      <div class="row">
         <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
               <h4 class="mb-sm-0 font-size-18"> LR / Consignment Documents</h4>
               <div class="page-title-right">
                  <ol class="breadcrumb m-0">
                     <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                     <li class="breadcrumb-item active"> LR / Consignment/</li>
                     <li class="breadcrumb-item active">Documents </li>
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
                     <h4 class="card-title">📦 LR / Consignment Documents </h4>
                     <p class="card-title-desc"> Documents View , edit, or delete order details below.</p>
                  </div>
               </div>
               {{-- <pre>{{ dd($lrEntries) }}</pre> --}}
               <div class="card-body">
                  @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
                <table class="table">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>LR Number</th>
                            <th>Document Name</th>
                            <th>Image</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($lrEntries['cargo'] as $index => $cargo)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $lrEntries['lr_number'] ?? '-' }}</td>
                                <td>{{ $cargo['document_name'] ?? 'N/A' }}</td>
                                <td>
                                    @if (!empty($cargo['document_file']))
                                        <img src="{{ asset('storage/' . $cargo['document_file']) }}" width="100">
                                    @else
                                        No Image
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <!-- POD File Table -->
                <h5 class="mt-5 mb-3">📁 POD File</h5>
                <table class="table">
                    <thead>
                        <tr>
                            <th>LR Number</th>
                            <th>Pod File</th>
                        </tr>
                    </thead>
                    <tbody>
                       <tr>
                            <td>{{ $lrEntries['lr_number'] ?? 'N/A' }}</td>
                            <td>
                                @if (!empty($lrEntries['pod_files']) && is_array($lrEntries['pod_files']))
                                    @foreach ($lrEntries['pod_files'] as $podFile)
                                        <a href="{{ asset($podFile) }}" target="_blank">View POD</a><br>
                                    @endforeach
                                @else
                                    No POD uploaded
                                @endif
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>
            </div>
         </div>
      </div>
         <!-- Upload POD Button - Pass LR number as data-lr -->
     <button type="button" class="btn btn-sm btn-primary upload-pod-btn"
        data-bs-toggle="modal"
        data-bs-target="#podUploadModal"
        data-lr="{{ $lrEntries['lr_number'] }}">Upload POD</button>
        <!-- pod upload -->
   </div>
   <!-- End Page-content -->
</div>

<!-- POD Upload Modal -->
<div class="modal fade" id="podUploadModal" tabindex="-1" aria-labelledby="podUploadModalLabel" aria-hidden="true">
  <div class="modal-dialog">

    <form action="{{ route('admin.consignments.uploadPod') }}" method="POST" enctype="multipart/form-data">
       @csrf
      <div class="modal-content">

        <div class="modal-header">
          <h5 class="modal-title" id="podUploadModalLabel">Upload POD Document</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
           
          <!-- Auto-filled LR Number -->
          <div class="mb-3">
            <label for="lr_number" class="form-label">LR Number</label>
            <input type="text" class="form-control" name="lr_number" id="lr_number" readonly required>
          </div>

          <!-- File upload -->
          <div class="mb-3">
            <label for="pod_files" class="form-label">Select POD Files</label>
            <input type="file" name="pod_file" class="form-control" id="pod_files" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Upload</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const podButtons = document.querySelectorAll('.upload-pod-btn');
        const lrInput = document.getElementById('lr_number');

        podButtons.forEach(button => {
            button.addEventListener('click', function () {
                const lr = this.getAttribute('data-lr');
                lrInput.value = lr;
            });
        });
    });
</script>



@endsection