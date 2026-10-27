// Client-side form validation for patient portal
document.addEventListener('DOMContentLoaded', function () {

    // Helper to display an inline error below a field
    function setError(input, message) {
        var errorSpan = document.getElementById('error-' + input.name);
        if (errorSpan) {
            errorSpan.textContent = message;
        }
        input.classList.add('input-error');
    }

    // Helper to clear all errors in a form
    function clearFormErrors(form) {
        var errorSpans = form.querySelectorAll('.field-error');
        errorSpans.forEach(function (span) {
            span.textContent = '';
        });
        var errorInputs = form.querySelectorAll('.input-error');
        errorInputs.forEach(function (input) {
            input.classList.remove('input-error');
        });
    }

    // 1. Phone check form validation (index.php)
    var checkForm = document.getElementById('checkForm');
    if (checkForm) {
        checkForm.addEventListener('submit', function (e) {
            clearFormErrors(checkForm);
            var phoneInput = document.getElementById('phone');
            var phone = phoneInput ? phoneInput.value.trim() : '';

            if (phone === '') {
                e.preventDefault();
                setError(phoneInput, 'Phone number is required.');
                phoneInput.focus();
            } else if (!/^\d{10}$/.test(phone)) {
                e.preventDefault();
                setError(phoneInput, 'Phone number must be exactly 10 digits.');
                phoneInput.focus();
            }
        });
    }

    // 2. Patient registration and edit forms validation (check.php, register.php, edit.php)
    var patientForms = document.querySelectorAll('#patientForm, #editForm');
    patientForms.forEach(function (form) {
        form.addEventListener('submit', function (e) {
            clearFormErrors(form);
            var hasError = false;
            var firstErrorInput = null;

            // Full Name: required, letters and spaces only
            var nameInput = form.querySelector('[name="name"]');
            if (nameInput) {
                var nameVal = nameInput.value.trim();
                if (nameVal === '') {
                    setError(nameInput, 'Full name is required.');
                    hasError = true;
                    if (!firstErrorInput) firstErrorInput = nameInput;
                } else if (!/^[a-zA-Z\s]+$/.test(nameVal)) {
                    setError(nameInput, 'Full name must contain letters and spaces only.');
                    hasError = true;
                    if (!firstErrorInput) firstErrorInput = nameInput;
                }
            }

            // Phone: required, exactly 10 digits (validate only if editable)
            var phoneInput = form.querySelector('[name="phone"]');
            if (phoneInput && !phoneInput.readOnly) {
                var phoneVal = phoneInput.value.trim();
                if (phoneVal === '') {
                    setError(phoneInput, 'Phone number is required.');
                    hasError = true;
                    if (!firstErrorInput) firstErrorInput = phoneInput;
                } else if (!/^\d{10}$/.test(phoneVal)) {
                    setError(phoneInput, 'Phone number must be exactly 10 digits.');
                    hasError = true;
                    if (!firstErrorInput) firstErrorInput = phoneInput;
                }
            }

            // Age: required, number 0-120
            var ageInput = form.querySelector('[name="age"]');
            if (ageInput) {
                var ageVal = ageInput.value.trim();
                var ageNum = Number(ageVal);
                if (ageVal === '') {
                    setError(ageInput, 'Age is required.');
                    hasError = true;
                    if (!firstErrorInput) firstErrorInput = ageInput;
                } else if (!/^\d+$/.test(ageVal) || ageNum < 0 || ageNum > 120) {
                    setError(ageInput, 'Age must be a number between 0 and 120.');
                    hasError = true;
                    if (!firstErrorInput) firstErrorInput = ageInput;
                }
            }

            // Gender: required selection
            var genderSelect = form.querySelector('[name="gender"]');
            if (genderSelect) {
                var genderVal = genderSelect.value.trim();
                if (genderVal === '') {
                    setError(genderSelect, 'Please select a gender.');
                    hasError = true;
                    if (!firstErrorInput) firstErrorInput = genderSelect;
                }
            }

            // Address: required
            var addressInput = form.querySelector('[name="address"]');
            if (addressInput) {
                var addressVal = addressInput.value.trim();
                if (addressVal === '') {
                    setError(addressInput, 'Address is required.');
                    hasError = true;
                    if (!firstErrorInput) firstErrorInput = addressInput;
                }
            }

            // Reason for visit: required
            var reasonInput = form.querySelector('[name="reason"]');
            if (reasonInput) {
                var reasonVal = reasonInput.value.trim();
                if (reasonVal === '') {
                    setError(reasonInput, 'Reason for visit is required.');
                    hasError = true;
                    if (!firstErrorInput) firstErrorInput = reasonInput;
                }
            }

            // Stop form submission if any validation failed
            if (hasError) {
                e.preventDefault();
                if (firstErrorInput) {
                    firstErrorInput.focus();
                }
            }
        });
    });
});
