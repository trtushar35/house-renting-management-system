@extends('frontend.master')

@section('content')
    <div class="container">
        <div class="row g-4">
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @elseif (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
            @foreach ($houses as $house)
                <div class="col-lg-4 col-md-4">
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
                                {{ $house->available_date }}</p>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    <div class="row mt-4">
        <div class="col-12 text-center">
            <a class="btn btn-primary py-3 px-5" href="{{ route('frontend.all.property') }}">Browse More Property</a>
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
