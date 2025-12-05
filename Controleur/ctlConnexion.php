<?php
require_once __DIR__ . '/../Model/UserModel.php';

// Gestion des actions
$action = $_GET['action'] ?? 'connexion';

switch ($action) {
    case 'connexion':
        // Afficher le formulaire de connexion
        include __DIR__ . '/../Vue/vueConnexion/v_form_connexion.php';
        break;
        
    case 'inscription':
        // Afficher le formulaire d'inscription
        include __DIR__ . '/../Vue/vueConnexion/v_form_inscription.php';
        break;
        
    case 'veriflogin':
        // Vérifier les identifiants
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            
            if (empty($email) || empty($password)) {
                $_SESSION['error'] = 'Veuillez remplir tous les champs';
                header('Location: index.php?ctl=connexion&action=connexion');
                exit();
            }
            
            $result = UserModel::login($email, $password);
            
            if ($result['success']) {
                // Connexion réussie
                $_SESSION['connect'] = true;
                $_SESSION['user_id'] = $result['user']['id'];
                $_SESSION['user_email'] = $result['user']['email'];
                $_SESSION['user_name'] = $result['user']['name'];
                
                header('Location: index.php');
                exit();
            } else {
                $_SESSION['error'] = $result['message'];
                header('Location: index.php?ctl=connexion&action=connexion');
                exit();
            }
        }
        break;
        
    case 'createaccount':
        // Créer un nouveau compte
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $password_confirm = $_POST['password_confirm'] ?? '';
            $name = $_POST['name'] ?? '';
            
            // Validation
            if (empty($email) || empty($password) || empty($password_confirm) || empty($name)) {
                $_SESSION['error'] = 'Veuillez remplir tous les champs';
                header('Location: index.php?ctl=connexion&action=inscription');
                exit();
            }
            
            if ($password !== $password_confirm) {
                $_SESSION['error'] = 'Les mots de passe ne correspondent pas';
                header('Location: index.php?ctl=connexion&action=inscription');
                exit();
            }
            
            if (strlen($password) < 6) {
                $_SESSION['error'] = 'Le mot de passe doit contenir au moins 6 caractères';
                header('Location: index.php?ctl=connexion&action=inscription');
                exit();
            }
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error'] = 'Email invalide';
                header('Location: index.php?ctl=connexion&action=inscription');
                exit();
            }
            
            // Créer le compte
            $result = UserModel::createUser($email, $password, $name);
            
            if ($result['success']) {
                // Connexion automatique après inscription
                $_SESSION['connect'] = true;
                $_SESSION['user_id'] = $result['userId'];
                $_SESSION['user_email'] = $email;
                $_SESSION['user_name'] = $name;
                
                $_SESSION['success'] = 'Compte créé avec succès !';
                header('Location: index.php');
                exit();
            } else {
                $_SESSION['error'] = $result['message'];
                header('Location: index.php?ctl=connexion&action=inscription');
                exit();
            }
        }
        break;
        
    case 'deconnexion':
        // Déconnexion
        session_destroy();
        header('Location: index.php');
        exit();
        break;
        
    default:
        include __DIR__ . '/../Vue/vueConnexion/v_form_connexion.php';
        break;
}
?>

