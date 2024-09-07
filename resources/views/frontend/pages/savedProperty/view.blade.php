@extends('frontend.partial.other')

@section('content')
    <section>
        <div class="container shadow mt-4 mb-4">
            <div class="row">
                <div class="col-12 text-center">
                    <h1 class="text-primary py-3">Flat Details</h1>
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @elseif (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <div class="p-4">
                        <div class="mb-3">
                            <h6><strong>House Name:</strong> {{ $flatDetails->flat->house->house_name }} </h6>
                            <h6><strong>House Owner Name:</strong> {{ $flatDetails->flat->house->houseOwner->name }}</h6>
                            <h6><strong>Flat Number:</strong> {{ $flatDetails->flat->flat_number }}</h6>
                            <h6><strong>Floor Number:</strong> {{ $flatDetails->flat->floor_number }}</h6>
                            <h6><strong>Total Bedroom:</strong> {{ $flatDetails->flat->num_bedrooms }}</h6>
                            <h6><strong>Total Bathrooms:</strong> {{ $flatDetails->flat->num_bathrooms }}</h6>
                            <h6><strong>Total Rent:</strong> {{ $flatDetails->flat->rent }} BDT</h6>
                            <h6><strong>City:</strong> {{ $flatDetails->flat->house->city }}</h6>
                            <h6><strong>Country:</strong> {{ $flatDetails->flat->house->country }}</h6>
                            <h6><strong>Address:</strong> {{ $flatDetails->flat->address }}</h6>
                            <h6><strong>Availability:</strong> {{ $flatDetails->flat->availability == 1 ? 'Yes' : 'No' }}</h6>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 mt-4 d-flex justify-content-center">

                        <div class="bg-light text-center"
                            style="height: 100%; display: flex; align-items: center; justify-content: center;">
                            <span class="text-muted">No Image Available</span>
                        </div>

                </div>
                <div class="col-12 text-center py-4">
                    <a href="{{ route('frontend.add.to.save', $flatDetails->flat->id) }}" class="btn btn-danger mx-2">Save
                        Post</a>
                    <button type="button" class="btn btn-success mx-2" data-toggle="modal" data-target="#bookNowModal">Book
                        Now</button>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="bookNowModal" tabindex="-1" role="dialog" aria-labelledby="bookNowModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bookNowModalLabel">Confirm Booking</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to book this flat?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <a href="{{ route('frontend.book.now', $flatDetails->flat->id) }}" class="btn btn-success">Confirm</a>
                </div>
            </div>
        </div>
    </div>
@endsection
