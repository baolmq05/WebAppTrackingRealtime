<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery - Shipper Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-light">
    <div class="container py-5">
        
        <!-- Alerts -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert">
                <strong>Success!</strong> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert">
                <strong>Error!</strong> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Header -->
        <div class="mb-4 d-flex justify-content-between align-items-center bg-white p-3 rounded shadow-sm">
            <h1 class="h3 m-0 fw-bold text-dark">Shipper Dashboard</h1>
            <div class="d-flex align-items-center gap-3">
                <span class="fs-6 text-muted">
                    Welcome back, <strong>{{ Auth::user()->name ?? 'Guest' }}</strong>
                    @if(Auth::user())
                        <span class="badge bg-info text-dark ms-1">
                            Role: {{ Auth::user()->role === 1 ? 'Shipper' : 'Buyer' }}
                        </span>
                    @endif
                </span>

                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger btn-sm">Logout</button>
                </form>
            </div>
        </div>

        <div class="row g-4">
            <!-- Left Column: Available Orders -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title fw-bold text-primary m-0">Available Orders (Unassigned)</h5>
                    </div>
                    <div class="card-body">
                        @if($availableOrders->isEmpty())
                            <div class="text-center py-5 text-muted">
                                <p class="mb-0">No unassigned orders available at the moment.</p>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Product</th>
                                            <th>Buyer</th>
                                            <th>Delivery Address</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($availableOrders as $order)
                                            <tr>
                                                <td class="fw-bold">#{{ $order->id }}</td>
                                                <td>
                                                    <span class="fw-semibold text-dark">{{ $order->product->name ?? 'Product' }}</span>
                                                    <br><small class="text-muted">Qty: {{ $order->quantity }}</small>
                                                </td>
                                                <td>{{ $order->customer->name ?? 'N/A' }}</td>
                                                <td>
                                                    {{ $order->delivery_address }}
                                                    @if($order->delivery_lat && $order->delivery_lng)
                                                        <br><small class="text-muted">📍 {{ $order->delivery_lat }}, {{ $order->delivery_lng }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    <form action="{{ route('orders.accept', $order->id) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success btn-sm fw-semibold">
                                                            Accept Order
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Column: My Deliveries -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title fw-bold text-success m-0">My Deliveries (Active)</h5>
                    </div>
                    <div class="card-body">
                        @if($myOrders->isEmpty())
                            <div class="text-center py-5 text-muted">
                                <p class="mb-0">No active orders found.</p>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Product</th>
                                            <th>Delivery Address</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($myOrders as $order)
                                            <tr>
                                                <td class="fw-bold">#{{ $order->id }}</td>
                                                <td>
                                                    <span class="fw-semibold text-dark">{{ $order->product->name ?? 'Product' }}</span>
                                                    <br><small class="text-muted">Qty: {{ $order->quantity }}</small>
                                                </td>
                                                <td>
                                                    {{ $order->delivery_address }}
                                                    @if($order->delivery_lat && $order->delivery_lng)
                                                        <br><small class="text-muted">📍 {{ $order->delivery_lat }}, {{ $order->delivery_lng }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($order->status === 'accepted')
                                                        <span class="badge bg-warning text-dark">Accepted</span>
                                                    @elseif($order->status === 'delivering')
                                                        <span class="badge bg-primary">Delivering</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($order->status === 'accepted')
                                                        <form action="{{ route('orders.start-delivery', $order->id) }}" method="POST" class="form-start-delivery">
                                                            @csrf
                                                            <input type="hidden" name="shipper_lat" class="shipper-lat">
                                                            <input type="hidden" name="shipper_lng" class="shipper-lng">
                                                            <button type="submit" class="btn btn-primary btn-sm fw-semibold">
                                                                Start Delivery
                                                            </button>
                                                        </form>
                                                    @elseif($order->status === 'delivering')
                                                        <a href="{{ route('orders.map', $order->id) }}" class="btn btn-outline-primary btn-sm fw-semibold me-1">
                                                            View Map
                                                        </a>
                                                        <form action="{{ route('orders.complete', $order->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-success btn-sm fw-semibold">
                                                                Complete
                                                            </button>
                                                        </form>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Script to intercept form and fetch location of shipper -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const startDeliveryForms = document.querySelectorAll('.form-start-delivery');
            
            startDeliveryForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const submitBtn = form.querySelector('button[type="submit"]');
                    const originalText = submitBtn.innerHTML;
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Getting location...';
                    
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(
                            function(position) {
                                form.querySelector('.shipper-lat').value = position.coords.latitude;
                                form.querySelector('.shipper-lng').value = position.coords.longitude;
                                form.submit();
                            },
                            function(error) {
                                alert('Unable to get your current location. Please grant location permission to the browser to proceed.');
                                submitBtn.disabled = false;
                                submitBtn.innerHTML = originalText;
                            },
                            {
                                enableHighAccuracy: true,
                                timeout: 10000,
                                maximumAge: 0
                            }
                        );
                    } else {
                        alert('Browser does not support GPS Geolocation.');
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    }
                });
            });
        });
    </script>
</body>

</html>
