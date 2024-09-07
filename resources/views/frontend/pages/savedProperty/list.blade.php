@extends('frontend.partial.other')

@section('content')
    <section class="h-100 gradient-custom">
        <div class="container py-5">
            <div class="row d-flex justify-content-center my-4">
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-header py-3">
                            <h5 class="mb-0">Saved Property List</h5>
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
                        <div class="card-body">
                            @if (auth()->guard('tenantCheck')->user())
                                @foreach ($flatDetails as $data)
                                    <div class="row">
                                        <div class="col-lg-3 col-md-12 mb-4 mb-lg-0">
                                            @if ($data->flat->flatImages->isNotEmpty())
                                                @foreach ($data->flat->flatImages as $image)
                                                    {{ $image->square_footage }}
                                                @endforeach
                                            @else
                                                <p>No square footage image available</p>
                                            @endif
                                        </div>

                                        <div class="col-lg-5 col-md-6 mb-4 mb-lg-0">
                                            <p><strong>House Name: {{ $data->flat->house->house_name }}</strong></p>
                                            <p>House Owner Name: {{ $data->flat->house->houseOwner->name }}</p>
                                            <p>Address: {{ $data->flat->address }}</p>


                                            <a href="{{ route('frontend.saved.property.delete', $data->id) }}"
                                                class="btn btn-danger btn-sm me-1 mb-2" data-mdb-toggle="tooltip"
                                                title="Remove item">
                                                <i class="fas fa-trash"></i> Delete
                                            </a>
                                            <a href="{{ route('frontend.saved.property.view', $data->id) }}"
                                                class="btn btn-primary btn-sm mb-2" title="View house details">
                                                View
                                            </a>
                                        </div>

                                        <div class="col-lg-4 col-md-6 mb-4 mb-lg-0">
                                            <p><strong>Rent Amount: {{ $data->flat->rent }} BDT</strong></p>
                                        </div>
                                    </div>
                                    <hr class="my-4" />
                                @endforeach
                            @elseif (auth()->guard('ownerCheck')->user())
                                @foreach ($flatDetails as $data)
                                    <div class="row">
                                        <div class="col-lg-3 col-md-12 mb-4 mb-lg-0">
                                            @if ($data->flat->flatImages->isNotEmpty())
                                                @foreach ($data->flat->flatImages as $image)
                                                   {{ $image->square_footage }}
                                                @endforeach
                                            @else
                                                <p>No square footage image available</p>
                                            @endif
                                        </div>

                                        <div class="col-lg-5 col-md-6 mb-4 mb-lg-0">
                                            <p><strong>House Name: {{ $data->flat->house->house_name }}</strong></p>
                                            <p>House Owner Name: {{ $data->flat->house->houseOwner->name }}</p>
                                            <p>Address: {{ $data->flat->address }}</p>


                                            <a href="{{ route('frontend.saved.property.view', $data->id) }}"
                                                class="btn btn-danger btn-sm me-1 mb-2" data-mdb-toggle="tooltip"
                                                title="Remove item">
                                                <i class="fas fa-trash"></i> Delete
                                            </a>
                                            <a href="" class="btn btn-primary btn-sm mb-2"
                                                title="View house details">
                                                View
                                            </a>
                                        </div>

                                        <div class="col-lg-4 col-md-6 mb-4 mb-lg-0">
                                            <p><strong>Rent Amount: {{ $data->flat->rent }} BDT</strong></p>
                                        </div>
                                    </div>
                                    <hr class="my-4" />
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
