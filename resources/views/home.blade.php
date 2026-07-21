<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product List</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-light">
    <div class="container py-5">
        
        <!-- Order success alert -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert">
                <strong>Success!</strong> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="mb-4 d-flex justify-content-between align-items-center bg-white p-3 rounded shadow-sm">
            <h1 class="h3 m-0 fw-bold text-dark">Product List</h1>
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
            @foreach ($product as $p)
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm border-0">
                        @if($p->image)
                            <img src="{{ $p->image }}" class="card-img-top" alt="{{ $p->name }}"
                                style="height: 250px; object-fit: cover;">
                        @endif
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title fw-bold text-dark">{{ $p->name }}</h5>
                            <p class="card-text text-muted small mb-3">{{ $p->description }}</p>
                            <div class="d-flex justify-content-between align-items-center mb-3 mt-auto">
                                <span class="text-danger fw-bold fs-5">{{ number_format($p->price) }}đ</span>
                                <span class="badge bg-success">Stock: {{ $p->stock }}</span>
                            </div>
                            <button type="button" class="btn btn-primary w-100 btn-order" 
                                data-bs-toggle="modal" 
                                data-bs-target="#orderModal"
                                data-id="{{ $p->id }}" 
                                data-name="{{ $p->name }}" 
                                data-price="{{ number_format($p->price) }}đ">
                                Buy Now
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Modal Đặt Hàng -->
    <div class="modal fade" id="orderModal" tabindex="-1" aria-labelledby="orderModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('orders.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="product_id" id="modal_product_id">
                    
                    <div class="modal-header bg-primary text-white border-0">
                        <h5 class="modal-title fw-bold" id="orderModalLabel">Confirm Order</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label text-muted small mb-1">Product</label>
                            <h4 class="fw-bold text-dark mb-0" id="modal_product_name">Product Name</h4>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small mb-1">Unit Price</label>
                            <h5 class="text-danger fw-bold mb-0" id="modal_product_price">0đ</h5>
                        </div>
                        
                        <hr class="my-3 text-muted opacity-25">

                        <div class="mb-3">
                            <label for="quantity" class="form-label fw-bold">Quantity</label>
                            <input type="number" class="form-control" id="quantity" name="quantity" value="1" min="1" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="delivery_address" class="form-label fw-bold">Delivery Address</label>
                            <input type="text" class="form-control" id="delivery_address" name="delivery_address" placeholder="Enter delivery address..." required>
                        </div>
                        
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label fw-bold mb-0">Delivery Coordinates</label>
                                <button type="button" class="btn btn-outline-primary btn-sm py-1" id="btn-get-location">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-geo-alt-fill me-1" viewBox="0 0 16 16">
                                      <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10m0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6"/>
                                    </svg>
                                    Get My Location
                                </button>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <label for="delivery_lat" class="form-label small text-muted">Latitude (Lat)</label>
                                    <input type="number" step="any" class="form-control" id="delivery_lat" name="delivery_lat" placeholder="e.g., 10.123456">
                                </div>
                                <div class="col-6">
                                    <label for="delivery_lng" class="form-label small text-muted">Longitude (Lng)</label>
                                    <input type="number" step="any" class="form-control" id="delivery_lng" name="delivery_lng" placeholder="e.g., 106.123456">
                                </div>
                            </div>
                            <div id="location-status" class="form-text text-danger mt-1 d-none"></div>
                        </div>
                    </div>
                    
                    <div class="modal-footer border-0 bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success px-4 fw-bold">Order Now</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Script điều khiển Modal -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const orderModalEl = document.getElementById('orderModal');
            const btnGetLocation = document.getElementById('btn-get-location');
            const locationStatus = document.getElementById('location-status');

            if (orderModalEl) {
                orderModalEl.addEventListener('show.bs.modal', function (event) {
                    // Button that triggered the modal
                    const button = event.relatedTarget;
                    
                    // Extract info from data-bs-* attributes
                    const id = button.getAttribute('data-id');
                    const name = button.getAttribute('data-name');
                    const price = button.getAttribute('data-price');
                    
                    // Inject data into modal fields
                    document.getElementById('modal_product_id').value = id;
                    document.getElementById('modal_product_name').textContent = name;
                    document.getElementById('modal_product_price').textContent = price;
                    
                    // Reset fields
                    document.getElementById('quantity').value = 1;
                    document.getElementById('delivery_address').value = '';
                    document.getElementById('delivery_lat').value = '';
                    document.getElementById('delivery_lng').value = '';
                    if (locationStatus) {
                        locationStatus.classList.add('d-none');
                    }
                });
            }

            if (btnGetLocation) {
                btnGetLocation.addEventListener('click', function () {
                    if (navigator.geolocation) {
                        btnGetLocation.disabled = true;
                        const originalContent = btnGetLocation.innerHTML;
                        btnGetLocation.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Fetching...';
                        locationStatus.classList.add('d-none');
                        
                        navigator.geolocation.getCurrentPosition(
                            function (position) {
                                document.getElementById('delivery_lat').value = position.coords.latitude;
                                document.getElementById('delivery_lng').value = position.coords.longitude;
                                btnGetLocation.disabled = false;
                                btnGetLocation.innerHTML = originalContent;
                            },
                            function (error) {
                                btnGetLocation.disabled = false;
                                btnGetLocation.innerHTML = originalContent;
                                locationStatus.textContent = 'Unable to get location. Please grant location permission to the browser.';
                                locationStatus.classList.remove('d-none');
                            },
                            {
                                enableHighAccuracy: true,
                                timeout: 10000,
                                maximumAge: 0
                            }
                        );
                    } else {
                        locationStatus.textContent = 'Browser does not support GPS Geolocation.';
                        locationStatus.classList.remove('d-none');
                    }
                });
            }
        });
    </script>
</body>
</html>