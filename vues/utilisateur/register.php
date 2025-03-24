<div class="register-container">
    <h2>Inscription</h2>
    <?php if (isset($_SESSION["error"])) : ?>
        <div class="error"><?= $_SESSION["error"] ?></div>
        <?php unset($_SESSION["error"]); ?>
    <?php endif; ?>
    
    <form action="register" method="POST" id="registerForm">
        <div class="form-group">
            <label for="nom">Nom :</label>
            <input type="text" id="nom" name="nom" required>
        </div>
        
        <div class="form-group">
            <label for="prenom">Prénom :</label>
            <input type="text" id="prenom" name="prenom" required>
        </div>
        
        <div class="form-group">
            <label for="pseudo">Pseudo :</label>
            <input type="text" id="pseudo" name="pseudo" required>
        </div>
        
        <div class="form-group">
            <label for="email">Email :</label>
            <input type="email" id="email" name="email" required>
        </div>
        
        <div class="form-group password-field">
            <label for="password">Mot de passe :</label>
            <div class="password-input-container">
                <input type="password" id="password" name="password" required>
                <span class="toggle-password" title="Afficher/Masquer le mot de passe">
                    <i class="fa fa-eye" aria-hidden="true"></i>
                </span>
            </div>
            <div class="password-requirements">
                <p>Le mot de passe doit contenir au moins :</p>
                <ul>
                    <li id="length">12 caractères</li>
                    <li id="uppercase">Une lettre majuscule</li>
                    <li id="number">Un chiffre</li>
                    <li id="special">Un caractère spécial</li>
                </ul>
            </div>
        </div>
        
        <div class="form-group password-field">
            <label for="confirm_password">Confirmer le mot de passe :</label>
            <div class="password-input-container">
                <input type="password" id="confirm_password" name="confirm_password" required>
                <span class="toggle-password" title="Afficher/Masquer le mot de passe">
                    <i class="fa fa-eye" aria-hidden="true"></i>
                </span>
            </div>
            <div id="password-match-message"></div>
        </div>
        
        <button id="register-button" type="submit">S'inscrire</button>
    </form>
    
    <p>Déjà un compte ? <a href="login">Se connecter</a></p>
</div>

<!-- Ajouter Font Awesome pour l'icône d'œil -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<script>
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirm_password');
    const lengthCheck = document.getElementById('length');
    const uppercaseCheck = document.getElementById('uppercase');
    const numberCheck = document.getElementById('number');
    const specialCheck = document.getElementById('special');
    const registerForm = document.getElementById('registerForm');
    const passwordMatchMessage = document.getElementById('password-match-message');
    
    // Fonction pour vérifier le mot de passe
    function checkPassword() {
        const password = passwordInput.value;
        
        // Vérifier la longueur
        if (password.length >= 12) {
            lengthCheck.classList.add('valid');
        } else {
            lengthCheck.classList.remove('valid');
        }
        
        // Vérifier la présence d'une majuscule
        if (/[A-Z]/.test(password)) {
            uppercaseCheck.classList.add('valid');
        } else {
            uppercaseCheck.classList.remove('valid');
        }
        
        // Vérifier la présence d'un chiffre
        if (/[0-9]/.test(password)) {
            numberCheck.classList.add('valid');
        } else {
            numberCheck.classList.remove('valid');
        }
        
        // Vérifier la présence d'un caractère spécial
        if (/[^A-Za-z0-9]/.test(password)) {
            specialCheck.classList.add('valid');
        } else {
            specialCheck.classList.remove('valid');
        }
        
        // Vérifier si les mots de passe correspondent
        checkPasswordsMatch();
    }
    
    // Fonction pour vérifier si les mots de passe correspondent
    function checkPasswordsMatch() {
        const password = passwordInput.value;
        const confirmPassword = confirmPasswordInput.value;
        
        if (confirmPassword === '') {
            passwordMatchMessage.textContent = '';
            passwordMatchMessage.className = '';
        } else if (password === confirmPassword) {
            passwordMatchMessage.textContent = 'Les mots de passe correspondent';
            passwordMatchMessage.className = 'match-success';
        } else {
            passwordMatchMessage.textContent = 'Les mots de passe ne correspondent pas';
            passwordMatchMessage.className = 'match-error';
        }
    }
    
    // Vérifier le mot de passe à chaque modification
    passwordInput.addEventListener('input', checkPassword);
    confirmPasswordInput.addEventListener('input', checkPasswordsMatch);
    
    // Vérifier le formulaire avant soumission
    registerForm.addEventListener('submit', function(e) {
        const password = passwordInput.value;
        const confirmPassword = confirmPasswordInput.value;
        
        // Vérifier si le mot de passe respecte les exigences
        if (
            password.length < 12 || 
            !/[A-Z]/.test(password) || 
            !/[0-9]/.test(password) || 
            !/[^A-Za-z0-9]/.test(password)
        ) {
            e.preventDefault();
            alert('Veuillez respecter toutes les exigences du mot de passe.');
            return;
        }
        
        // Vérifier si les mots de passe correspondent
        if (password !== confirmPassword) {
            e.preventDefault();
            alert('Les mots de passe ne correspondent pas.');
            return;
        }
    });
    
    // Gestion des boutons pour afficher/masquer le mot de passe
    const toggleButtons = document.querySelectorAll('.toggle-password');
    toggleButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            const input = this.previousElementSibling;
            const icon = this.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });
});
</script>
