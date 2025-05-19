@extends('admin.layouts.app')
@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="row add form">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h4>🚛 Vehicle Details</h4>
                            <p>Enter the required details for the vehicle.</p>
                        </div>
                        <a  href="{{ route('admin.voucher.index') }}" class="btn" id="backToListBtn"
                            style="background-color: #ca2639; color: white; border: none;">
                            ⬅ Back to Listing
                        </a>
                    </div>
                    <form method="POST" action="{{ route('admin.voucher.update', $voucher->id) }}">
                        @csrf
                        <div class="card-body">
                            <!-- Row 1: Voucher Type & Date -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Voucher Type <span class="text-danger">*</span></label>
                                         <select class="form-control" name="voucher_type" id="voucher_type" required>
                                        <option value="">-- Select --</option>
                                        <option value="Payment" {{ old('voucher_type', $voucher->voucher_type ?? '') == 'Payment' ? 'selected' : '' }}>Payment</option>
                                        <option value="Receipt" {{ old('voucher_type', $voucher->voucher_type ?? '') == 'Receipt' ? 'selected' : '' }}>Receipt</option>
                                        <option value="Journal" {{ old('voucher_type', $voucher->voucher_type ?? '') == 'Journal' ? 'selected' : '' }}>Journal</option>
                                        <option value="Contra" {{ old('voucher_type', $voucher->voucher_type ?? '') == 'Contra' ? 'selected' : '' }}>Contra</option>
                                        <option value="Sales" {{ old('voucher_type', $voucher->voucher_type ?? '') == 'Sales' ? 'selected' : '' }}>Sales</option>
                                        <option value="Purchase" {{ old('voucher_type', $voucher->voucher_type ?? '') == 'Purchase' ? 'selected' : '' }}>Purchase</option>
                                        <option value="Expense" {{ old('voucher_type', $voucher->voucher_type ?? '') == 'Expense' ? 'selected' : '' }}>Expense</option>
                                    </select>

                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" name="voucher_date" value="{{ $voucher->voucher_date }}" required>
                                    </div>
                                </div>
                            </div>

                            <!-- Voucher Rows -->
                            <div id="voucherRows">
                                @php
                                $voucherRows = is_string($voucher->vouchers) ? json_decode($voucher->vouchers, true) : $voucher->vouchers;
                                @endphp


                                @foreach($voucherRows as $index => $row)
                                    <div class="voucher-row" data-index="{{ $index }}">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">From Account</label>
                                                   <select class="form-control from_account" name="vouchers[{{ $index }}][from_account]" required
                            data-selected="{{ $row['from_account'] }}">
                            <option value="">-- Select --</option>
                            {{-- Options will be filled via JS --}}
                        </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">To Account</label>
                                                    <select class="form-control to_account" name="vouchers[{{ $index }}][to_account]" required
                            data-selected="{{ $row['to_account'] }}">
                            <option value="">-- Select --</option>
                            {{-- Options will be filled via JS --}}
                        </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Amount</label>
                                                    <input type="number" class="form-control" name="vouchers[{{ $index }}][amount]" value="{{ $row['amount'] }}" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Assign</label>
                                                    <select class="form-control" name="vouchers[{{ $index }}][assigned_to]">
                                                        <option value="">-- Select --</option>
                                                        <option value="Person A" {{ $row['assigned_to'] == 'Person A' ? 'selected' : '' }}>Person A</option>
                                                        <option value="Person B" {{ $row['assigned_to'] == 'Person B' ? 'selected' : '' }}>Person B</option>
                                                        <option value="Entity X" {{ $row['assigned_to'] == 'Entity X' ? 'selected' : '' }}>Entity X</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Narration</label>
                                                    <textarea class="form-control" name="vouchers[{{ $index }}][narration]">{{ $row['narration'] ?? '' }}</textarea>
                                                    <div class="form-check mt-2">
                                                        <input type="checkbox" class="form-check-input toggle-tally" id="toggle_tally_{{ $index }}" {{ empty($row['tally_narration']) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="toggle_tally_{{ $index }}">Add Tally Narration</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 tally-narration {{ empty($row['tally_narration']) ? 'd-none' : '' }}">
                                                <div class="mb-3">
                                                    <label class="form-label">Tally Narration</label>
                                                    <textarea class="form-control" name="vouchers[{{ $index }}][tally_narration]">{{ $row['tally_narration'] ?? '' }}</textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="text-end mb-3 removeRowContainer {{ $loop->first ? 'd-none' : '' }}">
                                            <button type="button" class="btn btn-danger removeRowBtn">Remove</button>
                                        </div>

                                        <hr>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mb-3">
                                <button type="button" class="btn btn-secondary" id="addMoreBtn">+ Add More</button>
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">Update Voucher</button>
                            </div>
                        </div>
                    </form>



                </div>
            </div>
        </div>  
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const voucherTypeSelect = document.getElementById('voucher_type');

    function fetchLedgersAndPopulateDropdowns(type) {
        if (!type) return;

        fetch(`/admin/voucher/get-ledgers?voucher_type=${type}`)
            .then(response => response.json())
            .then(data => {
                document.querySelectorAll('.voucher-row').forEach(row => {
                    const fromSelect = row.querySelector('.from_account');
                    const toSelect = row.querySelector('.to_account');

                    const selectedFrom = fromSelect.getAttribute('data-selected');
                    const selectedTo = toSelect.getAttribute('data-selected');

                    fromSelect.innerHTML = '';
                    const defaultFromOption = new Option('-- Select From Account --', '');
                    fromSelect.add(defaultFromOption);

                    data.from?.forEach(item => {
                        const option = new Option(item.ledger_name, item.id);
                        if (item.id == selectedFrom) option.selected = true;
                        fromSelect.add(option);
                    });

                    toSelect.innerHTML = '';
                    const defaultToOption = new Option('-- Select To Account --', '');
                    toSelect.add(defaultToOption);

                    data.to?.forEach(item => {
                        const option = new Option(item.ledger_name, item.id);
                        if (item.id == selectedTo) option.selected = true;
                        toSelect.add(option);
                    });
                });

                // Store for future use
                window.ledgerOptions = data;
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to fetch ledgers.');
            });
    }

    // On change
    voucherTypeSelect.addEventListener('change', function () {
        const type = this.value;
        fetchLedgersAndPopulateDropdowns(type);
    });

    // ⏪ On page load (edit mode)
    const initialVoucherType = voucherTypeSelect.value;
    if (initialVoucherType) {
        fetchLedgersAndPopulateDropdowns(initialVoucherType);
    }
});



