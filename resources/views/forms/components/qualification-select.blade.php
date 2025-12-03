<div>
    <label for="qualification" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Qualification *</label>
    <select id="qualification" name="qualification" wire:model.live="data.qualification" {{ $attributes->merge(['class' => 'fi-input block w-full border-none rounded-lg py-1.5 text-base text-gray-950 transition duration-75 placeholder:text-gray-400 focus:ring-0 disabled:text-gray-500 disabled:[-webkit-text-fill-color:theme(colors.gray.500)] disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.400)] dark:text-white dark:placeholder:text-gray-500 dark:disabled:text-gray-400 dark:disabled:[-webkit-text-fill-color:theme(colors.gray.400)] dark:disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.500)] sm:text-sm sm:leading-6 ps-3 pe-3']) }} style="border: 0.1px solid silver !important;" required>
        <option value="">Select qualification</option>
        <option value="nurse">Nurse</option>
        <option value="plus_two">+2</option>
        <option value="diploma">Diploma</option>
        <option value="bachelor">Bachelor's Degree</option>
        <option value="master">Master's Degree</option>
        <option value="others">Others</option>
    </select>
</div> 