@extends('frontend.partial.other')

@section('content')
    <section>
        <div class="container shadow mt-4 mb-4">
            <div class="row">
                <div class="col-12 text-center">
                    <h1 class="text-primary py-3">Tenant Details</h1>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <div class="p-4">
                        <div class="mb-3">
                            <h6><strong>Name:</strong> {{ $tenantDetails->name }} </h6>
                            <h6><strong>Phone:</strong> {{ $tenantDetails->phone }}</h6>
                            <h6><strong>Email:</strong> {{ $tenantDetails->email }}</h6>
                            <h6><strong>Address:</strong> {{ $tenantDetails->address }}</h6>
                            <h6><strong>Booked House: </strong> {{$bookings->flat->house->house_name}}({{$bookings->flat->house->house_number}})</h6>
                            <h6><strong>Floor Number: </strong> {{$bookings->flat->floor_number}}</h6>
                            <h6><strong>Flat Number: </strong> {{$bookings->flat->flat_number}}</h6>
                            <h6><strong>Total Rent Amount: </strong> {{$bookings->flat->rent}}</h6>
                            <h6><strong>Rent Status: </strong> </h6>
                            <h6><strong>Paid Amount: </strong> </h6>
                            <h6><strong>Due: </strong> </h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
