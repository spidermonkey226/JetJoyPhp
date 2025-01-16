// JavaScript source code
let fname = document.getElementById("fname");
let lname = document.getElementById("lname");
let phone = document.getElementById("phone");
let email = document.getElementById("email");
let password = document.querySelector('input[name="psw"]');
const signupButton = document.getElementById('signup-button');


fname.addEventListener('blur', () => {
    const isValid = isValidName(fname.value);
    document.getElementById('fname-error').textContent = isValid ? '' : 'Invalid first name';
});

lname.addEventListener('blur', () => {
    const isValid = isValidName(lname.value);
    document.getElementById('lname-error').textContent = isValid ? '' : 'Invalid last name';
});
email.addEventListener('blur', () => {
    const isValid = isValidEmail(email.value);
    document.getElementById('email-error').textContent = isValid ? '' : 'Invalid email address,must be example@email.com';
});

password.addEventListener('blur', () => {
    const isValid = isValidPassword(password.value);
    document.getElementById('password-error').textContent = isValid ? '' : 'Invalid password, must have: *one upper case,*one number,*8 digit';
});

phone.addEventListener('blur', () => {
    const isValid = isValidPhoneNumber(phone.value);
    document.getElementById('phone-error').textContent = isValid ? '' : 'Invalid phone number';
});

fname.addEventListener('blur', validateInputs);
lname.addEventListener('blur', validateInputs);
email.addEventListener('blur', validateInputs);
password.addEventListener('blur', validateInputs);
phone.addEventListener('blur', validateInputs);
function isValidName(fname) {
    const regex = /^[A-Z][a-z]{1,}$/;
    return regex.test(fname)
}

function isValidEmail(email) {
    const regex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    return regex.test(email);
}

function isValidPassword(password) {
    const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;
    return regex.test(password);
}

function isValidPhoneNumber(phone) {
    const regex = /^[+]?[0-9]{0,3}\W?[()0-9]{3}[-\s.]?[0-9]{3}[-\s.]?[0-9]{4,6}$/im;
    return regex.test(phone);
}

function validateInputs() {
    const isValidFirstNAme = isValidName(fname.value);
    const isValidLastNAme = isValidName(lname.value);
    const isValidPhoneValue = isValidPhoneNumber(phone.value);
    const isValidEmailValue = isValidEmail(email.value);
    const isValidPasswordValue = isValidPassword(password.value);
    signupButton.disabled = !(isValidFirstNAme && isValidLastNAme && isValidPhoneValue && isValidEmailValue && isValidPasswordValue);
}