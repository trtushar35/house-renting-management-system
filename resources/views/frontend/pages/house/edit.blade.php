@extends('frontend.partial.other')

@section('content')
    <div class="container bg-light col-md-5 pd-4 py-3 card shadow">
        <div class="row">
            <div class="col-md-12">
                <form action="{{ route('frontend.house.update', $house->id) }}" method="post">
                    @csrf
                    @method('PUT')
                    <div class="form-row">
                        <div class="col-md-12 mb-3">
                            <label for="validationDefault01">House Name</label>
                            <input type="text" class="form-control" id="validationDefault01" placeholder="House Name"
                                value="{{ $house->house_name }}" name="house_name" required>
                            @error('house_name')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="validationDefault01">House Number</label>
                            <input type="number" class="form-control" id="validationDefault01" placeholder="House Number"
                                value="{{ $house->house_number }}" name="house_number" required>
                            @error('house_number')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="validationDefault01">House Address</label>
                            <input type="text" class="form-control" id="validationDefault01" placeholder="House Address"
                                value="{{ $house->address }}" name="address" required>
                            @error('address')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="validationDefault01">Division</label>
                            <select class="form-control" id="validationDefault01" name="division" required>
                                <option value="" disabled>Select Division</option>
                                <option value="dhaka" {{ $house->division == 'dhaka' ? 'selected' : '' }}>Dhaka</option>
                                <option value="mymensingh" {{ $house->division == 'mymensingh' ? 'selected' : '' }}>
                                    Mymensingh</option>
                                <option value="khulna" {{ $house->division == 'khulna' ? 'selected' : '' }}>Khulna</option>
                            </select>
                            @error('division')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="validationDefault01">City</label>
                            <input type="text" class="form-control" id="validationDefault01" placeholder="City"
                                value="{{ $house->city }}" name="city" required>
                            @error('city')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="validationDefault01">Country</label>
                            <input type="text" class="form-control" id="validationDefault01" placeholder="Country"
                                value="{{ $house->country }}" name="country" required>
                            @error('country')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <button class="btn btn-primary" type="submit">Submit</button>
                </form>
            </div>
        </div>
    </div>
@endsection
