{{-- resources/views/admin/coupons/index.blade.php --}}
@extends('layouts.layout')

@section('title', 'Coupon Management')

@section('content')
<div class="pd-ltr-20 xs-pd-20-10">
    <div class="min-height-200px">

        <!-- Page Header -->
        <div class="page-header">
            <div class="row">
                <div class="col-md-6 col-sm-12">
                    <div class="title">
                        <h4>Coupon Management</h4>
                    </div>
                    <nav aria-label="breadcrumb" role="navigation">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.dashboard') }}">Home</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Coupons</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-md-6 col-sm-12 text-right">
                    <button class="btn btn-success" data-toggle="modal" data-target="#addCouponModal">
                        + Add New Coupon
                    </button>
                </div>
            </div>
        </div>
        <!-- End Page Header -->

        <!-- Coupon List Card -->
        <div class="card-box mb-30">
            <div class="pd-20">
                <h4 class="text-blue h4">Coupon List</h4>
            </div>
            <div class="pb-20">
                <table class="data-table table stripe hover nowrap dt-responsive" style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Code</th>
                            <th>Type</th>
                            <th>Value</th>
                            <th>Min Order</th>
                            <th>Usage (Used/Limit)</th>
                            <th>Expires At</th>
                            <th>Status</th>
                            <th class="datatable-nosort">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($coupons as $coupon)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $coupon->code }}</td>
                            <td>{{ ucfirst($coupon->type) }}</td>
                            <td>
                                @if($coupon->type === 'percent')
                                    {{ $coupon->value }}%
                                @elseif($coupon->type === 'fixed')
                                    ₺{{ number_format($coupon->value,2) }}
                                @else
                                    Free Shipping
                                @endif
                            </td>
                            <td>
                                @if($coupon->min_order_amount !== null)
                                    ₺{{ number_format($coupon->min_order_amount,2) }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                {{ $coupon->used_count }}
                                @if($coupon->usage_limit)
                                    / {{ $coupon->usage_limit }}
                                @endif
                            </td>
                            <td>
                                @if($coupon->expires_at)
                                    {{ $coupon->expires_at->format('d.m.Y') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $coupon->active ? 'badge-success' : 'badge-danger' }}">
                                    {{ $coupon->active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <a class="btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle"
                                       href="#" role="button" data-toggle="dropdown">
                                        <i class="dw dw-more"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
                                        <!-- Edit -->
                                        <a class="dropdown-item" data-toggle="modal"
                                           data-target="#editCouponModal{{ $coupon->id }}" href="#">
                                            <i class="dw dw-edit2"></i> Edit
                                        </a>
                                        <!-- Toggle Active/Inactive -->
                                        <a class="dropdown-item" href="#"
                                           onclick="event.preventDefault(); document.getElementById('toggle-coupon-{{ $coupon->id }}').submit();">
                                            <i class="dw {{ $coupon->active ? 'dw-ban' : 'dw-check' }}"></i>
                                            {{ $coupon->active ? 'Deactivate' : 'Activate' }}
                                        </a>
                                        <form id="toggle-coupon-{{ $coupon->id }}"
                                              action="{{ route('admin.coupons.toggle', $coupon->id) }}"
                                              method="POST" style="display: none;">
                                            @csrf
                                            @method('PUT')
                                        </form>
                                        <!-- Delete -->
                                        <a class="dropdown-item text-danger" href="#"
                                           onclick="event.preventDefault(); document.getElementById('delete-coupon-{{ $coupon->id }}').submit();">
                                            <i class="dw dw-delete-3"></i> Delete
                                        </a>
                                        <form id="delete-coupon-{{ $coupon->id }}"
                                              action="{{ route('admin.coupons.destroy', $coupon->id) }}"
                                              method="POST" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>

                        <!-- Edit Coupon Modal -->
                        <div class="modal fade" id="editCouponModal{{ $coupon->id }}" tabindex="-1"
                             role="dialog" aria-labelledby="editCouponModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Edit Coupon: {{ $coupon->code }}</h4>
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                    </div>
                                    <form action="{{ route('admin.coupons.update', $coupon->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label>Code</label>
                                                <input type="text" name="code" class="form-control"
                                                       value="{{ $coupon->code }}" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Type</label>
                                                <select name="type" class="form-control" required>
                                                    <option value="fixed" {{ $coupon->type=='fixed'?'selected':'' }}>
                                                        Fixed (₺)
                                                    </option>
                                                    <option value="percent" {{ $coupon->type=='percent'?'selected':'' }}>
                                                        Percent (%)
                                                    </option>
                                                    <option value="free_shipping" {{ $coupon->type=='free_shipping'?'selected':'' }}>
                                                        Free Shipping
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Value</label>
                                                <input type="number" step="0.01" min="0" name="value"
                                                       class="form-control" value="{{ $coupon->value }}" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Min Order Amount (Optional)</label>
                                                <input type="number" step="0.01" min="0" name="min_order_amount"
                                                       class="form-control" value="{{ $coupon->min_order_amount }}">
                                            </div>
                                            <div class="form-group">
                                                <label>Usage Limit (Optional)</label>
                                                <input type="number" min="1" name="usage_limit"
                                                       class="form-control" value="{{ $coupon->usage_limit }}">
                                            </div>
                                            <div class="form-group">
                                                <label>Expires At (Optional)</label>
                                                <input type="date" name="expires_at" class="form-control"
                                                       value="{{ $coupon->expires_at ? $coupon->expires_at->format('Y-m-d') : '' }}">
                                            </div>
                                            <div class="form-group">
                                                <label>Status</label>
                                                <select name="active" class="form-control" required>
                                                    <option value="1" {{ $coupon->active ? 'selected' : '' }}>Active</option>
                                                    <option value="0" {{ !$coupon->active ? 'selected' : '' }}>Inactive</option>
                                                </select>
                                            </div>

                                            <!-- Products multi-select -->
                                            <div class="form-group">
                                                <label>Products <small>(Select one or more)</small></label>
                                                <select name="product_ids[]" class="form-control" multiple>
                                                    @php
                                                        $sel = $coupon->products->pluck('id')->toArray();
                                                    @endphp
                                                    @foreach($products as $p)
                                                        <option value="{{ $p->id }}"
                                                            {{ in_array($p->id, $sel) ? 'selected' : '' }}>
                                                            {{ $p->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary">Save</button>
                                            <button type="button" class="btn btn-secondary"
                                                    data-dismiss="modal">Cancel</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- End Edit Coupon Modal -->

                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <!-- End Coupon List Card -->

    </div>
</div>

<!-- Add Coupon Modal -->
<div class="modal fade" id="addCouponModal" tabindex="-1" role="dialog"
     aria-labelledby="addCouponModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add New Coupon</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <form action="{{ route('admin.coupons.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Code</label>
                        <input type="text" name="code" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Type</label>
                        <select name="type" class="form-control" required>
                            <option value="fixed">Fixed (₺)</option>
                            <option value="percent">Percent (%)</option>
                            <option value="free_shipping">Free Shipping</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Value</label>
                        <input type="number" step="0.01" min="0" name="value" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Min Order Amount (Optional)</label>
                        <input type="number" step="0.01" min="0" name="min_order_amount" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Usage Limit (Optional)</label>
                        <input type="number" min="1" name="usage_limit" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Expires At (Optional)</label>
                        <input type="date" name="expires_at" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="active" class="form-control" required>
                            <option value="1" selected>Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>

                    <!-- Products multi-select -->
                    <div class="form-group">
                        <label>Products <small>(Select one or more)</small></label>
                        <select name="product_ids[]" class="form-control" multiple>
                            @foreach($products as $p)
                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Add</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
