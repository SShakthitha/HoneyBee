@extends('layouts.admin')

@section('title','Upload Gallery Image')

@section('content')

<div class="admin-card form-box">

    <div class="admin-card-header">
        <h3>Upload Gallery Image</h3>
    </div>


    <div class="admin-card-body">

        <form action="{{ route('admin.gallery.store') }}"
              method="POST"
              enctype="multipart/form-data">

            @csrf


            <div class="mb-3">

                <label class="form-label">
                    Title (Optional)
                </label>

                <input type="text"
                       name="title"
                       class="form-control"
                       value="{{ old('title') }}">

            </div>



            <div class="mb-3">

                <label class="form-label">
                    Category
                </label>

                <select name="category"
                        class="form-control"
                        required>

                    <option value="">
                        Select Category
                    </option>

                    <option value="Gift & Design" @selected(old('category') === 'Gift & Design')>
                        Gifts
                    </option>

                    <option value="Frame Designs" @selected(old('category') === 'Frame Designs')>
                        Frame Designs
                    </option>

                    <option value="Laser Work" @selected(old('category') === 'Laser Work')>
                        Laser Work
                    </option>

                    <option value="Events" @selected(old('category') === 'Events')>
                        Events
                    </option>

                </select>

            </div>



            <div class="mb-3">

                <label class="form-label">
                    Image
                </label>

                <input type="file"
                       name="image"
                       class="form-control"
                       accept="image/*"
                       required>

            </div>



            <button class="btn btn-honey">
                Upload
            </button>

            <a href="{{ route('admin.gallery.index') }}"
               class="btn btn-secondary">
                Cancel
            </a>


        </form>

    </div>

</div>

@endsection
