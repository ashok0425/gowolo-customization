/**
 * Bank Details Form Validation using country-validation.json
 * Provides dynamic state loading and postal code validation
 */

class BankDetailsValidator {
    constructor() {
        this.countryData = null;
        this.currentCountry = null;
        this.loadCountryData();
        this.initializeEventListeners();
    }

    /**
     * Load country validation data from JSON file
     */
    async loadCountryData() {
        try {
            const response = await fetch('/country-validation.json');
            this.countryData = await response.json();
            console.log('Country validation data loaded successfully');
        } catch (error) {
            console.error('Failed to load country validation data:', error);
        }
    }

    /**
     * Initialize event listeners for form elements
     */
    initializeEventListeners() {
        // Wait for DOM to be ready
        document.addEventListener('DOMContentLoaded', () => {
            const countrySelect = document.getElementById('bank_country');
            const stateSelect = document.getElementById('bank_state');
            const postalCodeInput = document.getElementById('bank_postcode');

            if (countrySelect) {
                countrySelect.addEventListener('change', (e) => {
                    this.handleCountryChange(e.target.value);
                });

                // Check if there's a pre-selected country
                if (countrySelect.value) {
                    this.handleCountryChange(countrySelect.value);
                }
            }

            if (postalCodeInput) {
                postalCodeInput.addEventListener('blur', (e) => {
                    this.validatePostalCode(e.target.value);
                });

                postalCodeInput.addEventListener('input', (e) => {
                    this.clearPostalCodeError();
                });
            }
        });
    }

    /**
     * Handle country selection change
     */
    async handleCountryChange(countryCode) {
        if (!countryCode || !this.countryData) {
            this.clearStateOptions();
            return;
        }

        this.currentCountry = countryCode;
        const country = this.countryData[countryCode];

        if (country && country.states) {
            this.populateStates(country.states);
            this.updatePostalCodeHint(country);
        } else {
            this.clearStateOptions();
            this.updatePostalCodeHint(country);
        }

        // Update currency automatically
        this.updateCurrency(countryCode);
    }

    /**
     * Populate state/province dropdown
     */
    populateStates(states) {
        const stateSelect = document.getElementById('bank_state');
        if (!stateSelect) return;

        // Get the currently saved state value (if any)
        const savedStateValue = stateSelect.dataset.savedValue || stateSelect.value;

        // Clear existing options except the first one
        stateSelect.innerHTML = '<option value="">Select State/Province</option>';

        // Add states
        states.forEach(state => {
            const option = document.createElement('option');
            option.value = state.code;
            option.textContent = state.name;
            
            // Select the saved state if it matches
            if (savedStateValue && (state.code === savedStateValue || state.name === savedStateValue)) {
                option.selected = true;
            }
            
            stateSelect.appendChild(option);
        });

        // Show the state field
        const stateContainer = stateSelect.closest('.col-md-6');
        if (stateContainer) {
            stateContainer.style.display = 'block';
        }
    }

    /**
     * Clear state options
     */
    clearStateOptions() {
        const stateSelect = document.getElementById('bank_state');
        if (!stateSelect) return;

        stateSelect.innerHTML = '<option value="">Select State/Province</option>';
        
        // Hide the state field if no states available
        const stateContainer = stateSelect.closest('.col-md-6');
        if (stateContainer) {
            stateContainer.style.display = 'none';
        }
    }

    /**
     * Update postal code hint based on country
     */
    updatePostalCodeHint(country) {
        const postalCodeInput = document.getElementById('bank_postcode');
        const hintElement = document.getElementById('postal-code-hint');
        
        if (!postalCodeInput) return;

        if (country && country.postalCodeFormat) {
            postalCodeInput.placeholder = country.postalCodeFormat;
            
            if (hintElement) {
                hintElement.textContent = `Format: ${country.postalCodeFormat}`;
                hintElement.style.display = 'block';
            }
        } else {
            postalCodeInput.placeholder = 'Enter postal code';
            
            if (hintElement) {
                hintElement.style.display = 'none';
            }
        }
    }

    /**
     * Update currency field automatically
     */
    updateCurrency(countryCode) {
        const currencyInput = document.getElementById('bank_currency');
        if (!currencyInput || !this.countryData) return;

        const country = this.countryData[countryCode];
        if (country && country.currency) {
            currencyInput.value = country.currency;
            
            // Trigger change event to update any dependent fields
            currencyInput.dispatchEvent(new Event('change'));
            
            // Dispatch custom event for currency-specific field display
            document.dispatchEvent(new CustomEvent('currencyChanged', {
                detail: { currency: country.currency }
            }));
        }
    }

    /**
     * Validate postal code against country pattern
     */
    validatePostalCode(postalCode) {
        if (!this.currentCountry || !this.countryData || !postalCode) {
            return true;
        }

        const country = this.countryData[this.currentCountry];
        if (!country || !country.postalCodePattern) {
            return true;
        }

        const pattern = new RegExp(country.postalCodePattern);
        const isValid = pattern.test(postalCode);

        this.showPostalCodeValidation(isValid, country.postalCodeFormat);
        return isValid;
    }

    /**
     * Show postal code validation feedback
     */
    showPostalCodeValidation(isValid, format) {
        const postalCodeInput = document.getElementById('bank_postcode');
        const feedbackElement = document.getElementById('postal-code-feedback');
        
        if (!postalCodeInput) return;

        // Remove existing validation classes
        postalCodeInput.classList.remove('is-valid', 'is-invalid');

        if (isValid) {
            postalCodeInput.classList.add('is-valid');
            if (feedbackElement) {
                feedbackElement.textContent = '';
                feedbackElement.style.display = 'none';
            }
        } else {
            postalCodeInput.classList.add('is-invalid');
            if (feedbackElement) {
                feedbackElement.textContent = `Invalid format. Expected: ${format}`;
                feedbackElement.style.display = 'block';
                feedbackElement.className = 'invalid-feedback';
            }
        }
    }

    /**
     * Clear postal code error
     */
    clearPostalCodeError() {
        const postalCodeInput = document.getElementById('bank_postcode');
        const feedbackElement = document.getElementById('postal-code-feedback');
        
        if (postalCodeInput) {
            postalCodeInput.classList.remove('is-invalid');
        }
        
        if (feedbackElement) {
            feedbackElement.style.display = 'none';
        }
    }

    /**
     * Get states for a specific country
     */
    getStatesForCountry(countryCode) {
        if (!this.countryData || !countryCode) return [];
        
        const country = this.countryData[countryCode];
        return country && country.states ? country.states : [];
    }

    /**
     * Validate entire form
     */
    validateForm() {
        let isValid = true;

        // Validate postal code
        const postalCodeInput = document.getElementById('bank_postcode');
        if (postalCodeInput && postalCodeInput.value) {
            isValid = this.validatePostalCode(postalCodeInput.value) && isValid;
        }

        // Add more validation as needed
        return isValid;
    }
}

// Initialize the validator when the script loads
const bankDetailsValidator = new BankDetailsValidator();

// Make it globally available for other scripts
window.bankDetailsValidator = bankDetailsValidator;
