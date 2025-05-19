@extends('admin.layouts.app')
@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <!-- Voucher Add/Edit Form -->
            <div class="row add-form">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <div>
                                        <h4>➕ Add Voucher</h4>
                                        <p>Fill in the details to create a new voucher.</p>
                                    </div>
                                    <a href="{{ route('admin.voucher.index') }}" class="btn" id="backToVoucherListBtn"
                                        style="background-color: #ca2639; color: white; border: none;">
                                        ⬅ Back
                                    </a>
                                </div>
                                <form method="POST" action="{{ route('admin.voucher.store') }}">
                                    @csrf
                                    <div class="card-body">
                                        <!-- Row 1: Voucher Type & Date -->
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Voucher Type <span class="text-danger">*</span></label>
                                                    <select class="form-control" name="voucher_type" id="voucher_type" required>
                                                        <option value="">-- Select --</option>
                                                        <option value="Payment">Payment</option>
                                                        <option value="Receipt">Receipt</option>
                                                        <option value="Journal">Journal</option>
                                                        <option value="Contra">Contra</option>
                                                        <option value="Sales">Sales</option>
                                                        <option value="Purchase">Purchase</option>
                                                        <option value="Expense">Expense</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Date <span class="text-danger">*</span></label>
                                                    <input type="date" class="form-control" name="voucher_date" required>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Voucher Rows -->
                                        <div id="voucherRows">
                                            <!-- This block will be cloned -->
                                            <div class="voucher-row" data-index="0">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label">From Account <span class="text-danger">*</span></label>
                                                            <select class="form-control from_account" name="vouchers[0][from_account]" required>
                                                                <option value="">-- Select From Account --</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label">To Account <span class="text-danger">*</span></label>
                                                            <select class="form-control to_account" name="vouchers[0][to_account]" required>
                                                                <option value="">-- Select To Account --</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label">Amount <span class="text-danger">*</span></label>
                                                            <input type="number" class="form-control" name="vouchers[0][amount]" placeholder="Enter amount" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label">Assign</label>
                                                            <select class="form-control" name="vouchers[0][assigned_to]">
                                                                <option value="">-- Select Person or Entity --</option>
                                                                <option value="Person A">Person A</option>
                                                                <option value="Person B">Person B</option>
                                                                <option value="Entity X">Entity X</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label">Narration</label>
                                                            <textarea class="form-control" name="vouchers[0][narration]" rows="2"></textarea>
                                                            <div class="form-check mt-2">
                                                                <input type="checkbox" class="form-check-input toggle-tally" id="toggle_tally_0" checked>
                                                                <label class="form-check-label" for="toggle_tally_0">Add Tally Narration</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 tally-narration d-none">
                                                        <div class="mb-3">
                                                            <label class="form-label">Tally Narration</label>
                                                            <textarea class="form-control" name="vouchers[0][tally_narration]" rows="2"></textarea>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="text-end mb-3 removeRowContainer d-none">
                                                    <button type="button" class="btn btn-danger removeRowBtn">Remove</button>
                                                </div>
                                                <hr>
                                            </div>
                                        </div>

                                        <!-- Add More -->
                                        <div class="mb-3">
                                            <button type="button" class="btn btn-secondary" id="addMoreBtn">+ Add More</button>
                                        </div>

                                        <!-- Submit -->
                                        <div class="row">
                                            <div class="col-12 text-end">
                                                <button type="submit" class="btn btn-primary">Save Voucher</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>

                            </div>
                        </div>
                    </div>
        </div>
    </div>


    <script>
    // Set the current date as the default value for the voucher date field
    document.getElementById('voucher_date').value = new Date().toISOString().split('T')[0];
