const form = document.getElementById('form-inscription');
const passwordInput = document.getElementById('password');
const feedback = document.getElementById('password-feedback');
const submitBtn = document.getElementById('submit-btn');

function verifierMotDePasse(password) {
    const règlesNonRespectées = [];

    if (password.length < 8) {
        règlesNonRespectées.push("au moins 8 caractères");
    }
    if (!/[A-Z]/.test(password)) {
        règlesNonRespectées.push("une majuscule");
    }
    if (!/[a-z]/.test(password)) {
        règlesNonRespectées.push("une minuscule");
    }
    if (!/[0-9]/.test(password)) {
        règlesNonRespectées.push("un chiffre");
    }

    return règlesNonRespectées;
}

passwordInput.addEventListener('keyup', function () {
    const password = this.value;
    const erreurs = verifierMotDePasse(password);

    if (erreurs.length === 0) {
        feedback.style.color = 'green';
        feedback.textContent = "✅ Mot de passe sécurisé.";
    } else {
        feedback.style.color = 'red';
        feedback.textContent = "Le mot de passe doit contenir " + erreurs.join(", ") + ".";
    }
});

// Empêche la soumission si le mot de passe est invalide
form.addEventListener('submit', function (e) {
    const password = passwordInput.value;
    const erreurs = verifierMotDePasse(password);

    if (erreurs.length > 0) {
        e.preventDefault();
        feedback.style.color = 'red';
        feedback.textContent = "❌ Le mot de passe doit contenir " + erreurs.join(", ") + ".";
    }
});