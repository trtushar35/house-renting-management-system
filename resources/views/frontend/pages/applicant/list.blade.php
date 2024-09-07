@extends('frontend.partial.other')

@section('content')
    <div class="container mt-5 mb-5">
        <div class="row shadow">
            <div class="col-md-12">
                <h3 class="text-center mb-4">Applicant List</h3>
                <a class="btn btn-success mb-3" href="{{ route('frontend.manual.payment') }}">Manual Payment</a>
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
                            <th scope="col">Applicant Name</th>
                            <th scope="col">Applicant Address</th>
                            <th scope="col">House Name</th>
                            <th scope="col">House Address</th>
                            <th scope="col">Flat Num</th>
                            <th scope="col">Floor Num</th>
                            <th scope="col">Rent</th>
                            <th scope="col">Booking Status</th>
                            <th scope="col">Payment Status</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    @foreach ($bookings as $key => $applicant)

                        <tbody>
                            <tr>
                                <th scope="row">{{ $key + 1 }}</th>
                                <td>{{ $applicant->tenant->name ?? $applicant->flat->house->houseOwner->name }}</td>
                                <td>{{ $applicant->tenant->address ?? $applicant->flat->house->houseOwner->address}}</td>
                                <td>{{ $applicant->flat->house->house_name }}</td>
                                <td>{{ $applicant->flat->address }}</td>
                                <td>{{ $applicant->flat->flat_number }}</td>
                                <td>{{ $applicant->flat->floor_number }}</td>
                                <td>{{ $applicant->rent }}</td>
                                <td>{{ $applicant->booking_status }}</td>
                                <td>{{ $applicant->payment->payment_status ?? 'Not Paid' }}</td>
                                @if ($applicant->booking_status == 'Pending')
                                    <td>
                                        <button class="btn btn-success" data-toggle="modal"
                                            data-target="#acceptModal-{{ $applicant->id }}">Accept</button>
                                        <button class="btn btn-danger" data-toggle="modal"
                                            data-target="#rejectModal-{{ $applicant->id }}">Reject</button>
                                    </td>
                                @endif
                            </tr>
                        </tbody>

                        <!-- Accept Modal -->
                        <div class="modal fade" id="acceptModal-{{ $applicant->id }}" tabindex="-1" role="dialog"
                            aria-labelledby="acceptModalLabel-{{ $applicant->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="acceptModalLabel-{{ $applicant->id }}">Confirmation
                                        </h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        Are you sure you want to accept this applicant?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        <a href="{{ route('frontend.applicant.accept', $applicant->id) }}"
                                            class="btn btn-primary">Confirm</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Reject Modal -->
                        <div class="modal fade" id="rejectModal-{{ $applicant->id }}" tabindex="-1" role="dialog"
                            aria-labelledby="rejectModalLabel-{{ $applicant->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="rejectModalLabel-{{ $applicant->id }}">Confirmation
                                        </h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        Are you sure you want to reject this applicant?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        <a href="{{ route('frontend.applicant.reject', $applicant->id) }}"
                                            class="btn btn-primary">Confirm</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
@endsection
