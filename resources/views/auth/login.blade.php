<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-5">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-5">
                        <h2 class="text-center mb-4">Login</h2>

                        @if($errors->any())
                            <div class="alert alert-danger p-2 small">
                                <ul class="mb-0 ps-3">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="/login">
                            <!-- CSRF Token (Laravel) -->
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">

                            <!-- Email Input -->
                            <div class="mb-3">
                                <label for="email" class="form-label">Email address</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    placeholder="name@example.com" value="{{ old('email') }}" required autocomplete="email" autofocus>
                            </div>

                            <!-- Password Input -->
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <label for="password" class="form-label">Password</label>
                                    <a href="#" class="text-decoration-none small">Forgot password?</a>
                                </div>
                                <input type="password" class="form-control" id="password" name="password"
                                    placeholder="••••••••" required autocomplete="current-password">
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-primary w-100">Sign In</button>
                        </form>

                        <hr class="my-4">

                        <div class="text-center">
                            <p class="mb-0 text-muted">Don't have an account? <a href="{{ route('register') }}"
                                    class="text-decoration-none">Sign up</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