</script>
<!-- JS: AJAX to fetch filtered ledgers -->
<script>
document.getElementById('voucher_type').addEventListener('change', function () {
    const type = this.value;
    if (!type) return;

    fetch(`/admin/voucher/get-ledgers?voucher_type=${type}`)
        .then(response => response.json())
        .then(data => {
            document.querySelectorAll('.voucher-row').forEach(row => {
                const fromSelect = row.querySelector('.from_account');
                const toSelect = row.querySelector('.to_account');

                fromSelect.innerHTML = '<option value="">-- Select From Account --</option>';
                toSelect.innerHTML = '<option value="">-- Select To Account --</option>';

                if (data.from?.length > 0) {
                    data.from.forEach(item => {
                        fromSelect.innerHTML += `<option value="${item.id}">${item.ledger_name}</option>`;
                    });
                } else {
                    fromSelect.innerHTML += '<option value="">No ledgers available</option>';
                }

                if (data.to?.length > 0) {
                    data.to.forEach(item => {
                        toSelect.innerHTML += `<option value="${item.id}">${item.ledger_name}</option>`;
                    });
                } else {
                    toSelect.innerHTML += '<option value="">No ledgers available</option>';
                }
            });
        })
        .catch(error => {
            console.error('Error fetching ledgers:', error);
            alert('An error occurred while fetching the ledgers.');
        });
});

// Add More functionality
document.getElementById('addMoreBtn').addEventListener('click', function () {
    const container = document.getElementById('voucherRows');
    const newRow = container.querySelector('.voucher-row').cloneNode(true);

    const index = container.querySelectorAll('.voucher-row').length;

    // Clear input values
    newRow.querySelectorAll('input, textarea, select').forEach(el => {
        if (el.tagName === 'SELECT') {
            el.selectedIndex = 0;
        } else if (el.type === 'checkbox') {
            el.checked = true; // Default to checked
        } else {
            el.value = '';
        }
    });

    // Update name attributes with the correct index
    newRow.querySelector('.from_account').name = `vouchers[${index}][from_account]`;
    newRow.querySelector('.to_account').name = `vouchers[${index}][to_account]`;
    newRow.querySelector('input[name^="vouchers"][name$="[amount]"]').name = `vouchers[${index}][amount]`;
    newRow.querySelector('select[name^="vouchers"][name$="[assigned_to]"]').name = `vouchers[${index}][assigned_to]`;
    newRow.querySelector('textarea[name^="vouchers"][name$="[narration]"]').name = `vouchers[${index}][narration]`;
    newRow.querySelector('textarea[name^="vouchers"][name$="[tally_narration]"]').name = `vouchers[${index}][tally_narration]`;

    // Reset Tally Narration: hidden and checkbox checked
    const tallyDiv = newRow.querySelector('.tally-narration');
    const toggleTally = newRow.querySelector('.toggle-tally');
    tallyDiv.classList.add('d-none');
    toggleTally.checked = true;

    // Assign unique ID to checkbox and label
    const uniqueId = `toggle_tally_${Date.now()}`;
    toggleTally.id = uniqueId;
    const label = newRow.querySelector('label[for^="toggle_tally_"]');
    if (label) {
        label.setAttribute('for', uniqueId);
    }

    // Show remove button
    const removeBtnContainer = newRow.querySelector('.removeRowContainer');
    removeBtnContainer.classList.remove('d-none');

    container.appendChild(newRow);
});

// Remove Row
document.addEventListener('click', function (e) {
    if (e.target.classList.contains('removeRowBtn')) {
        const totalRows = document.querySelectorAll('.voucher-row').length;
        if (totalRows > 1) {
            e.target.closest('.voucher-row').remove();
        } else {
            alert('At least one row is required.');
        }
    }
});

// Toggle Tally Narration (show when unchecked)
document.addEventListener('change', function (e) {
    if (e.target.classList.contains('toggle-tally')) {
        const row = e.target.closest('.voucher-row');
        const tallyDiv = row.querySelector('.tally-narration');

        // ❗ Logic: Show when unchecked (reversed logic)
        if (!e.target.checked) {
            tallyDiv.classList.remove('d-none');
        } else {
            tallyDiv.classList.add('d-none');
        }
    }
});

</script>



@endsection