<?php
header('Content-Type: application/json');

// Configuration SendGrid
$sendgrid_api_key = 'VOTRE_CLE_API_SENDGRID';
$from_email = 'contact@sonacaf.local';
$to_email = 'elleirejzakaria@gmail.com';

// Fonction pour nettoyer les données
function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Vérifier si la requête est de type POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer et nettoyer les données du formulaire
    $nom = cleanInput($_POST["nom"] ?? '');
    $prenom = cleanInput($_POST["prenom"] ?? '');
    $annee_naissance = cleanInput($_POST["annee_naissance"] ?? '');
    $code_postal = cleanInput($_POST["code_postal"] ?? '');
    $telephone = cleanInput($_POST["telephone"] ?? '');
    $email = cleanInput($_POST["email"] ?? '');
    $service = cleanInput($_POST["service"] ?? '');
    $message = cleanInput($_POST["message"] ?? '');
    $rgpd = isset($_POST["rgpd"]) ? 1 : 0;
    
    // Validation côté serveur
    $errors = [];
    
    // Validation du nom
    if (empty($nom)) {
        $errors[] = "Le nom est requis";
    }
    
    // Validation du prénom
    if (empty($prenom)) {
        $errors[] = "Le prénom est requis";
    }
    
    // Validation de l'année de naissance
    $currentYear = date('Y');
    if (empty($annee_naissance) || !is_numeric($annee_naissance) || $annee_naissance < 1900 || $annee_naissance > ($currentYear - 18)) {
        $errors[] = "Veuillez entrer une année de naissance valide";
    }
    
    // Validation du code postal
    if (empty($code_postal) || !preg_match('/^\d{5}$/', $code_postal)) {
        $errors[] = "Veuillez entrer un code postal valide (5 chiffres)";
    }
    
    // Validation du téléphone
    if (empty($telephone) || !preg_match('/^(0|\+33)[1-9]([-. ]?\d{2}){4}$/', $telephone)) {
        $errors[] = "Veuillez entrer un numéro de téléphone valide";
    }
    
    // Validation de l'email
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Veuillez entrer une adresse email valide";
    }
    
    // Validation du service
    if (empty($service)) {
        $errors[] = "Veuillez sélectionner un objet pour votre demande";
    }
    
    // Validation du message
    if (empty($message)) {
        $errors[] = "Veuillez entrer un message";
    }
    
    // Validation RGPD
    if ($rgpd != 1) {
        $errors[] = "Vous devez accepter la politique de confidentialité";
    }
    
    // Vérification reCAPTCHA (désactivée pour les tests)
    /*
    $recaptcha_secret = "VOTRE_CLE_SECRETE_RECAPTCHA";
    $recaptcha_response = $_POST['g-recaptcha-response'] ?? '';
    $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
    $recaptcha_data = [
        'secret' => $recaptcha_secret,
        'response' => $recaptcha_response,
        'remoteip' => $_SERVER['REMOTE_ADDR']
    ];
    $recaptcha_options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($recaptcha_data)
        ]
    ];
    $recaptcha_context = stream_context_create($recaptcha_options);
    $recaptcha_result = file_get_contents($recaptcha_url, false, $recaptcha_context);
    $recaptcha_json = json_decode($recaptcha_result, true);
    if (!$recaptcha_json['success']) {
        $errors[] = "La vérification reCAPTCHA a échoué. Veuillez réessayer.";
    }
    */
    
    // Si aucune erreur, procéder à l'enregistrement et à l'envoi d'emails
    if (empty($errors)) {
        try {
            // Connexion à la base de données
            $db_host = 'localhost';
            $db_name = 'sonacaf_db';
            $db_user = 'sonacaf_user';
            $db_pass = 'password_secure';
            
            $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Préparation de la requête d'insertion
            $stmt = $pdo->prepare("INSERT INTO contacts (nom, prenom, annee_naissance, code_postal, telephone, email, service, message, rgpd) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            // Exécution de la requête
            $stmt->execute([$nom, $prenom, $annee_naissance, $code_postal, $telephone, $email, $service, $message, $rgpd]);
            
            // Récupérer l'ID du contact inséré
            $contact_id = $pdo->lastInsertId();
            
            // Envoi de l'email de confirmation au client
            $to = $email;
            $subject = "Confirmation de votre demande";
            $body = "Bonjour $prenom $nom,\n\n" .
                    "Nous avons bien reçu votre demande concernant $service.\n" .
                    "Un de nos conseillers vous contactera dans les plus brefs délais.\n\n" .
                    "Merci de votre confiance.\n" .
                    "L'équipe SONACAF";
            $headers = "From: $from_email\r\n";
            $headers .= "Reply-To: $from_email\r\n";
            $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

            // Envoyer l'email
            if (mail($to, $subject, $body, $headers)) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Votre demande a été envoyée avec succès. Vous recevrez bientôt un email de confirmation.'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Une erreur est survenue lors de l\'envoi de l\'email.'
                ]);
            }
            
        } catch (PDOException $e) {
            // Erreur de base de données
            echo json_encode([
                'success' => false,
                'message' => 'Une erreur est survenue lors de l\'enregistrement de votre demande. Veuillez réessayer plus tard.'
            ]);
            
            // Log de l'erreur (à des fins de débogage)
            error_log("Erreur de base de données: " . $e->getMessage());
        } catch (Exception $e) {
            // Autres erreurs
            echo json_encode([
                'success' => false,
                'message' => 'Une erreur inattendue est survenue. Veuillez réessayer plus tard.'
            ]);
            
            // Log de l'erreur (à des fins de débogage)
            error_log("Erreur: " . $e->getMessage());
        }
    } else {
        // Retourner les erreurs de validation
        echo json_encode([
            'success' => false,
            'message' => 'Veuillez corriger les erreurs suivantes : ' . implode(', ', $errors)
        ]);
    }
} else {
    // Si la méthode n'est pas POST
    echo json_encode([
        'success' => false,
        'message' => 'Méthode non autorisée'
    ]);
}