const fName = document.getElementById('fName');
const lName = document.getElementById('lName');
const AddressLine1 = document.getElementById('AddressLine1');
const AddressLine2 = document.getElementById('AddressLine2');
const city = document.getElementById('city');
const postCode = document.getElementById('postCode');
const email = document.getElementById('email');
const password = document.getElementById('password');
const passwordConfirmation = document.getElementById('passwordConfirmation');
const form = document.getElementById('form');
const errorElement = document.getElementById('error'); 

form.addEventListener('submit', (e) => {
	
	let messages = [];

	if (fName.value === '' || fName.value == null) {
		messages.push('First name is required.'); 
	}

	if (password.value.length <= )

	if (messages.length > 0) {
		e.preventDefault(); 
		alert(errorElement.inntertext = messages.join(", ")); 
	}
}); 