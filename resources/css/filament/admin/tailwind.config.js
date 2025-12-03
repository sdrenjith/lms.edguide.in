// Custom CSS to fix Filament checkbox styling issues
const plugin = require('tailwindcss/plugin')

module.exports = {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
        './app/Filament/**/*.php',
        './vendor/filament/**/*.blade.php',
    ],
    theme: {
        extend: {},
    },
    plugins: [
        plugin(function({ addUtilities }) {
            addUtilities({
                // Fix checkbox styling
                '.filament-checkbox-fix': {
                    '& input[type="checkbox"]:checked': {
                        'background-color': '#3b82f6',
                        'border-color': '#3b82f6',
                    },
                    '& input[type="checkbox"]:checked:hover': {
                        'background-color': '#2563eb',
                        'border-color': '#2563eb',
                    },
                    '& input[type="checkbox"]:focus': {
                        'outline': '2px solid #3b82f6',
                        'outline-offset': '2px',
                    },
                },
                // Alternative checkbox styling with better visual feedback
                '.custom-checkbox': {
                    'position': 'relative',
                    'display': 'inline-block',
                    'width': '16px',
                    'height': '16px',
                    'border': '2px solid #d1d5db',
                    'border-radius': '4px',
                    'background-color': 'white',
                    'cursor': 'pointer',
                    'transition': 'all 0.2s ease-in-out',
                },
                '.custom-checkbox.checked': {
                    'background-color': '#3b82f6',
                    'border-color': '#3b82f6',
                },
                '.custom-checkbox.checked::after': {
                    'content': '""',
                    'position': 'absolute',
                    'left': '4px',
                    'top': '1px',
                    'width': '4px',
                    'height': '8px',
                    'border': 'solid white',
                    'border-width': '0 2px 2px 0',
                    'transform': 'rotate(45deg)',
                },
            })
        })
    ],
} 