// Add More Row
document.getElementById('addMoreBtn').addEventListener('click', function () {
    const container = document.getElementById('voucherRows');
    const lastRow = container.querySelector('.voucher-row:last-child');
    const newRow = lastRow.cloneNode(true);
    const index = container.querySelectorAll('.voucher-row').length;

    newRow.querySelectorAll('input, textarea, select').forEach(el => {
        if (el.tagName === 'SELECT') {
            el.selectedIndex = 0;
        } else if (el.type === 'checkbox') {
            el.checked = true;
        } else {
            el.value = '';
        }
    });

    // Update input/select/textarea names with new index
    newRow.querySelector('.from_account').name = `vouchers[${index}][from_account]`;
    newRow.querySelector('.to_account').name = `vouchers[${index}][to_account]`;
    newRow.querySelector('input[name$="[amount]"]').name = `vouchers[${index}][amount]`;
    newRow.querySelector('select[name$="[assigned_to]"]').name = `vouchers[${index}][assigned_to]`;
    newRow.querySelector('textarea[name$="[narration]"]').name = `vouchers[${index}][narration]`;
    newRow.querySelector('textarea[name$="[tally_narration]"]').name = `vouchers[${index}][tally_narration]`;

    // Reset tally narration field
    const tallyDiv = newRow.querySelector('.tally-narration');
    const toggleTally = newRow.querySelector('.toggle-tally');
    tallyDiv.classList.add('d-none');
    toggleTally.checked = true;

    const uniqueId = `toggle_tally_${Date.now()}`;
    toggleTally.id = uniqueId;
    const label = newRow.querySelector('label[for^="toggle_tally_"]');
    if (label) label.setAttribute('for', uniqueId);

    newRow.querySelector('.removeRowContainer').classList.remove('d-none');

    // Re-populate dropdowns with correct options if ledger data is available
    if (window.ledgerOptions) {
        const fromSelect = newRow.querySelector('.from_account');
        const toSelect = newRow.querySelector('.to_account');

        fromSelect.innerHTML = '<option value="">-- Select From Account --</option>';
        toSelect.innerHTML = '<option value="">-- Select To Account --</option>';

        window.ledgerOptions.from?.forEach(item => {
            fromSelect.innerHTML += `<option value="${item.id}">${item.ledger_name}</option>`;
        });

        window.ledgerOptions.to?.forEach(item => {
            toSelect.innerHTML += `<option value="${item.id}">${item.ledger_name}</option>`;
        });
    }

    container.appendChild(newRow);
});

// Remove Row
document.addEventListener('click', function (e) {
    if (e.target.classList.contains('removeRowBtn')) {
        const rows = document.querySelectorAll('.voucher-row');
        if (rows.length > 1) {
            e.target.closest('.voucher-row').remove();
        } else {
            alert("At least one row is required.");
        }
    }
});

// Toggle Tally Narration
document.addEventListener('change', function (e) {
    if (e.target.classList.contains('toggle-tally')) {
        const row = e.target.closest('.voucher-row');
        const tallyDiv = row.querySelector('.tally-narration');
        if (!e.target.checked) {
            tallyDiv.classList.remove('d-none');
        } else {
            tallyDiv.classList.add('d-none');
        }
    }
});
</script>









@endsection