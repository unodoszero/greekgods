const email = document.getElementById('email');
const password = document.getElementById('password');
const confirmPassword = document.getElementById('confirm-password');
const registerAccountForm = document.getElementById('registerAccount');
const registerInfoForm = document.getElementById('registerInfo');

// Function to display error messages
function displayErrorMessage(inputId, message) {
    const errorMessageContainer = document.querySelector('.error-message-container');

    // Create a new error message element
    let errorMessage = document.createElement('div');
    errorMessage.classList.add('error-message');
    errorMessage.textContent = message;

    // Append error message to the container
    errorMessageContainer.appendChild(errorMessage);
}

// Password validation
function validatePassword(password) {
    const minLength = 8;
    const hasUppercase = /[A-Z]/;
    const hasLowercase = /[a-z]/;
    const hasDigit = /[0-9]/;
    const hasSpecialChar = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]+/;
    const commonPasswords = ["123456", "password", "12345678", "qwerty", "abc123", "111111", "123123"];
    
    if (password.length < minLength) return "*Password must be at least 8 characters long.";
    if (!hasUppercase.test(password)) return "*Password must contain at least one uppercase letter.";
    if (!hasLowercase.test(password)) return "*Password must contain at least one lowercase letter.";
    if (!hasDigit.test(password)) return "*Password must contain at least one digit.";
    if (!hasSpecialChar.test(password)) return "*Password must contain at least one special character.";
    if (commonPasswords.includes(password.toLowerCase())) return "*Password is too common. Please choose a stronger password.";
    
    return "Password is valid.";
}

// Password match validation
function validatePasswordMatch(password, confirmPassword) {
    if (password !== confirmPassword) return "*Passwords do not match.";
    return "Passwords match.";
}

// Form validation
function validateForm() {
    let isValid = true;

    // Clear previous error messages
    document.querySelectorAll('.error-message').forEach(msg => msg.remove());

    const passwordValidationMessage = validatePassword(password.value);
    if (passwordValidationMessage !== "Password is valid.") {
        displayErrorMessage('password', passwordValidationMessage);
        isValid = false;
    }

    const passwordMatchMessage = validatePasswordMatch(password.value, confirmPassword.value);
    if (passwordMatchMessage !== "Passwords match.") {
        displayErrorMessage('confirm-password', passwordMatchMessage);
        isValid = false;
    }

    // Checkbox validation
    const termsCheckbox = document.getElementById('check');
    if (!termsCheckbox.checked) {
        displayErrorMessage('termsCheckbox', "*You must agree to the terms and conditions.");
        isValid = false;
    }

    return isValid;
}

// Register button click handler to navigate to registerInfo form
document.querySelector('#registerButton').addEventListener('click', function(event) {
    event.preventDefault(); // Prevent the default button behavior

    if (!validateForm()) {
        return; // If validation fails, stop here
    }

    // Hide registerAccount form and show registerInfo form
    registerAccountForm.style.display = 'none';
    registerInfoForm.style.display = 'block';
});

// RegisterInfo form submit handler
document.querySelector('#startJourneyNow').addEventListener('click', function(event) {
    event.preventDefault();

    const form = document.getElementById('registrationForm');

    if (form.reportValidity && !form.reportValidity()) {
        return;
    }

    form.submit();
});
