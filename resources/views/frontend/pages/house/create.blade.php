@extends('frontend.partial.other')

@section('content')
    <div class="container bg-light col-md-5 pd-4 py-3 card shadow">
        <div class="row">
            <div class="col-md-12">
                <h3 class="text-center">Register House</h3>
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @elseif (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif
                <form action="{{ route('frontend.house.store') }}" method="post">
                    @csrf
                    <div class="form-row">
                        <div class="col-md-12 mb-3">
                            <label for="validationDefault01">House Name</label>
                            <input type="text" class="form-control" id="validationDefault01" placeholder="House Name"
                                name="house_name" required>
                            @error('house_name')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="validationDefault01">House Number</label>
                            <input type="number" class="form-control" id="validationDefault01" placeholder="House Number"
                                name="house_number" required>
                            @error('house_number')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="validationDefault01">House Address</label>
                            <input type="text" class="form-control" id="validationDefault01" placeholder="House Address"
                                name="address" required>
                            @error('address')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="validationDefault01">City</label>
                            <input type="text" class="form-control" id="validationDefault01" placeholder="City"
                                name="city" required>
                            @error('city')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="validationDefault01">Division</label>
                            <select type="text" class="form-control" id="validationDefault01" placeholder="Division"
                                name="division" required>
                                <option value="">Select Division</option>
                                <option value="dhaka">Dhaka</option>
                                <option value="mymensingh">Mymensingh</option>
                                <option value="khulna">Khulna</option>
                            </select>
                            @error('division')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="validationDefault01">Country</label>
                            <input type="text" class="form-control" id="validationDefault01" placeholder="Country"
                                name="country" required>
                            @error('country')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <button class="btn btn-primary" type="submit">Submit form</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
