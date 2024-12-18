function openDialog(dialogId) {
    document.getElementById(dialogId).style.display = 'block';
}

function closeDialog(dialogId) {
    document.getElementById(dialogId).style.display = 'none';
}

function submitForm() {
    const name = document.getElementById('name').value;
    const email = document.getElementById('email').value;
    const role = document.getElementById('role').value;

    if (name && email && role) {
        console.log(`Registration Details: Name=${name}, Email=${email}, Role=${role}`);
        alert('Form submitted successfully!');
        closeDialog('dialog1');
        // TODO: Connect this to the backend for storing in a table
    } else {
        alert('Please fill all fields.');
    }
}
