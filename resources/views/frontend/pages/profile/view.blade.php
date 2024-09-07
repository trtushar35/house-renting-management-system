@extends('frontend.partial.other')

@section('content')
    <div class="container text-center d-flex justify-content-center">
        <div class="main-body col-md-8">
            <nav aria-label="breadcrumb" class="main-breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item active" aria-current="page">User Profile</li>
                </ol>
            </nav>

            <div class="row gutters-sm">
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-body">
                            @if (auth()->guard('tenantCheck')->check())
                            <div class="d-flex flex-column align-items-center text-center">
                                <img src="{{url('/tenants/'. auth('tenantCheck')->user()->image)}}" alt="Upload Image"
                                    class="rounded-circle" width="150">
                            </div>
                            @elseif (auth()->guard('ownerCheck')->check())
                            <div class="d-flex flex-column align-items-center text-center">
                                <img src="{{url('/owners/'. auth('ownerCheck')->user()->image)}}" alt="Image"
                                    class="rounded-circle" width="150">
                            </div>
                            @endif
                        </div>
                    </div>
                    @if (auth()->guard('tenantCheck')->check())
                        <a href="{{ route('frontend.profile.edit', auth('tenantCheck')->user()->id) }}"
                            class="btn btn-success mt-2">Edit</a>
                    @elseif (auth()->guard('ownerCheck')->check())
                        <a href="{{ route('frontend.profile.edit', auth('ownerCheck')->user()->id) }}"
                            class="btn btn-success mt-2">Edit</a>
                    @endif
                </div>
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-body ">
                            @if (auth()->guard('tenantCheck')->check())
                                <h4>Name: {{ auth('tenantCheck')->user()->name }}</h4>
                                <p>Tenant Details:</p>
                                <p>Email: {{ auth('tenantCheck')->user()->email }}</p>
                                <p>Phone: {{ auth('tenantCheck')->user()->phone }}</p>
                                <p>Address: {{ auth('tenantCheck')->user()->address }}</p>
                            @elseif (auth()->guard('ownerCheck')->check())
                                <h4>Name: {{ auth('ownerCheck')->user()->name }}</h4>
                                <p>Owner Details:</p>
                                <p>Email: {{ auth('ownerCheck')->user()->email }}</p>
                                <p>Phone: {{ auth('ownerCheck')->user()->phone }}</p>
                                <p>Address: {{ auth('ownerCheck')->user()->address }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <hr>
@endsection
