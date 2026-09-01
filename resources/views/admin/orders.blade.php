@extends('layouts.admin')

@section('title', 'Orders')

@section('content')

<div class="topbar">
    <h1>All <span>Orders</span></h1>
</div>

<div class="btn-group mb-3" role="tablist" aria-label="Order categories">
    <button type="button" class="btn btn-honey order-category-tab active" data-category="">All Orders</button>
    <button type="button" class="btn btn-outline-secondary order-category-tab" data-category="gift-design">Gift &amp; Design</button>
    <button type="button" class="btn btn-outline-secondary order-category-tab" data-category="laser-work">Laser Work</button>
    <button type="button" class="btn btn-outline-secondary order-category-tab" data-category="events">Events</button>
</div>

@if(session('success'))
    <div style="
        background:#d4edda;
        color:#155724;
        padding:15px;
        border-radius:10px;
        margin-bottom:20px;
    ">
        {{ session('success') }}
    </div>
@endif

<div style="
    background:#fff;
    border-radius:15px;
    padding:30px;
    box-shadow:0 5px 20px rgba(0,0,0,0.08);
">

    <div style="overflow-x:auto;">

        <table
            id="ordersTable"
            class="table table-hover align-middle"
            style="width:100%;"
        >

            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer ID</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
            </tbody>

        </table>

    </div>

</div>

@push('styles')

<link
    rel="stylesheet"
    href="https://cdn.datatables.net/2.3.3/css/dataTables.dataTables.min.css"
>

<style>

    #ordersTable thead th {
        background: #f8f8f8;
        padding: 14px;
        white-space: nowrap;
    }

    #ordersTable tbody td {
        padding: 14px;
        vertical-align: middle;
    }

    #ordersTable tbody tr {
        border-bottom: 1px solid #eee;
    }

    .dataTables_wrapper {
        width: 100%;
    }

</style>

@endpush

@push('scripts')

<script src="https://cdn.datatables.net/2.3.3/js/dataTables.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const ordersTable = new DataTable('#ordersTable', {

        processing: true,
        serverSide: true,

        ajax: {
            url: @json(route('admin.orders.data')),
            type: 'GET',
            data: function (data) {
                data.category = document.querySelector('.order-category-tab.active')?.dataset.category || '';
            }
        },

        columns: [
            {
                data: 'order_id',
                name: 'order_id',
                render: function (data) {
                    return '#' + data;
                }
            },

            {
                data: 'customer_id',
                name: 'customer_id'
            },

            {
                data: 'customer',
                name: 'customer',
                orderable: false,
                searchable: false
            },

            {
                data: 'date',
                name: 'order_date'
            },

            {
                data: 'items',
                name: 'items',
                orderable: false,
                searchable: false
            },

            {
                data: 'total',
                name: 'total',
                orderable: false,
                searchable: false
            },

            {
                data: 'status',
                name: 'status'
            },

            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            }
        ],

        order: [
            [0, 'desc']
        ],

        pageLength: 10,

        lengthMenu: [
            [10, 25, 50, 100],
            [10, 25, 50, 100]
        ],

        responsive: true

    });

    document.querySelectorAll('.order-category-tab').forEach(function (tab) {
        tab.addEventListener('click', function () {
            document.querySelectorAll('.order-category-tab').forEach(function (button) {
                button.classList.remove('active', 'btn-honey');
                button.classList.add('btn-outline-secondary');
            });

            tab.classList.add('active', 'btn-honey');
            tab.classList.remove('btn-outline-secondary');
            ordersTable.ajax.reload();
        });
    });

});
</script>

@endpush

@endsection
