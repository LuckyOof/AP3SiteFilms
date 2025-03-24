<div class="login-container">
    <h2>Connexion</h2>
    <?php if (isset($_SESSION["error"])) : ?>
        <div class="error"><?= $_SESSION["error"] ?></div>
        <?php unset($_SESSION["error"]); ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION["success"])) : ?>
        <div class="success"><?= $_SESSION["success"] ?></div>
        <?php unset($_SESSION["success"]); ?>
    <?php endif; ?>
    
    <form action="login" method="POST">
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
        </div>
        
        <button id="login-button" type="submit">Se connecter</button>
    </form>
    
    <p>Pas encore de compte ? <a href="register">S'inscrire</a></p>
</div>

<!-- Ajouter Font Awesome pour l'icu00f4ne d'u0153il -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<script>
document.addEventListener('DOMContentLoaded', function() {
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
