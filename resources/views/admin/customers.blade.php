@extends('layouts.admin')

@section('title', 'Customers')

@section('content')

<div class="topbar">
    <h1>All <span>Customers</span></h1>
</div>

<div style="display:flex; justify-content:space-between; align-items:center; margin:15px 0;">

    <!-- Left side: Add button -->
    <a href="{{ route('admin.customers.create') }}"
       style="background: #f5a623; color: black; padding: 8px 12px; text-decoration: none; border-radius: 5px;">
        <b>+ Add Customer</b>
    </a>

    <!-- Right side: Search -->
    <form method="GET" action="{{ route('admin.customers') }}" style="display:flex; gap:5px;">
        <input type="text"
               name="search"
               placeholder="Search..."
               value="{{ request('search') }}"
               style="padding:8px; width:200px;">

        <button type="submit" style="padding:8px; background: #f5a623; color: black;">
            <b>Search</b>
        </button>
    </form>

</div>

<div style="background: #fff; border-radius: 15px; padding: 30px; box-shadow: 0 5px 20px rgba(0,0,0,0.08);">
    @if($customers->isEmpty())
        <p style="text-align: center; color: #777; padding: 40px;">No customers yet! 🐝</p>
    @else
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f5a623; color: #1a1a1a;">
                    <th style="padding: 12px; text-align: left;">ID</th>
                    <th style="padding: 12px; text-align: left;">Name</th>
                    <th style="padding: 12px; text-align: left;">Email</th>
                    <th style="padding: 12px; text-align: left;">Phone</th>
                    <th style="padding: 12px; text-align: left;">Total Spent</th>
                    <th style="padding: 12px; text-align: left;">Registered</th>
                    <th style="padding: 12px; text-align: left;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($customers as $customer)
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 12px;">#{{ $customer->customer_id }}</td>
                    <td style="padding: 12px;">{{ $customer->full_name }}</td>
                    <td style="padding: 12px;">{{ $customer->email }}</td>
                    <td style="padding: 12px;">{{ $customer->phone }}</td>
                    <td style="padding: 12px;">Rs. {{ number_format($customer->total_spent, 2) }}</td>
                    <td style="padding: 12px;">{{ $customer->registered_date }}</td>
                    <td>
                        <a href="{{ route('admin.customers.edit', $customer->customer_id) }}"
                            title="Edit"
                            style="color:blue; font-size:16px; margin-right:10px; text-decoration:none;">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>

                        <form action="{{ route('admin.customers.delete', $customer->customer_id) }}"
                            method="POST"
                            style="display:inline;"
                            onsubmit="return confirm('Do you want to remove!');">

                          @csrf
                          @method('DELETE')

                          <button type="submit"
                              title="Delete"
                              style="background:none;border:none;cursor:pointer;color:red;font-size:16px;">
                              <i class="fa-solid fa-trash"></i>
                          </button>
                        </form>
                    </td>
                </tr>
                
                @endforeach
            </tbody>
        </table>
    @endif
</div>

@endsection