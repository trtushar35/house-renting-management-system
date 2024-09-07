@extends('frontend.partial.other')

@section('content')
    <section>
        <div class="container shadow mt-4 mb-4">
            <div class="row">
                <h1 class="text-center text-primary py-1">Flat Details</h1>
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @elseif (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif
                <div class="col-xl-6 col-lg-6 col-sm-12 col-md-6">
                    <div class="text-left p-4 pb-0">
                        <div class="mb-1">
                            <h6>House Name: {{ $flatDetails->house->house_name }}</h6>
                            <h6>Flat Number: {{ $flatDetails->flat_number }}</h6>
                            <h6>Floor Number: {{ $flatDetails->floor_number }}</h6>
                            <h6>Total Bedroom: {{ $flatDetails->num_bedrooms }}</h6>
                            <h6>Total Bathrooms: {{ $flatDetails->num_bathrooms }}</h6>
                            <h6>Total Rent: {{ $flatDetails->rent }} .bdt</h6>
                            <h6>Availability: {{ $flatDetails->availability == 1 ? 'Yes' : 'No' }}</h6>
                            <h6>Available Date: {{ $flatDetails->available_date }}</h6>
                        </div>
                    </div>
                </div>
                <div class="col-xl-6 col-lg-6 col-sm-12 col-md-6 mt-4">
                    <div id="previewImages" class="d-flex flex-wrap">
                        @if ($flatDetails->flatImages)
                            @foreach ($flatDetails->flatImages as $image)
                                <div class="image-container position-relative">
                                    {!! $image->square_footage !!}
                                    <form action="{{ route('frontend.single.flat.image.delete', $image->id) }}"
                                        class="delete-image-form position-absolute top-0 end-0 m-2">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm">X</button>
                                    </form>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <img id="defaultImage" class="img-fluid m-2" src="{{ asset('storage/flatImage/noimage.jpg') }}"
                        style="width:150px; height:auto; display: none;" alt="no image">
                </div>
                <div class="col-xl-6 col-lg-6 col-sm-12 col-md-6 mb-2">
                    <a class="btn btn-success" href="{{ route('frontend.flat.edit', $flatDetails->id) }}">Edit</a>
                </div>
                <div class="col-xl-6 col-lg-6 col-sm-12 col-md-6 mb-2">
                    <form id="uploadForm" action="{{ route('frontend.single.flat.image', $flatDetails->id) }}"
                        method="post" enctype="multipart/form-data">
                        <input type="hidden" name="flat_id" value="{{ $flatDetails->id }}">
                        @csrf
                        @method('put')
                        <label class="btn btn-primary" for="imageUpload">Upload Images</label>
                        <input multiple type="file" name="image[]" id="imageUpload" style="display:none;"
                            onchange="previewImages(event)">
                        <button type="submit" id="submitBtn" class="btn btn-primary mt-2"
                            style="display:none;">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <script>
        function previewImages(event) {
            var input = event.target;
            var previewContainer = document.getElementById('previewImages');
            var submitBtn = document.getElementById('submitBtn');
            var defaultImage = document.getElementById('defaultImage');
            previewContainer.innerHTML = '';

            if (input.files.length > 0) {
                submitBtn.style.display = 'block';
                defaultImage.style.display = 'none';
            } else {
                submitBtn.style.display = 'none';
                defaultImage.style.display = 'block';
            }

            for (var i = 0; i < input.files.length; i++) {
                var reader = new FileReader();
                reader.onload = function(event) {
                    var imgElement = document.createElement('img');
                    imgElement.src = event.target.result;
                    imgElement.className = 'img-fluid m-2';
                    imgElement.style.width = '150px';
                    imgElement.style.height = 'auto';
                    previewContainer.appendChild(imgElement);
                }
                reader.readAsDataURL(input.files[i]);
            }
        }

        document.getElementById('uploadForm').addEventListener('submit', function() {
            var submitBtn = document.getElementById('submitBtn');
            submitBtn.style.display = 'none';
            setTimeout(function() {
                submitBtn.style.display = 'none';
            }, 500);
        });
    </script>
    <style>
        .image-container {
            position: relative;
            display: inline-block;
        }

        .delete-image-form {
            display: none;
        }

        .image-container:hover .delete-image-form {
            display: block;
        }
    </style>
@endsection
