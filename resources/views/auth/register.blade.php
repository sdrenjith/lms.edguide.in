<x-guest-layout>
    <div class="max-w-4xl mx-auto">
        <div class="bg-white shadow-lg rounded-lg p-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">Student Registration Form</h2>
            
            <form method="POST" action="{{ route('register') }}" class="space-y-6">
                @csrf

                <!-- Student Information Section -->
                <div class="bg-gray-50 p-6 rounded-lg">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Student Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- 1. Student Full Name -->
                        <div>
                            <x-input-label for="name" :value="__('Student Full Name *')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- 2. Guardian Name -->
                        <div>
                            <x-input-label for="guardian_name" :value="__('Guardian Name *')" />
                            <x-text-input id="guardian_name" class="block mt-1 w-full" type="text" name="guardian_name" :value="old('guardian_name')" required autocomplete="name" />
                            <x-input-error :messages="$errors->get('guardian_name')" class="mt-2" />
                        </div>

                        <!-- 3. Date of Birth -->
                        <div>
                            <x-input-label for="dob" :value="__('Date of Birth *')" />
                            <x-text-input id="dob" class="block mt-1 w-full" type="date" name="dob" :value="old('dob')" required />
                            <x-input-error :messages="$errors->get('dob')" class="mt-2" />
                        </div>

                        <!-- 4. Phone Number -->
                        <div>
                            <x-input-label for="phone" :value="__('Phone Number *')" />
                            <x-text-input id="phone" class="block mt-1 w-full" type="tel" name="phone" :value="old('phone')" required autocomplete="tel" />
                            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                        </div>

                        <!-- 5. Qualification -->
                        <div>
                            <x-input-label for="qualification" :value="__('Qualification *')" />
                            <select id="qualification" name="qualification" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">Select Qualification</option>
                                <option value="High School" {{ old('qualification') == 'High School' ? 'selected' : '' }}>High School</option>
                                <option value="Diploma" {{ old('qualification') == 'Diploma' ? 'selected' : '' }}>Diploma</option>
                                <option value="Bachelor's Degree" {{ old('qualification') == 'Bachelor\'s Degree' ? 'selected' : '' }}>Bachelor's Degree</option>
                                <option value="Master's Degree" {{ old('qualification') == 'Master\'s Degree' ? 'selected' : '' }}>Master's Degree</option>
                                <option value="PhD" {{ old('qualification') == 'PhD' ? 'selected' : '' }}>PhD</option>
                                <option value="Other" {{ old('qualification') == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                            <x-input-error :messages="$errors->get('qualification')" class="mt-2" />
                        </div>

                        <!-- 6. Batch (Optional) -->
                        <div>
                            <x-input-label for="batch_id" :value="__('Batch (Optional)')" />
                            <select id="batch_id" name="batch_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">Select Batch (Optional)</option>
                                @foreach(\App\Models\Batch::all() as $batch)
                                    <option value="{{ $batch->id }}" {{ old('batch_id') == $batch->id ? 'selected' : '' }}>{{ $batch->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('batch_id')" class="mt-2" />
                        </div>

                        <!-- 7. Email ID -->
                        <div>
                            <x-input-label for="email" :value="__('Email ID *')" />
                            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- 8. Address Information -->
                        <div>
                            <x-input-label for="address" :value="__('Address Information *')" />
                            <textarea id="address" name="address" rows="3" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required placeholder="Enter your complete address...">{{ old('address') }}</textarea>
                            <x-input-error :messages="$errors->get('address')" class="mt-2" />
                        </div>

                        <!-- 9. Financial Information (Optional) -->
                        <div>
                            <x-input-label for="course_fee" :value="__('Course Fee (Optional)')" />
                            <x-text-input id="course_fee" class="block mt-1 w-full" type="number" step="0.01" min="0" max="999999999999.99" name="course_fee" :value="old('course_fee')" placeholder="Enter course fee amount" />
                            <p class="mt-1 text-sm text-gray-600">Maximum value: ₹999,999,999,999.99</p>
                            <x-input-error :messages="$errors->get('course_fee')" class="mt-2" />
                        </div>

                        <!-- 10. Gender -->
                        <div>
                            <x-input-label for="gender" :value="__('Gender *')" />
                            <select id="gender" name="gender" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">Select Gender</option>
                                <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            <x-input-error :messages="$errors->get('gender')" class="mt-2" />
                        </div>

                        <!-- 11. Nationality -->
                        <div>
                            <x-input-label for="nationality" :value="__('Nationality *')" />
                            <x-text-input id="nationality" class="block mt-1 w-full" type="text" name="nationality" :value="old('nationality', 'Indian')" required />
                            <x-input-error :messages="$errors->get('nationality')" class="mt-2" />
                        </div>
                    </div>
                </div>

                <!-- Account Security Section -->
                <div class="bg-gray-50 p-6 rounded-lg">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Account Security</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Password -->
                        <div>
                            <x-input-label for="password" :value="__('Password *')" />
                            <x-text-input id="password" class="block mt-1 w-full"
                                            type="password"
                                            name="password"
                                            required autocomplete="new-password" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <x-input-label for="password_confirmation" :value="__('Confirm Password *')" />
                            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                                            type="password"
                                            name="password_confirmation" required autocomplete="new-password" />
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between mt-6">
                    <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                        {{ __('Already registered?') }}
                    </a>

                    <x-primary-button class="px-8 py-3">
                        {{ __('Register') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
