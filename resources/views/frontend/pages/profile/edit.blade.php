@extends('frontend.partial.other')

@section('content')
    <div class="container">
        <div class="main-body">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="main-breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item active" aria-current="page">User Profile</li>
                </ol>
            </nav>
            <!-- /Breadcrumb -->

            <div class="row gutters-sm">
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex flex-column align-items-center text-center">
                                @if (auth()->guard('tenantCheck')->check())
                                    <div class="d-flex flex-column align-items-center text-center">
                                        <img src="{{ url('/tenants' . auth('tenantCheck')->user()->image) }}"
                                            alt="Upload Image" class="rounded-circle" width="150" id="profileImage">
                                    </div>
                                @elseif (auth()->guard('ownerCheck')->check())
                                    <div class="d-flex flex-column align-items-center text-center">
                                        <img src="{{ url('/owners/' . auth('ownerCheck')->user()->image) }}" alt="Image"
                                            class="rounded-circle" width="150" id="profileImage">
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-body ">
                            <h5 class="card-title">Edit Profile</h5>
                            @if (auth()->guard('tenantCheck')->check())
                                <form action="{{ route('frontend.profile.update', auth('tenantCheck')->user()->id) }}"
                                    method="post" enctype="multipart/form-data">
                                    @csrf
                                    @method('put')
                                    <div class="form-group">
                                        <label for="name">Name</label>
                                        <input type="text" class="form-control" id="name" name="name"
                                            value="{{ auth('tenantCheck')->user()->name }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="email" class="form-control" id="email" name="email"
                                            value="{{ auth('tenantCheck')->user()->email }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="phone">Phone</label>
                                        <input type="text" class="form-control" id="phone" name="phone"
                                            value="{{ auth('tenantCheck')->user()->phone }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="address">Address</label>
                                        <input type="text" class="form-control" id="address" name="address"
                                            value="{{ auth('tenantCheck')->user()->address }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="image">Upload Image</label>
                                        <input type="file" class="form-control" id="image" name="image"
                                            accept="image/*" onchange="previewImage(event)">
                                    </div>
                                    <button type="submit" class="btn btn-primary">Update</button>
                                </form>
                            @elseif (auth()->guard('ownerCheck')->check())
                                <form action="{{ route('frontend.profile.update', auth('ownerCheck')->user()->id) }}"
                                    enctype="multipart/form-data" method="post">
                                    @csrf
                                    @method('put')
                                    <div class="form-group">
                                        <label for="name">Name</label>
                                        <input type="text" class="form-control" id="name" name="name"
                                            value="{{ auth('ownerCheck')->user()->name }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="email" class="form-control" id="email" name="email"
                                            value="{{ auth('ownerCheck')->user()->email }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="phone">Phone</label>
                                        <input type="text" class="form-control" id="phone" name="phone"
                                            value="{{ auth('ownerCheck')->user()->phone }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="address">Address</label>
                                        <input type="text" class="form-control" id="address" name="address"
                                            value="{{ auth('ownerCheck')->user()->address }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="image">Upload Image</label>
                                        <input type="file" class="form-control" id="image" name="image"
                                            accept="image/*" onchange="previewImage(event)">
                                    </div>
                                    <button type="submit" class="btn-sm btn-primary mt-2">Update</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function() {
                const output = document.getElementById('profileImage');
                output.src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
@endsection
