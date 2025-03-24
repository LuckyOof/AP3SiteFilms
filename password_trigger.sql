-- Trigger pour vérifier la complexité des mots de passe
DELIMITER //

-- Supprimer le trigger s'il existe déjà
DROP TRIGGER IF EXISTS before_insert_utilisateur //
DROP TRIGGER IF EXISTS before_update_utilisateur //

-- Trigger pour vérifier le mot de passe avant insertion
CREATE TRIGGER before_insert_utilisateur
BEFORE INSERT ON Utilisateur
FOR EACH ROW
BEGIN
    -- Vérifier si le mot de passe est déjà hashé (commence par $2y$)
    IF NEW.motDePasse NOT LIKE '$2y$%' THEN
        -- Vérification de la longueur (au moins 12 caractères)
        IF LENGTH(NEW.motDePasse) < 12 THEN
            SIGNAL SQLSTATE '45000' 
            SET MESSAGE_TEXT = 'Le mot de passe doit contenir au moins 12 caractères';
        END IF;
        
        -- Vérification de la présence d'au moins une lettre majuscule
        IF NEW.motDePasse NOT REGEXP '[A-Z]' THEN
            SIGNAL SQLSTATE '45000' 
            SET MESSAGE_TEXT = 'Le mot de passe doit contenir au moins une lettre majuscule';
        END IF;
        
        -- Vérification de la présence d'au moins un chiffre
        IF NEW.motDePasse NOT REGEXP '[0-9]' THEN
            SIGNAL SQLSTATE '45000' 
            SET MESSAGE_TEXT = 'Le mot de passe doit contenir au moins un chiffre';
        END IF;
        
        -- Vérification de la présence d'au moins un caractère spécial
        IF NEW.motDePasse NOT REGEXP '[^A-Za-z0-9]' THEN
            SIGNAL SQLSTATE '45000' 
            SET MESSAGE_TEXT = 'Le mot de passe doit contenir au moins un caractère spécial';
        END IF;
    END IF;
END //

-- Trigger pour vérifier le mot de passe avant mise à jour
CREATE TRIGGER before_update_utilisateur
BEFORE UPDATE ON Utilisateur
FOR EACH ROW
BEGIN
    -- Vérifier si le mot de passe a été modifié et n'est pas déjà hashé
    IF NEW.motDePasse != OLD.motDePasse AND NEW.motDePasse NOT LIKE '$2y$%' THEN
        -- Vérification de la longueur (au moins 12 caractères)
        IF LENGTH(NEW.motDePasse) < 12 THEN
            SIGNAL SQLSTATE '45000' 
            SET MESSAGE_TEXT = 'Le mot de passe doit contenir au moins 12 caractères';
        END IF;
        
        -- Vérification de la présence d'au moins une lettre majuscule
        IF NEW.motDePasse NOT REGEXP '[A-Z]' THEN
            SIGNAL SQLSTATE '45000' 
            SET MESSAGE_TEXT = 'Le mot de passe doit contenir au moins une lettre majuscule';
        END IF;
        
        -- Vérification de la présence d'au moins un chiffre
        IF NEW.motDePasse NOT REGEXP '[0-9]' THEN
            SIGNAL SQLSTATE '45000' 
            SET MESSAGE_TEXT = 'Le mot de passe doit contenir au moins un chiffre';
        END IF;
        
        -- Vérification de la présence d'au moins un caractère spécial
        IF NEW.motDePasse NOT REGEXP '[^A-Za-z0-9]' THEN
            SIGNAL SQLSTATE '45000' 
            SET MESSAGE_TEXT = 'Le mot de passe doit contenir au moins un caractère spécial';
        END IF;
    END IF;
END //

DELIMITER ;
