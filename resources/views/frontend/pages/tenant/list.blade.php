@extends('frontend.partial.other')

@section('content')
    <div class="container mt-5 mb-5">
        <div class="row">
            <div class="col-md-12">
                @if ($bookings->isEmpty())
                    <div class="text-center">
                        <p class="text-muted">You don't have any tenants.</p>
                    </div>
                @else
                    <div class="container">
                        <h3 class="mt-3 mb-3 text-center">Tenant Lists</h3>
                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @elseif (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif
                        <div class="row">
                            @foreach ($bookings as $data)
                            <a href="{{ route('frontend.tenant.details', $data->tenant->id) }}">
                                <div class="col-md-4">
                                    <div class="card shadow">
                                        <div class="card-body d-flex align-items-center">
                                            <div class="mr-3">
                                                @if ($data->tenant && $data->tenant->image)
                                                    <img src="{{ url('/tenants/' . $data->tenant->image) }}"
                                                        alt="Tenant Image" style="width: 70px; height: 70px;">
                                                @else
                                                    <img src="{{ asset('storage/flatImages/noimage.jpg') }}"
                                                        alt="Default Image" class="rounded-circle"
                                                        style="width: 70px; height: 70px;">
                                                @endif
                                            </div>
                                            <div style="margin-left: 35px;">
                                                <div class="mb-3">
                                                    <h5 class="card-title">Flat Number: {{ $data->flat->flat_number }}</h5>
                                                </div>
                                                <p class="card-text">House Number: {{ $data->flat->house->house_number }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
