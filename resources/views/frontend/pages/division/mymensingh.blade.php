@extends('frontend.partial.other')

@section('content')
    <div class="container mt-5 mb-5">
        <div class="row g-0 gx-5 align-items-end">
            <div class="col-lg-6">
                <div class="text-start mx-auto mb-5 wow slideInLeft" data-wow-delay="0.1s">
                    <h1 class="mb-3">Property Listing</h1>
                </div>
            </div>
        </div>
        <div class="tab-content">
            <div id="tab-1" class="tab-pane fade show p-0 active">
                <div class="row g-4">
                    @foreach ($houses as $house)
                        <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                            <a href="{{ route('frontend.single.house.details', $house->id) }}">
                                <div class="property-item rounded overflow-hidden">
                                    <div class="position-relative overflow-hidden">
                                        <div class="box" style="height: 250px; width:100%;">

                                            @foreach ($house->flatImages as $flatImage)
                                                @foreach (explode(',', $flatImage->square_footage) as $image)
                                                    <img src="{{ trim($image) }}" height="50" width="50" />
                                                @endforeach
                                            @endforeach
                                        </div>
                                        <div
                                            class="bg-primary rounded text-white position-absolute start-0 top-0 m-4 py-1 px-3">
                                        </div>
                                    </div>
                                    <div class="p-4 pb-0">
                                        <h5 class="text-primary mb-3">{{ $house->rent }}.bdt</h5>
                                        <a class="d-block h5 mb-2" href="#">{{ $house->address }}</a>
                                        <p><i class="fa fa-map-marker-alt text-primary me-2"></i> {{ $house->address }}</p>
                                    </div>
                                    <div class="d-flex border-top">
                                        <small class="flex-fill text-center border-end py-2"><i
                                                class="bi bi-calendar2-check-fill"></i> Available From:
                                            {{ $house->availability }}</small>
                                        <small class="flex-fill text-center border-end py-2"><i
                                                class="fa fa-bed text-primary me-2"></i> {{ $house->num_bedrooms }}
                                            Bedrooms</small>
                                        <small class="flex-fill text-center py-2"><i
                                                class="fa fa-bath text-primary me-2"></i> {{ $house->num_bathrooms }}
                                            Bathrooms</small>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
