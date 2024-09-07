@extends('frontend.partial.other')

@section('content')
    <div class="container bg-light col-md-5 pd-4 py-3 mt-3 mb-3 card shadow">
        <div class="row">
            <div class="col-md-12">
                <h3 class="text-center">Payment Form</h3>
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @elseif (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif
                <form action="{{ route('frontend.initiate.payment', $booking->id) }}" method="get">
                    @csrf
                    @if (auth()->guard('tenantCheck')->check())
                        <div class="form-row">
                            <div class="col-md-12 mb-3">
                                <label for="validationDefault01">Name</label>
                                <input type="text" class="form-control" id="validationDefault01"
                                    placeholder="Tenant Name" value="{{ auth('tenantCheck')->user()->name }}"
                                    readonly required>
                                @error('name')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="validationDefault01">Total Amount</label>
                                <input type="number" class="form-control" id="totalAmount" value="{{ $booking->rent }}"
                                    placeholder="Total Amount" name="amount" required readonly>
                                @error('amount')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="validationDefault01">Pay Amount</label>
                                <input type="number" class="form-control" id="payAmount" placeholder="Pay Amount"
                                    name="paid_amount" required>
                                @error('paid_amount')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="validationDefault01">Due</label>
                                <input type="number" class="form-control" id="dueAmount" placeholder="Due" name="due"
                                    required readonly>
                                @error('due')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    @elseif(auth()->guard('ownerCheck')->check())
                        <div class="form-row">
                            <div class="col-md-12 mb-3">
                                <label for="validationDefault02">Name</label>
                                <input type="text" class="form-control" id="validationDefault02" placeholder="Owner Name"
                                    readonly value="{{ auth('ownerCheck')->user()->name }}" required>
                                @error('name')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="validationDefault01">Total Amount</label>
                                <input type="number" class="form-control" id="totalAmount"
                                    value="{{ $booking->rent }}" placeholder="Total Amount" name="amount" required
                                    readonly>
                                @error('amount')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="validationDefault01">Pay Amount</label>
                                <input type="number" class="form-control" id="payAmount" placeholder="Pay Amount"
                                    name="paid_amount" required>
                                @error('paid_amount')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="validationDefault01">Due</label>
                                <input type="number" class="form-control" id="dueAmount" placeholder="Due"
                                    name="due" required readonly>
                                @error('due')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    @endif
                    <button class="btn btn-primary" type="submit">Pay Now</button>
                </form>

            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const payAmountField = document.getElementById('payAmount');
            const totalAmountField = document.getElementById('totalAmount');
            const dueAmountField = document.getElementById('dueAmount');

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
