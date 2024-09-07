@extends('frontend.partial.other')

@section('content')
    <div class="container mt-5 mb-5">
        <div class="row ">
            <div class="col-md-12">
                @if ($flatDetails->isEmpty())
                    <div class="text-center">
                        <p class="text-muted">Add your flat first. You haven't add any flat yet.</p>
                    </div>
            </div>
        </div>
    </div>
@else
    <div class="container col-md-12">
        <h3 class="mt-3 mb-3">Flat Lists </h3>
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
            @foreach ($flatDetails as $data)
                <div class="col-md-4 mb-4">
                    <div class="card shadow">
                        <div class="card-body d-flex align-items-center">
                            <a href="{{ route('frontend.single.flat.details', $data->id) }}"
                                class="flex-grow-1 text-decoration-none">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h3 class="mb-0">{{ $data->floor_number }}</h3>
                                        <p class="mb-0">Floor Number</p>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h3 class="mb-0">{{ $data->flat_number }}</h3>
                                        <p class="mb-0">Flat Number</p>
                                    </div>
                                    <div>
                                        <i class="bi bi-building fa-3x text-primary"></i>
                                    </div>
                                </div>
                            </a>
                            <form action="{{ route('frontend.flat.delete', $data->id) }}" method="get"
                                onsubmit="return confirm('Are you sure you want to delete this flat?');">
                                @csrf
                                <button type="submit" class="btn btn-link p-0">
                                    <i class="bi bi-trash fa-2x text-danger"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif
@endsection
