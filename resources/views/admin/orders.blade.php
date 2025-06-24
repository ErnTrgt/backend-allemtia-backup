@extends('layouts.layout')

@section('title', 'Orders')

@section('content')
    <!-- CSRF Token meta tag -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <div class="pd-ltr-20 xs-pd-20-10">
        <div class="min-height-200px">
            <div class="page-header">
                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <div class="title">
                            <h4>Orders Management</h4>
                        </div>
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Orders</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="col-md-6 col-sm-12 text-right">
                        <div class="dropdown">
                            <a class="btn btn-primary dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                                Filter By Status
                            </a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="{{ route('admin.orders') }}">All</a>
                                <a class="dropdown-item" href="{{ route('admin.orders', ['status' => 'pending']) }}">Pending</a>
                                <a class="dropdown-item" href="{{ route('admin.orders', ['status' => 'waiting_payment']) }}">Waiting Payment</a>
                                <a class="dropdown-item" href="{{ route('admin.orders', ['status' => 'paid']) }}">Paid</a>
                                <a class="dropdown-item" href="{{ route('admin.orders', ['status' => 'processing']) }}">Processing</a>
                                <a class="dropdown-item" href="{{ route('admin.orders', ['status' => 'shipped']) }}">Shipped</a>
                                <a class="dropdown-item" href="{{ route('admin.orders', ['status' => 'delivered']) }}">Delivered</a>
                                <a class="dropdown-item" href="{{ route('admin.orders', ['status' => 'cancelled']) }}">Cancelled</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-box mb-30">
                <div class="pd-20">
                    <h4 class="text-blue h4">Orders List</h4>
                </div>
                <div class="pb-20">
                    <table class="data-table table stripe hover nowrap">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Order Number</th>
                                <th>Customer</th>
                                <th>Email</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Payment Method</th>
                                <th>Date</th>
                                <th class="datatable-nosort">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $order)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $order->order_number }}</td>
                                    <td>{{ $order->customer_name }}</td>
                                    <td>{{ $order->customer_email }}</td>
                                    <td>₺{{ number_format($order->total, 2) }}</td>
                                    <td>
                                        <span class="badge 
                                            @switch($order->status)
                                                @case('pending') badge-warning @break
                                                @case('waiting_payment') badge-info @break
                                                @case('paid') badge-success @break
                                                @case('processing') badge-primary @break
                                                @case('shipped') badge-info @break
                                                @case('delivered') badge-success @break
                                                @case('cancelled') badge-danger @break
                                                @default badge-secondary
                                            @endswitch
                                        ">
                                            @switch($order->status)
                                                @case('pending') Beklemede @break
                                                @case('waiting_payment') Ödeme Bekleniyor @break
                                                @case('paid') Ödendi @break
                                                @case('processing') Hazırlanıyor @break
                                                @case('shipped') Kargoda @break
                                                @case('delivered') Teslim Edildi @break
                                                @case('cancelled') İptal Edildi @break
                                                @default {{ ucfirst($order->status) }}
                                            @endswitch
                                        </span>
                                    </td>
                                    <td>
                                        @switch($order->payment_method)
                                            @case('eft') EFT/Havale @break
                                            @case('cash_on_delivery') Kapıda Nakit @break
                                            @default {{ ucfirst($order->payment_method) }}
                                        @endswitch
                                    </td>
                                    <td>{{ $order->created_at->format('d M Y H:i') }}</td>
                                    <td>
                                        <div class="dropdown">
                                            <a class="btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle"
                                                href="#" role="button" data-toggle="dropdown">
                                                <i class="dw dw-more"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
                                                <a class="dropdown-item"
                                                    href="{{ route('admin.orders.show', $order->id) }}">
                                                    <i class="dw dw-eye"></i> View Details
                                                </a>
                                                <a class="dropdown-item" data-toggle="modal"
                                                    data-target="#editOrderModal{{ $order->id }}" href="#">
                                                    <i class="dw dw-edit2"></i> Edit Status
                                                </a>
                                                <a class="dropdown-item" href="#" onclick="printInvoice({{ $order->id }})">
                                                    <i class="dw dw-print"></i> Print Invoice
                                                </a>
                                                <a class="dropdown-item" href="#"
                                                    onclick="if(confirm('Are you sure you want to delete this order?')) deleteOrder({{ $order->id }});">
                                                    <i class="dw dw-delete-3"></i> Delete
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Edit Order Status Modal -->
                                <div class="modal fade" id="editOrderModal{{ $order->id }}" tabindex="-1"
                                    role="dialog" aria-labelledby="editOrderModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-lg modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title">Update Order - {{ $order->order_number }}</h4>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-hidden="true">×</button>
                                            </div>
                                            <form action="{{ route('admin.orders') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="order_id" value="{{ $order->id }}">
                                                <input type="hidden" name="action" value="update_status">
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="orderStatus{{ $order->id }}">Order Status</label>
                                                                <select name="status" id="orderStatus{{ $order->id }}" class="form-control" required>
                                                                    <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Beklemede</option>
                                                                    <option value="waiting_payment" {{ $order->status == 'waiting_payment' ? 'selected' : '' }}>Ödeme Bekleniyor</option>
                                                                    <option value="paid" {{ $order->status == 'paid' ? 'selected' : '' }}>Ödendi</option>
                                                                    <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Hazırlanıyor</option>
                                                                    <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Kargoda</option>
                                                                    <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Teslim Edildi</option>
                                                                    <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>İptal Edildi</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="trackingNumber{{ $order->id }}">Tracking Number</label>
                                                                <input type="text" name="tracking_number" id="trackingNumber{{ $order->id }}"
                                                                    class="form-control" value="{{ $order->tracking_number ?? '' }}"
                                                                    placeholder="Enter tracking number">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="form-group">
                                                        <label for="statusNote{{ $order->id }}">Status Update Note</label>
                                                        <textarea name="status_note" id="statusNote{{ $order->id }}" class="form-control" rows="3"
                                                            placeholder="Add a note about this status update (optional)..."></textarea>
                                                    </div>

                                                    <!-- Cancel Reason (sadece iptal seçildiğinde göster) -->
                                                    <div class="form-group" id="cancelReasonGroup{{ $order->id }}" style="{{ $order->status == 'cancelled' ? 'display: block;' : 'display: none;' }}">
                                                        <label for="cancelReason{{ $order->id }}">Cancellation Reason</label>
                                                        <textarea name="cancel_reason" id="cancelReason{{ $order->id }}" class="form-control" rows="2"
                                                            placeholder="Enter reason for cancellation...">{{ $order->cancellation_reason ?? '' }}</textarea>
                                                    </div>

                                                    <!-- Order Details -->
                                                    <div class="mt-4">
                                                        <h6 class="text-blue">Order Information</h6>
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <p><strong>Customer:</strong> {{ $order->customer_name }}</p>
                                                                <p><strong>Phone:</strong> {{ $order->customer_phone }}</p>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <p><strong>Email:</strong> {{ $order->customer_email }}</p>
                                                                <p><strong>Payment:</strong> 
                                                                    @switch($order->payment_method)
                                                                        @case('eft') EFT/Havale @break
                                                                        @case('cash_on_delivery') Kapıda Nakit @break
                                                                        @default {{ ucfirst($order->payment_method) }}
                                                                    @endswitch
                                                                </p>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <p><strong>Total:</strong> ₺{{ number_format($order->total, 2) }}</p>
                                                                <p><strong>Date:</strong> {{ $order->created_at->format('d M Y H:i') }}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-primary">
                                                        Update Order
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <!-- Edit Order Status Modal End -->
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Status değiştiğinde cancel reason göster/gizle
        document.addEventListener('DOMContentLoaded', function() {
            @foreach ($orders as $order)
                document.getElementById('orderStatus{{ $order->id }}').addEventListener('change', function() {
                    const cancelGroup = document.getElementById('cancelReasonGroup{{ $order->id }}');
                    if (this.value === 'cancelled') {
                        cancelGroup.style.display = 'block';
                    } else {
                        cancelGroup.style.display = 'none';
                    }
                });
            @endforeach
        });

        // Notification fonksiyonu
        function showNotification(message, type = 'info') {
            // Mevcut notification varsa kaldır
            const existingNotification = document.querySelector('.custom-notification');
            if (existingNotification) {
                existingNotification.remove();
            }
            
            // SweetAlert kullanıyorsanız
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: type === 'success' ? 'success' : type === 'error' ? 'error' : 'info',
                    title: type === 'success' ? 'Başarılı!' : type === 'error' ? 'Hata!' : 'Bilgi',
                    text: message,
                    showConfirmButton: true,
                    timer: 4000,
                    timerProgressBar: true
                });
            }
            // Custom notification div
            else {
                const notificationDiv = document.createElement('div');
                notificationDiv.className = `custom-notification alert alert-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info'} alert-dismissible fade show position-fixed`;
                notificationDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
                notificationDiv.innerHTML = `
                    <strong>${type === 'success' ? 'Başarılı!' : type === 'error' ? 'Hata!' : 'Bilgi:'}</strong> ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                
                document.body.appendChild(notificationDiv);
                
                // 5 saniye sonra otomatik kaldır
                setTimeout(() => {
                    if (notificationDiv.parentNode) {
                        notificationDiv.remove();
                    }
                }, 5000);
            }
        }

        // Notification fonksiyonu (sadece başarı/hata mesajları için)
        @if(session('success'))
            showNotification('{{ session('success') }}', 'success');
        @endif
        
        @if(session('error'))
            showNotification('{{ session('error') }}', 'error');
        @endif

        function printInvoice(orderId) {
            const printUrl = `/admin/orders/${orderId}/invoice`;
            window.open(printUrl, '_blank');
        }

        function deleteOrder(orderId) {
            // AJAX ile delete işlemi
            fetch(`/admin/orders/${orderId}/delete`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Order deleted successfully', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showNotification(data.message || 'Error deleting order', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Delete functionality needs to be implemented', 'error');
            });
        }
    </script>
@endsection