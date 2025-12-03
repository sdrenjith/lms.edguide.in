<div>
    <label for="gender" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Gender *</label>
    <select id="gender" name="gender" wire:model.live="data.gender" {{ $attributes->merge(['class' => 'fi-input block w-full border-none rounded-lg py-1.5 text-base text-gray-950 transition duration-75 placeholder:text-gray-400 focus:ring-0 disabled:text-gray-500 disabled:[-webkit-text-fill-color:theme(colors.gray.500)] disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.400)] dark:text-white dark:placeholder:text-gray-500 dark:disabled:text-gray-400 dark:disabled:[-webkit-text-fill-color:theme(colors.gray.400)] dark:disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.500)] sm:text-sm sm:leading-6 ps-3 pe-3']) }} style="border: 0.1px solid silver !important;" required>
        <option value="">Select an option</option>
        <option value="male">Male</option>
        <option value="female">Female</option>
        <option value="other">Other</option>
    </select>
</div> 