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
                            <h6><strong>House Name:</strong> {{ $flatDetails->house->house_name }} </h6>
                            <h6><strong>House Owner Name:</strong> {{ $flatDetails->house->houseOwner->name }}</h6>
                            <h6><strong>Flat Number:</strong> {{ $flatDetails->flat_number }}</h6>
                            <h6><strong>Floor Number:</strong> {{ $flatDetails->floor_number }}</h6>
                            <h6><strong>Total Bedroom:</strong> {{ $flatDetails->num_bedrooms }}</h6>
                            <h6><strong>Total Bathrooms:</strong> {{ $flatDetails->num_bathrooms }}</h6>
                            <h6><strong>Total Rent:</strong> {{ $flatDetails->rent }} BDT</h6>
                            <h6><strong>City:</strong> {{ $flatDetails->house->city }}</h6>
                            <h6><strong>Country:</strong> {{ $flatDetails->house->country }}</h6>
                            <h6><strong>Address:</strong> {{ $flatDetails->address }}</h6>
                            <h6><strong>Availability:</strong> {{ $flatDetails->availability == 1 ? 'Yes' : 'No' }}</h6>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 mt-4 d-flex justify-content-center">
                    @if ($flatDetails->flatImages->count() > 0)
                        @foreach ($flatDetails->flatImages as $flatImage)
                            {!! $flatImage->square_footage !!}
                        @endforeach
                    @else
                        <div class="bg-light text-center"
                            style="height: 100%; display: flex; align-items: center; justify-content: center;">
                            <span class="text-muted">No Image Available</span>
                        </div>
                    @endif
                </div>
                <div class="col-12 text-center py-4">
                    <a href="{{ route('frontend.add.to.save', $flatDetails->id) }}" class="btn btn-danger mx-2">Save
                        Post</a>
                    <a type="button" href="#" class="btn btn-success mx-2" data-toggle="modal" data-target="#bookNowModal" >Book Now</a>
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
                    <a href="{{ route('frontend.book.now', $flatDetails->id) }}" class="btn btn-success">Confirm</a>
                </div>
            </div>
        </div>
    </div>
@endsection
