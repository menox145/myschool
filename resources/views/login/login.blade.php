<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        * {
            font-family: 'Inter', sans-serif;
        }

        .auth-card {
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center p-4 bg-gray-100">
    <div class="w-full max-w-md">
        <!-- Tab Navigation -->
        <div class="flex gap-2 mb-6 bg-white p-1.5 rounded-xl shadow-sm">
            <a href="{{ route('login') }}"
                class="flex-1 py-2.5 px-4 rounded-lg font-medium text-sm bg-white text-gray-800 shadow-sm text-center">
                <i class="fas fa-sign-in-alt mr-2"></i>Login
            </a>
            <a href="{{ route('register') }}"
                class="flex-1 py-2.5 px-4 rounded-lg font-medium text-sm text-gray-500 text-center hover:text-gray-700">
                <i class="fas fa-user-plus mr-2"></i>Register
            </a>
        </div>

        <!-- Login Form -->
        <div class="auth-card bg-white rounded-2xl p-8">
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 rounded-full mb-4">
                    <i class="fas fa-lock text-2xl text-blue-600"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Welcome Back</h2>
                <p class="text-gray-500 text-sm">Please sign in to your account</p>
            </div>

            @if (session('error'))
                <div
                    class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-5 text-sm flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="post">
                @csrf
                <!-- Email Field -->
                <div class="mb-5">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email Address
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400 text-sm"></i>
                        </div>
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                            class="w-full pl-10 pr-4 py-3 border @error('email') border-red-500 @else border-gray-300 @enderror rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="john@example.com" required>
                    </div>
                    @error('email')
                        <p class="text-red-500 text-xs mt-1.5 flex items-center">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Password Field -->
                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Password
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-key text-gray-400 text-sm"></i>
                        </div>
                        <input type="password" id="password" name="password" autocomplete="current-password"
                            class="w-full pl-10 pr-4 py-3 border @error('password') border-red-500 @else border-gray-300 @enderror rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Enter your password" required>
                    </div>
                    @error('password')
                        <p class="text-red-500 text-xs mt-1.5 flex items-center">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
                <!-- Remember Me -->
                <div class="flex items-center mb-6">
                    <input type="checkbox" id="remember" name="remember"
                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                    <label for="remember" class="ml-2 text-sm text-gray-600">Remember me</label>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                    class="btn w-full py-3 px-4 rounded-lg text-white font-semibold text-sm bg-blue-600 hover:bg-blue-700 shadow-sm transition-all">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Sign In
                </button>

                <div class="text-center mt-6 text-sm text-gray-500">
                    Don't have an account?
                    <a href="{{ route('register') }}" class="text-blue-600 hover:text-blue-700 font-medium">
                        Register
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>
