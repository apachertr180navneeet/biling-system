<!-- Global Payment History & Rollback Modal -->
<div class="modal fade" id="paymentHistoryModal" tabindex="-1" aria-labelledby="paymentHistoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-dark text-white py-3">
                <h5 class="modal-title text-white mb-0" id="paymentHistoryModalLabel">
                    <i class="bx bx-history me-2 text-warning"></i> Payment History & Audit Log
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Summary Card -->
                <div class="row g-3 mb-4">
                    <div class="col-md-3 col-6">
                        <div class="card bg-light border-0 text-center py-2 px-1">
                            <span class="text-muted small text-uppercase">Doc Number</span>
                            <span class="fw-bold fs-6 text-dark" id="modalDocNumber">-</span>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="card bg-light border-0 text-center py-2 px-1">
                            <span class="text-muted small text-uppercase">Total Amount</span>
                            <span class="fw-bold fs-6 text-primary" id="modalTotalAmount">₹0.00</span>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="card bg-light border-0 text-center py-2 px-1">
                            <span class="text-muted small text-uppercase">Total Paid</span>
                            <span class="fw-bold fs-6 text-success" id="modalReceivedAmount">₹0.00</span>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="card bg-light border-0 text-center py-2 px-1">
                            <span class="text-muted small text-uppercase">Outstanding</span>
                            <span class="fw-bold fs-6 text-danger" id="modalBalance">₹0.00</span>
                        </div>
                    </div>
                </div>

                <!-- Nav Tabs for History & Rollback -->
                <ul class="nav nav-tabs nav-fill mb-3" id="paymentModalTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-bold" id="history-tab" data-bs-toggle="tab" data-bs-target="#history-pane" type="button" role="tab">
                            <i class="bx bx-list-ul me-1"></i> Transaction Log
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold text-danger" id="rollback-tab" data-bs-toggle="tab" data-bs-target="#rollback-pane" type="button" role="tab">
                            <i class="bx bx-undo me-1"></i> Rollback Payment
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="paymentModalTabContent">
                    <!-- History Tab Pane -->
                    <div class="tab-pane fade show active" id="history-pane" role="tabpanel">
                        <div class="table-responsive" style="max-height: 280px;">
                            <table class="table table-hover table-striped align-middle table-bordered mb-0">
                                <thead class="table-dark small">
                                    <tr>
                                        <th>Date & Time</th>
                                        <th>Type</th>
                                        <th>Amount</th>
                                        <th>Mode</th>
                                        <th>Ref / Reason</th>
                                        <th>User</th>
                                    </tr>
                                </thead>
                                <tbody id="modalHistoryTableBody">
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">Loading history...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Rollback Form Pane -->
                    <div class="tab-pane fade" id="rollback-pane" role="tabpanel">
                        <form id="paymentRollbackForm" class="p-2">
                            @csrf
                            <input type="hidden" id="rollbackType" name="type">
                            <input type="hidden" id="rollbackId" name="id">

                            <div id="rollbackWarningMessage" class="alert alert-warning py-2 mb-3 small d-none">
                                <i class="bx bx-info-circle me-1"></i> Reversing a payment will reduce the paid total and increase the outstanding ledger balance.
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark">Rollback Amount (₹) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">₹</span>
                                        <input type="number" step="0.01" min="0.01" id="rollbackAmount" name="amount" class="form-control fw-bold text-danger" required placeholder="0.00">
                                    </div>
                                    <div class="form-text">Max transferable back: <strong id="rollbackMaxLimit" class="text-dark">₹0.00</strong></div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark">Payment Mode</label>
                                    <select name="payment_mode" id="rollbackPaymentMode" class="form-select">
                                        <option value="Cash">Cash</option>
                                        <option value="UPI">UPI / GPay / PhonePe</option>
                                        <option value="Bank Transfer">Bank Transfer (NEFT/RTGS/IMPS)</option>
                                        <option value="Cheque">Cheque</option>
                                        <option value="Card">Credit / Debit Card</option>
                                    </select>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-bold text-dark">Reason for Rollback <span class="text-danger">*</span></label>
                                    <textarea name="reason" id="rollbackReason" class="form-control" rows="2" required placeholder="Specify reason (e.g. Wrong amount entered, cheque bounced, payment cancelled...)"></textarea>
                                </div>
                                <div class="col-md-12 text-end">
                                    <button type="submit" id="rollbackSubmitBtn" class="btn btn-danger fw-bold">
                                        <i class="bx bx-undo me-1"></i> Execute Payment Rollback
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light py-2">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    window.openPaymentHistoryModal = function(type, id) {
        var modalEl = document.getElementById('paymentHistoryModal');
        var bsModal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);

        // Reset inputs
        document.getElementById('modalDocNumber').innerText = '...';
        document.getElementById('modalTotalAmount').innerText = '...';
        document.getElementById('modalReceivedAmount').innerText = '...';
        document.getElementById('modalBalance').innerText = '...';
        document.getElementById('modalHistoryTableBody').innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4"><span class="spinner-border spinner-border-sm me-2"></span>Loading transactions...</td></tr>';
        document.getElementById('rollbackAmount').value = '';
        document.getElementById('rollbackReason').value = '';
        document.getElementById('rollbackType').value = type;
        document.getElementById('rollbackId').value = id;

        // Activate History Tab by default
        var historyTab = new bootstrap.Tab(document.getElementById('history-tab'));
        historyTab.show();

        // Fetch Data via AJAX
        var url = "{{ route('admin.payment-transactions.history', ['type' => ':type', 'id' => ':id']) }}"
            .replace(':type', type)
            .replace(':id', id);

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                alert(data.message || 'Error fetching history.');
                return;
            }

            document.getElementById('modalDocNumber').innerText = data.doc_number;
            document.getElementById('modalTotalAmount').innerText = '₹' + data.total_amount;
            document.getElementById('modalReceivedAmount').innerText = '₹' + data.received_amount;
            document.getElementById('modalBalance').innerText = '₹' + data.balance;
            document.getElementById('rollbackMaxLimit').innerText = '₹' + data.received_amount;
            
            var rollbackAmtInp = document.getElementById('rollbackAmount');
            rollbackAmtInp.max = data.raw_received;
            rollbackAmtInp.value = data.raw_received > 0 ? data.raw_received : '';

            var rollbackTabBtn = document.getElementById('rollback-tab');
            var rollbackWarning = document.getElementById('rollbackWarningMessage');
            var rollbackSubmitBtn = document.getElementById('rollbackSubmitBtn');

            if (data.raw_received <= 0) {
                rollbackTabBtn.classList.add('disabled');
                rollbackSubmitBtn.disabled = true;
                rollbackWarning.classList.remove('d-none');
                rollbackWarning.innerHTML = '<i class="bx bx-error-circle me-1"></i> No received payments available to rollback for this document.';
            } else {
                rollbackTabBtn.classList.remove('disabled');
                rollbackSubmitBtn.disabled = false;
                rollbackWarning.classList.remove('d-none');
                rollbackWarning.innerHTML = '<i class="bx bx-info-circle me-1"></i> Reversing a payment will reduce the total paid amount and restore outstanding balance.';
            }

            // Populate table
            var tbody = document.getElementById('modalHistoryTableBody');
            if (!data.history || data.history.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-3">No payment or rollback records logged yet.</td></tr>';
            } else {
                var html = '';
                data.history.forEach(function(row) {
                    var badgeClass = row.type === 'payment' ? 'bg-success' : 'bg-danger';
                    var badgeText = row.type === 'payment' ? 'PAYMENT' : 'ROLLBACK';
                    var amtClass = row.type === 'payment' ? 'text-success' : 'text-danger';
                    var sign = row.type === 'payment' ? '+' : '-';

                    html += '<tr>' +
                        '<td><small class="fw-semibold">' + (row.created_at || '-') + '</small></td>' +
                        '<td><span class="badge ' + badgeClass + '">' + badgeText + '</span></td>' +
                        '<td class="fw-bold ' + amtClass + '">' + sign + '₹' + row.amount.toFixed(2) + '</td>' +
                        '<td><span class="badge bg-secondary">' + (row.payment_mode || 'Cash') + '</span></td>' +
                        '<td><small>' + (row.note || '-') + '</small></td>' +
                        '<td><small class="text-muted">' + (row.user_name || 'System') + '</small></td>' +
                        '</tr>';
                });
                tbody.innerHTML = html;
            }

            bsModal.show();
        })
        .catch(err => {
            console.error(err);
            alert('Failed to load transaction history.');
        });
    };

    // Handle Rollback Form Submit
    var rollbackForm = document.getElementById('paymentRollbackForm');
    if (rollbackForm) {
        rollbackForm.addEventListener('submit', function(e) {
            e.preventDefault();

            var type = document.getElementById('rollbackType').value;
            var id = document.getElementById('rollbackId').value;
            var amount = parseFloat(document.getElementById('rollbackAmount').value) || 0;
            var reason = document.getElementById('rollbackReason').value.trim();

            if (amount <= 0) {
                alert('Please enter a valid rollback amount.');
                return;
            }
            if (!reason) {
                alert('Please provide a reason for the rollback.');
                return;
            }

            if (!confirm('Are you sure you want to rollback payment of ₹' + amount.toFixed(2) + '? This action cannot be undone.')) {
                return;
            }

            var submitBtn = document.getElementById('rollbackSubmitBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Processing...';

            var url = "{{ route('admin.payment-transactions.rollback', ['type' => ':type', 'id' => ':id']) }}"
                .replace(':type', type)
                .replace(':id', id);

            var formData = new FormData(rollbackForm);

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '{{ csrf_token() }}'
                },
                body: formData
            })
            .then(res => res.json())
            .then(resData => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bx bx-undo me-1"></i> Execute Payment Rollback';

                if (resData.success) {
                    alert(resData.message || 'Payment rolled back successfully.');
                    var modalEl = document.getElementById('paymentHistoryModal');
                    var bsModal = bootstrap.Modal.getInstance(modalEl);
                    if (bsModal) bsModal.hide();
                    location.reload();
                } else {
                    alert(resData.message || 'Failed to process rollback.');
                }
            })
            .catch(err => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bx bx-undo me-1"></i> Execute Payment Rollback';
                console.error(err);
                alert('An error occurred while processing rollback.');
            });
        });
    }
});
</script>
