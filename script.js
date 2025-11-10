/**
 * Accessible Recipe Website - JavaScript
 * Implements WCAG 2.1 Perceivable principle features
 */

document.addEventListener('DOMContentLoaded', function() {
    // Toggle high contrast mode
    const highContrastBtn = document.getElementById('high-contrast');
    if (highContrastBtn) {
        highContrastBtn.addEventListener('click', toggleHighContrast);
        highContrastBtn.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                toggleHighContrast();
            }
        });
    }

    // Toggle large text mode
    const fontSizeBtn = document.getElementById('font-size');
    if (fontSizeBtn) {
        fontSizeBtn.addEventListener('click', toggleFontSize);
        fontSizeBtn.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                toggleFontSize();
            }
        });
    }

    // Add focus styles for keyboard navigation
    document.addEventListener('keyup', function(e) {
        if (e.key === 'Tab') {
            document.documentElement.classList.add('keyboard-navigation');
            document.removeEventListener('keyup', arguments.callee);
        }
    });

    // Make all images have alt text if missing
    document.querySelectorAll('img:not([alt])').forEach(img => {
        img.setAttribute('alt', 'Decorative image');
    });

    // Add print button functionality
    const printButton = document.createElement('button');
    printButton.textContent = 'Print Recipe';
    printButton.className = 'print-button';
    printButton.addEventListener('click', function() {
        window.print();
    });
    
    const recipeHeader = document.querySelector('.recipe-header .container');
    if (recipeHeader) {
        recipeHeader.appendChild(printButton);
    }

    // Add focus management for better keyboard navigation
    const focusableElements = 'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])';
    const focusableContent = document.querySelectorAll(focusableElements);
    
    focusableContent.forEach(element => {
        element.setAttribute('tabindex', '0');
    });

    // Add aria-live region for dynamic content updates
    const liveRegion = document.createElement('div');
    liveRegion.setAttribute('aria-live', 'polite');
    liveRegion.setAttribute('aria-atomic', 'true');
    liveRegion.className = 'sr-only';
    document.body.appendChild(liveRegion);

    // Function to announce changes to screen readers
    window.announceToScreenReader = function(message) {
        liveRegion.textContent = message;
        // Force a reflow to ensure the screen reader announces the change
        setTimeout(() => {
            liveRegion.textContent = '';
        }, 100);
    };

    // Initialize any interactive components
    initAccessibilityFeatures();
});

/**
 * Toggle high contrast mode
 */
function toggleHighContrast() {
    const highContrastBtn = document.getElementById('high-contrast');
    const isPressed = highContrastBtn.getAttribute('aria-pressed') === 'true';
    
    if (isPressed) {
        document.documentElement.classList.remove('high-contrast');
        highContrastBtn.setAttribute('aria-pressed', 'false');
        announceToScreenReader('High contrast mode turned off');
    } else {
        document.documentElement.classList.add('high-contrast');
        highContrastBtn.setAttribute('aria-pressed', 'true');
        announceToScreenReader('High contrast mode turned on');
    }
    
    // Save preference to localStorage
    localStorage.setItem('highContrast', !isPressed);
}

/**
 * Toggle large text mode
 */
function toggleFontSize() {
    const fontSizeBtn = document.getElementById('font-size');
    const isPressed = fontSizeBtn.getAttribute('aria-pressed') === 'true';
    
    if (isPressed) {
        document.documentElement.classList.remove('large-text');
        fontSizeBtn.setAttribute('aria-pressed', 'false');
        announceToScreenReader('Large text mode turned off');
    } else {
        document.documentElement.classList.add('large-text');
        fontSizeBtn.setAttribute('aria-pressed', 'true');
        announceToScreenReader('Large text mode turned on');
    }
    
    // Save preference to localStorage
    localStorage.setItem('largeText', !isPressed);
}

/**
 * Initialize accessibility features based on user preferences
 */
function initAccessibilityFeatures() {
    // Check for saved preferences
    if (localStorage.getItem('highContrast') === 'true') {
        document.documentElement.classList.add('high-contrast');
        document.getElementById('high-contrast').setAttribute('aria-pressed', 'true');
    }
    
    if (localStorage.getItem('largeText') === 'true') {
        document.documentElement.classList.add('large-text');
        document.getElementById('font-size').setAttribute('aria-pressed', 'true');
    }
    
    // Add focus styles for keyboard navigation
    document.documentElement.classList.add('js-focus-visible');
    
    // Add skip link focus
    const skipLink = document.querySelector('.skip-link');
    if (skipLink) {
        skipLink.addEventListener('focus', function() {
            this.classList.add('focus-visible');
        });
        
        skipLink.addEventListener('blur', function() {
            this.classList.remove('focus-visible');
        });
    }
    
    // Add focus trap for modals (if any are added in the future)
    setupFocusTrap();
}

/**
 * Setup focus trap for modals (for future use)
 */
function setupFocusTrap() {
    // This would be implemented when modals are added to the page
    // It ensures keyboard focus stays within the modal when it's open
}

/**
 * Debounce function to limit the rate at which a function can fire
 */
function debounce(func, wait) {
    let timeout;
    return function() {
        const context = this;
        const args = arguments;
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            func.apply(context, args);
        }, wait);
    };
}

// Handle window resize with debounce
window.addEventListener('resize', debounce(function() {
    // Handle responsive behaviors here if needed
}, 250));

// Add support for reduced motion preference
const mediaQuery = window.matchMedia('(prefers-reduced-motion: reduce)');
if (mediaQuery.matches) {
    document.documentElement.classList.add('reduced-motion');
}

mediaQuery.addEventListener('change', () => {
    if (mediaQuery.matches) {
        document.documentElement.classList.add('reduced-motion');
    } else {
        document.documentElement.classList.remove('reduced-motion');
    }
});

// Add support for dark mode preference
const darkModeQuery = window.matchMedia('(prefers-color-scheme: dark)');

function updateDarkMode(e) {
    if (e.matches) {
        document.documentElement.setAttribute('data-theme', 'dark');
    } else {
        document.documentElement.setAttribute('data-theme', 'light');
    }
}

darkModeQuery.addEventListener('change', updateDarkMode);
updateDarkMode(darkModeQuery);

// Add support for print
window.addEventListener('beforeprint', () => {
    // Any cleanup or additional actions before printing
});

window.addEventListener('afterprint', () => {
    // Any actions after printing is done
});
