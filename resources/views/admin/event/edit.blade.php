<form action="/admin/event/update/{{ $event->event_id }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <input type="text" name="event_name" value="{{ $event->event_name }}">

    <input type="text" name="event_type" value="{{ $event->event_type }}">

    <input type="number" name="price" value="{{ $event->price }}">

    <input type="number" name="offer_price" value="{{ $event->offer_price }}">

    <textarea name="description">{{ $event->description }}</textarea>

    <!-- IMAGE -->
    <input type="file" name="image">

    @if($event->image)
        <img src="{{ asset('storage/' . $event->image) }}" width="120">
    @endif

    <button type="submit">Update Event</button>
</form>