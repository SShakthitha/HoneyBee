@extends('layouts.admin')

@section('title', 'Services')

@section('content')

<div class="topbar">
    <h1>All <span>Services</span></h1>
    <a href="/admin/services/create">+ Add Service</a>
</div>

<!-- GIFT & DESIGN -->
<div style="background: #fff; border-radius: 15px; padding: 30px; box-shadow: 0 5px 20px rgba(0,0,0,0.08); margin-bottom: 30px;">
    <h2 style="margin-bottom: 20px;">🎁 Gift & Design</h2>
    @if($gifts->isEmpty())
        <p style="color: #777;">No gift items yet!</p>
    @else
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f5a623; color: #1a1a1a;">
                    <th style="padding: 12px; text-align: left;">Item Name</th>
                    <th style="padding: 12px; text-align: left;">Category</th>
                    <th style="padding: 12px; text-align: left;">Price</th>
                    <th style="padding: 12px; text-align: left;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($gifts as $gift)
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 12px;">{{ $gift->item_name }}</td>
                    <td style="padding: 12px;">{{ $gift->category }}</td>
                    <td style="padding: 12px;">Rs. {{ number_format($gift->price, 2) }}</td>
                    <td style="padding: 12px;">
                        <a href="/admin/gift/{{ $gift->gift_design_id }}/edit" style="color: #f5a623;">Edit</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

<!-- LASER WORK -->
<div style="background: #fff; border-radius: 15px; padding: 30px; box-shadow: 0 5px 20px rgba(0,0,0,0.08); margin-bottom: 30px;">
    <h2 style="margin-bottom: 20px;">⚡ Laser Work</h2>
    @if($laserWorks->isEmpty())
        <p style="color: #777;">No laser work items yet!</p>
    @else
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f5a623; color: #1a1a1a;">
                    <th style="padding: 12px; text-align: left;">Product Name</th>
                    <th style="padding: 12px; text-align: left;">Category</th>
                    <th style="padding: 12px; text-align: left;">Price</th>
                    <th style="padding: 12px; text-align: left;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($laserWorks as $laserWork)
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 12px;">{{ $laserWork->product_name }}</td>
                    <td style="padding: 12px;">{{ $laserWork->product_category }}</td>
                    <td style="padding: 12px;">Rs. {{ number_format($laserWork->price, 2) }}</td>
                    <td style="padding: 12px;">
                        <a href="/admin/laser/{{ $laserWork->laser_id }}/edit" style="color: #f5a623;">Edit</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

<!-- EVENTS -->
<div style="background: #fff; border-radius: 15px; padding: 30px; box-shadow: 0 5px 20px rgba(0,0,0,0.08);">
    <h2 style="margin-bottom: 20px;">🎉 Events</h2>
    @if($events->isEmpty())
        <p style="color: #777;">No events yet!</p>
    @else
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f5a623; color: #1a1a1a;">
                    <th style="padding: 12px; text-align: left;">Event Name</th>
                    <th style="padding: 12px; text-align: left;">Type</th>
                    <th style="padding: 12px; text-align: left;">Date</th>
                    <th style="padding: 12px; text-align: left;">Price</th>
                    <th style="padding: 12px; text-align: left;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($events as $event)
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 12px;">{{ $event->event_name }}</td>
                    <td style="padding: 12px;">{{ $event->event_type }}</td>
                    <td style="padding: 12px;">{{ $event->event_date }}</td>
                    <td style="padding: 12px;">Rs. {{ number_format($event->price, 2) }}</td>
                    <td style="padding: 12px;">
                        <a href="/admin/event/{{ $event->event_id }}/edit" style="color: #f5a623;">Edit</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

@endsection