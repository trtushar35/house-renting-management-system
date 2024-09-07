@extends('frontend.partial.other')

@section('content')
    <div class="container bg-light col-md-5 pd-4 py-3 card shadow">
        <h5 class="text-center">Create Flat Under House</h5>
        <div class="row">
            <div class="col-md-12">
                <form action="{{ route('frontend.flat.update', $flat->id) }}" method="post">
                    @csrf
                    @method('PUT')
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @elseif (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                    <div class="form-row">
                        <div class="col-md-12 mb-3">
                            <label for="validationDefault01">Address</label>
                            <input type="text" class="form-control" id="validationDefault01" placeholder="Address"
                                value="{{ $flat->address }}" name="address" required readonly>
                            @error('address')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="validationDefault01">Floor Number</label>
                            <input type="number" class="form-control" id="validationDefault01" placeholder="Floor Number"
                                value="{{ $flat->floor_number }}" name="floor_number" required>
                            @error('floor_number')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="validationDefault01">Flat Number</label>
                            <input type="text" class="form-control" id="validationDefault01" placeholder="Flat Number"
                                value="{{ $flat->flat_number }}" name="flat_number" required>
                            @error('flat_number')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="validationDefault01">Total Bedroom</label>
                            <input type="number" class="form-control" id="validationDefault01" placeholder="Total Bedroom"
                                value="{{ $flat->num_bedrooms }}" name="num_bedrooms" required>
                            @error('num_bedrooms')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="validationDefault01">Total Bathroom</label>
                            <input type="number" class="form-control" id="validationDefault01" placeholder="Total Bathroom"
                                value="{{ $flat->num_bathrooms }}" name="num_bathrooms" required>
                            @error('num_bathrooms')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="validationDefault01">Total Rent</label>
                            <input type="number" class="form-control" id="validationDefault01" placeholder="Total Rent"
                                value="{{ $flat->rent }}" name="rent" required>
                            @error('rent')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="availability">Availability</label><br>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="availability" id="availability1"
                                    value="{{ $flat->availability }}" value="1" checked>
                                <label class="form-check-label" for="availability1">Yes</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="availability" id="availability0"
                                    value="{{ $flat->availability }}" value="0">
                                <label class="form-check-label" for="availability0">No</label>
                            </div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="validationDefault01">Available Date</label>
                            <input type="date" class="form-control" id="validationDefault01" placeholder="Available Date"
                               value="{{ $flat->available_date }}" name="available_date" required>
                            @error('available_date')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <button class="btn btn-primary" type="submit">Submit form</button>
                </form>
            </div>
        </div>
    </div>
@endsection
