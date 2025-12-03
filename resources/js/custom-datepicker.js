import flatpickr from 'flatpickr';
import 'flatpickr/dist/plugins/monthSelect/style.css';
import monthSelectPlugin from 'flatpickr/dist/plugins/monthSelect';

document.addEventListener('livewire:navigated', () => {
    const dobInput = document.getElementById('dob-datepicker');

    // Check if the input exists and has not been initialized
    if (dobInput && !dobInput.hasAttribute('flatpickr-initialized')) {
        flatpickr(dobInput, {
            allowInput: true,
            dateFormat: 'Y-m-d',
            altInput: true,
            altFormat: 'd/m/Y',
            plugins: [
                new monthSelectPlugin({
                    shorthand: true,
                    dateFormat: 'm/Y',
                    altFormat: 'F Y',
                }),
            ],
        });
        // Mark the element as initialized
        dobInput.setAttribute('flatpickr-initialized', 'true');
    }
}); 