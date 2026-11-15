<div class="admin-card">
    <div class="admin-card-body">
        @if ($errors->any())
            <div class="alert alert-danger" role="alert"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
        @endif
        <form method="POST" action="{{ $action }}">
            @csrf
            @if ($method !== 'POST') @method($method) @endif
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label for="service-business" class="form-label">Business</label>
                    <select id="service-business" name="business_id" class="form-select" required>
                        <option value="">Choose a business</option>
                        @foreach ($businesses as $business)
                            <option value="{{ $business->business_id }}" @selected((string) old('business_id', $service?->business_id) === (string) $business->business_id)>{{ $business->business_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-6">
                    <label for="service-name" class="form-label">Service name</label>
                    <input id="service-name" name="service_name" class="form-control" maxlength="255" value="{{ old('service_name', $service?->service_name) }}" required>
                </div>
                <div class="col-12 col-md-6">
                    <label for="service-type" class="form-label">Service type</label>
                    <input id="service-type" name="service_type" class="form-control" maxlength="255" value="{{ old('service_type', $service?->service_type) }}" required>
                </div>
                <div class="col-12 col-md-6">
                    <label for="service-price" class="form-label">Price (Rs.)</label>
                    <input id="service-price" name="price" type="number" min="0" max="99999999.99" step="0.01" class="form-control" value="{{ old('price', $service?->price) }}" required>
                </div>
                <div class="col-12 col-md-6">
                    <label for="service-status" class="form-label">Availability</label>
                    <select id="service-status" name="availability_status" class="form-select" required>
                        <option value="available" @selected(old('availability_status', $service?->availability_status ?? 'available') === 'available')>Available</option>
                        <option value="unavailable" @selected(old('availability_status', $service?->availability_status) === 'unavailable')>Unavailable</option>
                    </select>
                </div>
                <div class="col-12 col-md-6">
                    <label for="service-category" class="form-label">Category (optional)</label>
                    <input id="service-category" name="category" class="form-control" maxlength="255" value="{{ old('category', $service?->category) }}">
                </div>
                <div class="col-12 col-md-6">
                    <label for="served-date" class="form-label">Served date (optional)</label>
                    <input id="served-date" name="served_date" type="date" class="form-control" value="{{ old('served_date', $service?->served_date) }}">
                </div>
                <div class="col-12">
                    <label for="service-description" class="form-label">Description (optional)</label>
                    <textarea id="service-description" name="description" rows="4" class="form-control">{{ old('description', $service?->description) }}</textarea>
                </div>
                <div class="col-12"><button class="btn btn-honey" type="submit">{{ $service ? 'Save Changes' : 'Create Service' }}</button></div>
            </div>
        </form>
    </div>
</div>
