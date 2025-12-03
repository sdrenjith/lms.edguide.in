import './bootstrap';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import flatpickr from 'flatpickr';
import Alpine from 'alpinejs';
window.Alpine = Alpine;
document.addEventListener('livewire:navigated', () => {
    const dobInput = document.getElementById('dob-datepicker');
    if (dobInput && !dobInput.hasAttribute('flatpickr-initialized')) {
        flatpickr(dobInput, {
            allowInput: true,
            dateFormat: 'Y-m-d',
            altInput: true,
            altFormat: 'd/m/Y',
        });
        dobInput.setAttribute('flatpickr-initialized', 'true');
    }
});
Alpine.start();