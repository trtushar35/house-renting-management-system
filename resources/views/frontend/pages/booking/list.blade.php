@extends('frontend.partial.other')

@section('content')
    <div class="container mt-5 mb-5">
        <div class="row ">
            <div class="col-md-12">
                @if ($bookings->isEmpty())
                    <div class="text-center">
                        <p class="text-muted">Please make a booking first. You haven't book yet.</p>
                    </div>
            </div>
        </div>
    </div>
@else
    <div class="container mt-5 mb-5">
        <div class="row ">
            <div class="col-md-12">
                <div class="col-md-12">
                    <h3 class="text-center mb-4">Booking List</h3>
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @elseif (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th scope="col">Serial</th>
                                <th scope="col">House Name</th>
                                <th scope="col">Flat Number</th>
                                <th scope="col">Floor Number</th>
                                <th scope="col">Rent Amount</th>
                                <th scope="col">House Owner Name</th>
                                <th scope="col">Owner Phone Number</th>
                                <th scope="col">Booking Date</th>
                                <th scope="col">Booking Status</th>
                                <th scope="col">Payment Status</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bookings as $booking)
                                <tr>
                                    <th scope="row">{{ $booking->id }}</th>
                                    <td>{{ $booking->flat->house->house_name }}</td>
                                    <td>{{ $booking->flat->flat_number }}</td>
                                    <td>{{ $booking->flat->floor_number }}</td>
                                    <td>{{ $booking->flat->rent }}</td>
                                    <td>{{ $booking->flat->house->houseOwner->name }}</td>
                                    <td>{{ $booking->flat->house->houseOwner->phone }}</td>
                                    <td>{{ $booking->created_at }}</td>
                                    <td>{{ $booking->booking_status }}</td>
                                    <td>{{ $booking->payment_status }}</td>
                                    <td>
                                        @if ($booking->booking_status == 'Approved' && $booking->payment_status == 'Pending')
                                            <a class="btn btn-success"
                                                href="{{ route('frontend.payment.form', $booking->id) }}">Pay Now</a>
                                            <a class="btn btn-danger"
                                                href="{{ route('frontend.cancel.booking', $booking->id) }}">Cancel
                                                Booking</a>
                                        @elseif ($booking->booking_status == 'Pending' && $booking->payment_status == 'Pending')
                                            <a class="btn btn-danger"
                                                href="{{ route('frontend.cancel.booking', $booking->id) }}">Cancel
                                                Booking</a>
                                        @elseif ($booking->booking_status == 'Approved' && $booking->payment_status == 'Paid')
                                            <a href="">Invoice</a>
                                        @endif  
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection
