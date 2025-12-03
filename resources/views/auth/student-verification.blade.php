<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Verification - EdGuide</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
    </style>
</head>
<body class="gradient-bg min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <!-- Header -->
        <div class="text-center">
            <div class="mx-auto h-16 w-16 bg-white rounded-full flex items-center justify-center shadow-lg mb-4">
                <i class="fas fa-key text-2xl text-yellow-600"></i>
            </div>
            <h2 class="text-3xl font-bold text-white mb-2">Account Verification Required</h2>
            <p class="text-indigo-100">Please enter your verification code to activate your account and access your assigned courses.</p>
        </div>

        <!-- Verification Form -->
        <div class="bg-white rounded-2xl shadow-2xl p-8">
            <div class="space-y-6">
                @if(session('error'))
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-circle text-red-500"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-red-700">{{ session('error') }}</p>
                            </div>
                        </div>
                    </div>
                @endif
                
                <form method="POST" action="{{ route('student.verify') }}" class="space-y-4">
                    @csrf
                    
                    <div>
                        <label for="verification_code" class="block text-sm font-medium text-gray-700 mb-2">Verification Code</label>
                        <input type="text" id="verification_code" name="verification_code" required
                               placeholder="Enter your verification code"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('verification_code') border-red-500 @enderror">
                        @error('verification_code')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-lg transition duration-300">
                        <i class="fas fa-check mr-2"></i>
                        Verify Account
                    </button>
                </form>
                
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full bg-gray-500 hover:bg-gray-600 text-white font-bold py-3 px-4 rounded-lg transition duration-300">
                        <i class="fas fa-sign-out-alt mr-2"></i>
                        Logout
                    </button>
                </form>
            </div>
            
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-500">
                    Don't have a verification code? 
                    <a href="#" class="text-indigo-600 hover:text-indigo-500 font-medium">Contact Administrator</a>
                </p>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center text-indigo-100">
            <p>&copy; 2025 EdGuide. All rights reserved.</p>
        </div>
    </div>
</body>
</html>

