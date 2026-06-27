<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
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
                class="flex-1 py-2.5 px-4 rounded-lg font-medium text-sm text-gray-500 text-center hover:text-gray-700">
                <i class="fas fa-sign-in-alt mr-2"></i>Login
            </a>
            <a href="{{ route('register') }}"
                class="flex-1 py-2.5 px-4 rounded-lg font-medium text-sm bg-white text-gray-800 shadow-sm text-center">
                <i class="fas fa-user-plus mr-2"></i>Register
            </a>
        </div>

        <!-- Register Form -->
        <div class="auth-card bg-white rounded-2xl p-8">
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
                    <i class="fas fa-user-plus text-2xl text-green-600"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Create Account</h2>
                <p class="text-gray-500 text-sm">Join us today and get started</p>
            </div>

            <form action="{{ route('register.post') }}" method="post">
                @csrf
                <!-- Name Field -->
                <div class="mb-5">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Full Name
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-user text-gray-400 text-sm"></i>
                        </div>
                        <input type="text" id="name" name="name" value="{{ old('name') }}"
                            class="w-full pl-10 pr-4 py-3 border @error('name') border-red-500 @else border-gray-300 @enderror rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                            placeholder="John Doe" required>
                    </div>
                    @error('name')
                        <p class="text-red-500 text-xs mt-1.5 flex items-center">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

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
                            class="w-full pl-10 pr-4 py-3 border @error('email') border-red-500 @else border-gray-300 @enderror rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
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
                <div class="mb-5">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Password
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-key text-gray-400 text-sm"></i>
                        </div>
                        <input type="password" id="password" name="password" autocomplete="new-password"
                            class="w-full pl-10 pr-4 py-3 border @error('password') border-red-500 @else border-gray-300 @enderror rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                            placeholder="Min. 8 characters" required>
                    </div>
                    @error('password')
                        <p class="text-red-500 text-xs mt-1.5 flex items-center">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Confirm Password Field -->
                <div class="mb-6">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                        Confirm Password
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400 text-sm"></i>
                        </div>

                        <input type="password" id="password_confirmation" name="password_confirmation"
                            autocomplete="new-password"
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                            placeholder="Re-enter your password" required>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                    class="btn w-full py-3 px-4 rounded-lg text-white font-semibold text-sm bg-green-600 hover:bg-green-700 shadow-sm transition-all">
                    <i class="fas fa-user-plus mr-2"></i>
                    Create Account
                </button>

                <div class="text-center mt-6 text-sm text-gray-500">
                    Already have an account?
                    <a href="{{ route('login') }}" class="text-green-600 hover:text-green-700 font-medium">
                        Sign in instead
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>
