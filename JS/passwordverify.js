function validateForm() {
    const password = document.getElementById('password').value;
    const verifyPassword = document.getElementById('verif_mot_de_passe').value;
    const passwordError = document.getElementById('password_error');

    if (password !== verifyPassword) {
        passwordError.textContent = "Les mots de passe ne correspondent pas.";
        return false;
    } else {
        passwordError.textContent = "";
        return true;
    }
}