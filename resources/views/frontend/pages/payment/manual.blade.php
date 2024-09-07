@extends('frontend.partial.other')

@section('content')
    <div class="container bg-light col-md-5 pd-4 py-3 mt-3 mb-3 card shadow">
        <div class="row">
            <div class="col-md-12">
                <h3 class="text-center">Manual Payment Form</h3>
                <form action="{{ route('frontend.manual.payment.store', $ownerId) }}" method="post">
                    @csrf
                    @foreach ($bookingDetails as $bookingDetail)
                        <input type="hidden" name="booking_id" value="{{ $bookingDetail->id }}">
                    @endforeach
                    <div class="form-row">
                        <div class="col-md-12 mb-3">
                            <label for="tenantSelect">Name</label>
                            <select class="form-control" name="" id="tenantSelect">
                                <option value="">Select Name</option>
                                @foreach ($bookingDetails as $data)
                                    <option value="{{ $data->tenant_id }}" data-rent="{{ $data->flat->rent }}">
                                        {{ $data->tenant->name ?? $data->flat->house->houseOwner->name }}
                                        ({{ $data->tenant_id ?? $data->owner_id }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="totalAmount">Total Amount</label>
                            <input type="number" class="form-control" id="totalAmount" placeholder="Total Amount"
                                name="amount" required readonly>
                            @error('amount')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="payAmount">Pay Amount</label>
                            <input type="number" class="form-control" id="payAmount" placeholder="Pay Amount"
                                name="paid_amount" required>
                            @error('paid_amount')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="dueAmount">Due</label>
                            <input type="number" class="form-control" id="dueAmount" placeholder="Due" name="due"
                                required readonly>
                            @error('due')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="validationDefault01">Payment Date</label>
                            <input type="date" class="form-control" id="validationDefault01" placeholder="Payment Date"
                                name="payment_date" required>
                            @error('payment_date')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="payment_month">Payment Month</label>
                            <select class="form-control" name="payment_month" id="payment_month">
                                <option value="">Select Month</option>
                                <option value="January">January</option>
                                <option value="February">February</option>
                                <option value="March">March</option>
                                <option value="April">April</option>
                                <option value="May">May</option>
                                <option value="June">June</option>
                                <option value="July">July</option>
                                <option value="August">August</option>
                                <option value="September">September</option>
                                <option value="October">October</option>
                                <option value="November">November</option>
                                <option value="December">December</option>
                            </select>
                            @error('payment_month')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <button class="btn btn-primary" type="submit">Submit</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const payAmountField = document.getElementById('payAmount');
            const totalAmountField = document.getElementById('totalAmount');
            const dueAmountField = document.getElementById('dueAmount');
            const tenantSelect = document.getElementById('tenantSelect');

            tenantSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const rentAmount = selectedOption.getAttribute('data-rent');

                totalAmountField.value = rentAmount ? parseFloat(rentAmount) : 0;

                // Update the due amount as well
                const payAmount = parseFloat(payAmountField.value);
                const dueAmount = rentAmount - payAmount;
                dueAmountField.value = dueAmount >= 0 ? dueAmount : 0;
            });

            payAmountField.addEventListener('input', function() {
                const totalAmount = parseFloat(totalAmountField.value);
                const payAmount = parseFloat(payAmountField.value);

                if (!isNaN(totalAmount) && !isNaN(payAmount)) {
                    const dueAmount = totalAmount - payAmount;
                    dueAmountField.value = dueAmount >= 0 ? dueAmount : 0;
                } else {
                    dueAmountField.value = totalAmount;
                }
            });
        });
    </script>
@endsection
