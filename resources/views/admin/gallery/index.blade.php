@extends('layouts.admin')

@section('title', 'Gallery Management')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Gallery Management</h3>

        <a href="{{ route('admin.gallery.create') }}"
            class="btn btn-honey">
            <i class="fa-solid fa-upload"></i>
            Upload Image
        </a>
    </div>

    @if($images->count())

        <div class="row">

            @foreach($images as $image)

                <div class="col-md-3 mb-4">

                    <div class="card">

                        <img src="{{ asset('storage/'.$image->image) }}"
                             class="card-img-top"
                             alt="{{ $image->title ?? $image->category . ' gallery image' }}"
                             style="height: 180px; object-fit: cover;">

                        <div class="card-body">

                            <h6>{{ $image->title ?? 'No Title' }}</h6>

                            <span class="badge bg-warning">
                                {{ $image->category }}
                            </span>

                            <div class="d-flex justify-content-end gap-2 mt-3">

                            <a href="{{ route('admin.gallery.edit', $image->id) }}"
                              class="btn btn-sm btn-outline-primary"
                              title="Edit">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>

                            <form action="{{ route('admin.gallery.delete', $image->id) }}"
                                  method="POST"
                                  onsubmit="return confirm('Delete this gallery image?');">
                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                        class="btn btn-sm btn-outline-danger"
                                        title="Delete">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>

                        </div>

                        </div>

                    </div>

                </div>

            @endforeach

        </div>

    @else

        <div class="alert alert-info">
            No gallery images uploaded yet.
        </div>

    @endif

</div>

@endsection
