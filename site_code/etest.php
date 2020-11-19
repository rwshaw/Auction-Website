const fName = document.getElementById('fName');
const lName = document.getElementById('lName');
const addressLine1 = document.getElementById('AddressLine1');
const addressLine2 = document.getElementById('AddressLine2');
const city = document.getElementById('city');
const postCode = document.getElementById('postCode');
const email = document.getElementById('email1');
const password = document.getElementById('password1');
const passwordConfirmation = document.getElementById('passwordConfirmation');
const form = document.getElementById('form');
const errorElement = document.getElementById('error'); 

form.addEventListener('submit', (e) => {
  
  let messages = [];

  // check if value entered. 
  if (fName.value.trim() === '' || fName.value == null) {
    messages.push('Please enter your first name.'); 
  }

  if (lName.value.trim() === '' || lName.value == null) {
    messages.push('Please enter your last name.'); 
  }

  if (addressLine1.value.trim() === '' || addressLine1.value == null) {
    messages.push('Please enter your first address line.'); 
  }

  if (city.value.trim() === '' || city.value == null) {
    messages.push('Please enter the city.'); 
  }

  if (postCode.value.trim() === '' || postCode.value == null) {
    messages.push('Please enter your post code.'); 
  }

  // email_regex = validateEmail(email.value)
  if (email.value.trim() === '' || email.value == null) {
    messages.push('Please enter a valid email.'); 
  }

  if (password.value.trim() === '' || password.value == null || password.value.length < 8 || password.value.length > 20 ||
    passwordConfirmation.value === '' || passwordConfirmation.value == null || password.value !== passwordConfirmation.value) {
    messages.push('Please enter a password of between 8 and 20 characters is required. The password should include a The Password confirmation should match the password.'); 
  }

  // sanitize email 

  // regex at least 1 special character, 1uppercase, 1 number function

  if (messages.length > 0) {
    e.preventDefault(); 
    alert(errorElement.inntertext = messages.join("\n")); 
  }
}); 

// function validateEmail(email) 
//     {
//         const re = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/; 
//         email_regex = re.test(string(email).toLowerCase());
//         return email_regex;
//     }

