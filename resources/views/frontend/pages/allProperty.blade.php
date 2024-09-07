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
            <div class="row g-4">
                @foreach ($houses as $house)
                    <div class="col-lg-4 col-md-6">
                        <a href="{{ route('frontend.single.house.details', $house->id) }}" class="text-decoration-none">
                            <div class="card shadow-sm">
                                <div class="position-relative overflow-hidden" style="height: 150px; overflow: hidden;">
                                    @if ($house->flatImages->count() > 0)
                                        @foreach ($house->flatImages as $flatImage)
                                            {!! $flatImage->square_footage !!}
                                        @break
                                    @endforeach
                                @else
                                    <div class="bg-light text-center"
                                        style="height: 100%; display: flex; align-items: center; justify-content: center;">
                                        <span class="text-muted">No Image Available</span>
                                    </div>
                                @endif
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">{{ $house->address }}</h5>
                                <p class="card-text text-muted">{{ $house->rent }}.bdt</p>
                                <p class="card-text">
                                    <i class="fa fa-bed text-primary me-2"></i> {{ $house->num_bedrooms }} Bedrooms
                                </p>
                                <p class="card-text">
                                    <i class="fa fa-bath text-primary me-2"></i> {{ $house->num_bathrooms }} Bathrooms
                                </p>
                                <p class="card-text"><i class="fa fa-map-marker-alt text-primary me-2"></i>
                                    {{ $house->address }}</p>
                                <p class="card-text"><i class="bi bi-calendar2-check-fill"></i> Available From:
                                    {{ $house->availability }}</p>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</div>
<style>
    .card-img-top {
        height: 250px;
        object-fit: cover;
    }

    .overflow-hidden img {
        height: 100%;
        width: 100%;
    }
</style>
@endsection
