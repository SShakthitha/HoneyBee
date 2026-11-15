@extends('layouts.admin')

@section('title', 'Services')

@section('content')
<div class="topbar">
    <h1>All <span>Services</span></h1>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('admin.gift.create') }}" class="btn btn-honey">+ Add Gift Item</a>
        <a href="{{ route('admin.laser.create') }}" class="btn btn-dark">+ Add Laser Work</a>
        <a href="{{ route('admin.event.create') }}" class="btn btn-honey">+ Add Event</a>
    </div>
</div>

<div class="admin-card mb-4">
    <div class="admin-card-header"><h2 class="h4 mb-0">Gift &amp; Design</h2></div>
    <div class="admin-card-body">
        @if($gifts->isEmpty()) <p class="text-muted mb-0">No gift items yet!</p> @else
        <div class="table-responsive"><table class="table table-hover admin-datatable" style="width:100%;">
            <thead><tr><th>Image</th><th>Item Name</th><th>Category</th><th>Material</th><th>Size</th><th>Price</th><th>Offer Price</th><th>Customization</th><th>Description</th><th>Actions</th></tr></thead>
            <tbody>@foreach($gifts as $gift)<tr>
                <td>@if($gift->image)<img src="{{ asset('storage/' . $gift->image) }}" alt="{{ $gift->item_name }}" style="width:48px;height:48px;object-fit:cover;border-radius:6px;">@else <span class="text-muted">-</span> @endif</td>
                <td>{{ $gift->item_name }}</td><td>{{ $gift->category }}</td><td>{{ $gift->material ?: '-' }}</td><td>{{ $gift->size ?: '-' }}</td><td>Rs. {{ number_format((float) $gift->price, 2) }}</td><td>{{ $gift->offer_price !== null ? 'Rs. ' . number_format((float) $gift->offer_price, 2) : '-' }}</td><td>{{ $gift->customization_option ?: '-' }}</td><td>{{ $gift->description ?: '-' }}</td>
                <td><div class="action-buttons"><a href="{{ route('admin.gift.edit', $gift->gift_design_id) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="fa-solid fa-pen-to-square"></i></a><form action="{{ route('admin.gift.destroy', $gift->gift_design_id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this gift item?');">@csrf @method('DELETE')<button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"><i class="fa-solid fa-trash"></i></button></form></div></td>
            </tr>@endforeach</tbody>
        </table></div>@endif
    </div>
</div>

<div class="admin-card mb-4">
    <div class="admin-card-header"><h2 class="h4 mb-0">Laser Work</h2></div>
    <div class="admin-card-body">
        @if($laserWorks->isEmpty()) <p class="text-muted mb-0">No laser work items yet!</p> @else
        <div class="table-responsive"><table class="table table-hover admin-datatable" style="width:100%;">
            <thead><tr><th>Product Name</th><th>Laser Type</th><th>Material Type</th><th>Product Category</th><th>Size</th><th>Price</th><th>Offer Price</th><th>Engraving Text</th><th>Description</th><th>Actions</th></tr></thead>
            <tbody>@foreach($laserWorks as $laserWork)<tr>
                <td>{{ $laserWork->product_name }}</td><td>{{ $laserWork->laser_type }}</td><td>{{ $laserWork->material_type ?: '-' }}</td><td>{{ $laserWork->product_category ?: '-' }}</td><td>{{ $laserWork->size ?: '-' }}</td><td>Rs. {{ number_format((float) $laserWork->price, 2) }}</td><td>{{ $laserWork->offer_price !== null ? 'Rs. ' . number_format((float) $laserWork->offer_price, 2) : '-' }}</td><td>{{ $laserWork->engraving_text ?: '-' }}</td><td>{{ $laserWork->description ?: '-' }}</td>
                <td><div class="action-buttons"><a href="{{ route('admin.laser.edit', $laserWork->laser_id) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="fa-solid fa-pen-to-square"></i></a><form action="{{ route('admin.laser.destroy', $laserWork->laser_id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this laser work?');">@csrf @method('DELETE')<button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"><i class="fa-solid fa-trash"></i></button></form></div></td>
            </tr>@endforeach</tbody>
        </table></div>@endif
    </div>
</div>

<div class="admin-card">
    <div class="admin-card-header"><h2 class="h4 mb-0">Events</h2></div>
    <div class="admin-card-body">
        @if($events->isEmpty()) <p class="text-muted mb-0">No events yet!</p> @else
        <div class="table-responsive"><table class="table table-hover admin-datatable" style="width:100%;">
            <thead><tr><th>Event Name</th><th>Event Type</th><th>Decoration Type</th><th>Event Date</th><th>Location</th><th>Lighting</th><th>Sound</th><th>DJ</th><th>Photography</th><th>Cake</th><th>Price</th><th>Offer Price</th><th>Description</th><th>Actions</th></tr></thead>
            <tbody>@foreach($events as $event)<tr>
                <td>{{ $event->event_name }}</td><td>{{ $event->event_type }}</td><td>{{ $event->decoration_type ?: '-' }}</td><td>{{ $event->event_date?->format('d M Y') ?? '-' }}</td><td>{{ $event->event_location ?: '-' }}</td><td>{{ $event->lighting_service ? 'Yes' : 'No' }}</td><td>{{ $event->sound_service ? 'Yes' : 'No' }}</td><td>{{ $event->dj_service ? 'Yes' : 'No' }}</td><td>{{ $event->photography_service ? 'Yes' : 'No' }}</td><td>{{ $event->cake_service ? 'Yes' : 'No' }}</td><td>Rs. {{ number_format((float) $event->price, 2) }}</td><td>{{ $event->offer_price !== null ? 'Rs. ' . number_format((float) $event->offer_price, 2) : '-' }}</td><td>{{ $event->description ?: '-' }}</td>
                <td><div class="action-buttons"><a href="{{ route('admin.event.edit', $event->event_id) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="fa-solid fa-pen-to-square"></i></a><form action="{{ route('admin.event.destroy', $event->event_id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this event?');">@csrf @method('DELETE')<button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"><i class="fa-solid fa-trash"></i></button></form></div></td>
            </tr>@endforeach</tbody>
        </table></div>@endif
    </div>
</div>
@endsection
