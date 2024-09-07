@extends('frontend.partial.other')

@section('content')
    <div class="container mt-5 mb-5">
        <div class="row">
            <div class="col-md-12">
                <h3 class="text-center">House List</h3>
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @elseif (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th scope="col">Serial</th>
                            <th scope="col">House Name</th>
                            <th scope="col">House Owner Name</th>
                            <th scope="col">House Number</th>
                            <th scope="col">House Address</th>
                            <th scope="col">Division</th>
                            <th scope="col">City</th>
                            <th scope="col">Country</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($houseList as $house)
                            <tr>
                                <th scope="row">{{ $house->id }}</th>
                                <td>{{ $house->house_name }}</td>
                                <td>{{ $house->houseOwner->name }}</td>
                                <td>{{ $house->house_number }}</td>
                                <td>{{ $house->address }}</td>
                                <td>{{ $house->division }}</td>
                                <td>{{ $house->city }}</td>
                                <td>{{ $house->country }}</td>
                                <td>
                                    <a href="{{ route('frontend.house.edit', $house->id) }}"
                                        class="btn-sm btn-primary">Edit</a>
                                    <a href="{{ route('frontend.flat.list', $house->id) }}" class="btn-sm btn-info">View
                                        Flat</a>
                                    <a href="{{ route('frontend.flat.create', $house->id) }}"
                                        class="btn-sm btn-warning">Add Flat</a>
                                    <button type="button" class="btn-sm btn-danger" data-toggle="modal" data-target="#deleteModal{{ $house->id }}">Delete</button>

                                    <!-- Delete Confirmation Modal -->
                                    <div class="modal fade" id="deleteModal{{ $house->id }}" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel{{ $house->id }}" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteModalLabel{{ $house->id }}">Delete House</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Delete house with flats. Are you sure?</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <form action="{{ route('frontend.house.delete', $house->id) }}" method="get">
                                                        @csrf
                                                        <button type="submit" class="btn btn-primary">Confirm</button>
                                                    </form>
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach

                    </tbody>

                </table>
            </div>
        </div>
    </div>
@endsection
