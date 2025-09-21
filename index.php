<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="SONACAF - Centre d'appel spécialisé dans les services clients et courtage d'assurance">
    <title>SONACAF - Courtier d'Assurance</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>
    <!-- Header fixe -->
    <header>
        <div class="container">
            <div class="logo">
                <a href="index.php">SONACAF</a>
            </div>
            <nav>
                <div class="hamburger">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
                <ul class="menu">
                    <li><a href="index.php" class="active">Accueil</a></li>
                    <li><a href="services.html">Services</a></li>
                    <li><a href="about.html">À propos</a></li>
                    <li><a href="contact.html">Contact</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Hero section avec formulaire -->
<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

$messageStatus = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nom        = trim($_POST["nom"] ?? '');
    $prenom     = trim($_POST["prenom"] ?? '');
    $annee      = trim($_POST["annee_naissance"] ?? '');
    $code_postal= trim($_POST["code_postal"] ?? '');
    $telephone  = trim($_POST["telephone"] ?? '');
    $email      = filter_var(trim($_POST["email"] ?? ''), FILTER_VALIDATE_EMAIL);
    $service    = trim($_POST["service"] ?? '');
    $message    = trim($_POST["message"] ?? '');
    $rgpd       = isset($_POST["rgpd"]);

    if (!$nom || !$prenom || !$annee || !$code_postal || !$telephone || !$email || !$service || !$message || !$rgpd) {
        $messageStatus = "<div class='alert alert-danger'>⚠️ Veuillez remplir tous les champs.</div>";
    } else {
        // Email design pro (tableau HTML stylé)
        $body = "
        <div style='font-family:Arial,sans-serif;background:#f8f9fa;padding:20px'>
            <div style='max-width:600px;margin:auto;background:#fff;border-radius:8px;box-shadow:0 3px 8px rgba(0,0,0,0.1);padding:20px'>
                <h2 style='text-align:center;color:#0056b3;margin-bottom:20px'>📩 Nouvelle demande de devis</h2>
                <table style='width:100%;border-collapse:collapse'>
                    <tr><td style='padding:10px;border:1px solid #ddd'><b>Nom</b></td><td style='padding:10px;border:1px solid #ddd'>$nom</td></tr>
                    <tr><td style='padding:10px;border:1px solid #ddd'><b>Prénom</b></td><td style='padding:10px;border:1px solid #ddd'>$prenom</td></tr>
                    <tr><td style='padding:10px;border:1px solid #ddd'><b>Année de naissance</b></td><td style='padding:10px;border:1px solid #ddd'>$annee</td></tr>
                    <tr><td style='padding:10px;border:1px solid #ddd'><b>Code postal</b></td><td style='padding:10px;border:1px solid #ddd'>$code_postal</td></tr>
                    <tr><td style='padding:10px;border:1px solid #ddd'><b>Téléphone</b></td><td style='padding:10px;border:1px solid #ddd'>$telephone</td></tr>
                    <tr><td style='padding:10px;border:1px solid #ddd'><b>Email</b></td><td style='padding:10px;border:1px solid #ddd'>$email</td></tr>
                    <tr><td style='padding:10px;border:1px solid #ddd'><b>Service demandé</b></td><td style='padding:10px;border:1px solid #ddd'>$service</td></tr>
                    <tr><td style='padding:10px;border:1px solid #ddd'><b>Message</b></td><td style='padding:10px;border:1px solid #ddd'>".nl2br(htmlspecialchars($message))."</td></tr>
                </table>
                <p style='text-align:center;color:#555;margin-top:20px;font-size:14px'>🔒 Données transmises conformément à la politique RGPD.</p>
            </div>
        </div>";

        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host       = "smtp.gmail.com";
            $mail->SMTPAuth   = true;

            // ⚠️ Mets ton Gmail + mot de passe application
            $mail->Username   = "elleirejzakaria@gmail.com";
            $mail->Password   = "ssqe vuve qifk tmmf";

            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;
            $mail->CharSet    = "UTF-8";

            $mail->setFrom("elleirejzakaria@gmail.com", "Formulaire Devis");
            $mail->addAddress("elleirejzakaria@gmail.com");

            $mail->isHTML(true);
            $mail->Subject = "Nouvelle demande de devis - $nom $prenom";
            $mail->Body    = $body;

            $mail->send();
            $messageStatus = "<div class='alert alert-success'>✅ Votre demande a été envoyée avec succès !</div>";
        } catch (Exception $e) {
            $messageStatus = "<div class='alert alert-danger'>❌ Erreur : {$mail->ErrorInfo}</div>";
        }
    }
}
?>
<head></head>
    <style>
   
        .hero {
            display: flex;
            gap: 40px;
            max-width: 1100px;
            background: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .hero-content {
            flex: 1;
        }
        .hero-content h1 {
            font-size: 28px;
            color: #1e40af;
        }
        .hero-content p {
            margin-top: 10px;
            color: #555;
        }
        .hero-form {
            flex: 1;
            background: #f9fafb;
            padding: 25px;
            border-radius: 12px;
        }
        .hero-form h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #1e40af;
        }
        .form-group {
            margin-bottom: 15px;
        }
        input, select, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 15px;
        }
        textarea {
            resize: none;
            height: 100px;
        }
        .checkbox {
            display: flex;
            align-items: center;
            font-size: 13px;
            color: #555;
        }
        .checkbox input {
            margin-right: 8px;
        }
        button {
            background: #1e40af;
            color: white;
            border: none;
            padding: 12px;
            width: 100%;
            font-size: 16px;
            border-radius: 6px;
            cursor: pointer;
            margin-top: 10px;
        }
        button:hover {
            background: #1c3a9a;
        }
        .alert {
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 6px;
            font-size: 14px;
            text-align: center;
        }
        .alert-success {
            background: #d1fae5;
            color: #065f46;
        }
        .alert-danger {
            background: #fee2e2;
            color: #991b1b;
        }
    </style>
</head>
<body>
<section class="hero">
    <div class="hero-content"><br><br><br><br><br><br><br><br><br>
        <h1>Votre courtier d'assurance de confiance</h1>
        <p>Des solutions d'assurance adaptées à vos besoins personnels et professionnels</p>
    </div>
    <div class="hero-form"><br>
        <h2>Demandez votre devis gratuit</h2>
        <?= $messageStatus ?>
        <form method="POST">
            <div class="form-group"><input type="text" name="nom" placeholder="Nom" required></div>
            <div class="form-group"><input type="text" name="prenom" placeholder="Prénom" required></div>
            <div class="form-group"><input type="number" name="annee_naissance" placeholder="Année de naissance" min="1900" max="2006" required></div>
            <div class="form-group"><input type="text" name="code_postal" placeholder="Code postal" pattern="[0-9]{5}" required></div>
            <div class="form-group"><input type="tel" name="telephone" placeholder="Téléphone" pattern="[0-9]{10}" required></div>
            <div class="form-group"><input type="email" name="email" placeholder="Adresse email" required></div>
            <div class="form-group">
                <select name="service" required>
                    <option value="" disabled selected>Objet de votre demande</option>
                    <option value="assurance_auto">Assurance Auto</option>
                    <option value="assurance_habitation">Assurance Habitation</option>
                    <option value="assurance_sante">Assurance Santé</option>
                    <option value="assurance_vie">Assurance Vie</option>
                    <option value="autre">Autre</option>
                </select>
            </div>
            <div class="form-group"><textarea name="message" placeholder="Votre message" required></textarea></div>
            <div class="form-group checkbox">
                <input type="checkbox" id="rgpd" name="rgpd" required>
                <label for="rgpd">J'accepte que mes données soient traitées conformément à la politique de confidentialité RGPD</label>
            </div>
            <button type="submit" name="send">Mon devis gratuit</button>
        </form>
    </div>
</section>



<!-- ... Le reste du code HTML (partenaires, témoignages, footer) reste inchangé ... -->
 

    <!-- Section partenaires -->
    <section class="partners">
        <div class="container">
            <h2>Nos partenaires</h2>
            <div class="partners-marquee">
                <div class="marquee-track">
                    <div class="partner"><img src="imagesonacaf/2MA.png" alt="Logo du site"></div>
                    <div class="partner"><img src="imagesonacaf/CEGEMA.png" alt="Partenaire 2"></div>
                    <div class="partner"><img src="imagesonacaf/RCD.png" alt="Partenaire 3"></div>
                    <div class="partner"><img src="imagesonacaf/ECA.png" alt="Partenaire 4"></div>
                    <div class="partner"><img src="imagesonacaf/APRIL.png" alt="Partenaire 5"></div>
                    <div class="partner"><img src="imagesonacaf/GROUPELEADER.png" alt="Partenaire 6"></div>
                    <div class="partner"><img src="imagesonacaf/SPVIE.png" alt="Partenaire 7"></div>
                    <div class="partner"><img src="imagesonacaf/netvox.png" alt="Partenaire 8"></div>
                    <div class="partner"><img src="imagesonacaf/ZEPHIR.png" alt="Partenaire 9"></div>
                    <div class="partner"><img src="imagesonacaf/maxance.png" alt="Partenaire 10"></div>
                    <!-- Dupliquer pour effet infini -->
                    <div class="partner"><img src="imagesonacaf/2MA.png" alt="Logo du site"></div>
                    <div class="partner"><img src="imagesonacaf/CEGEMA.png" alt="Partenaire 2"></div>
                    <div class="partner"><img src="imagesonacaf/RCD.png" alt="Partenaire 3"></div>
                    <div class="partner"><img src="imagesonacaf/ECA.png" alt="Partenaire 4"></div>
                    <div class="partner"><img src="imagesonacaf/APRIL.png" alt="Partenaire 5"></div>
                    <div class="partner"><img src="imagesonacaf/GROUPELEADER.png" alt="Partenaire 6"></div>
                    <div class="partner"><img src="imagesonacaf/SPVIE.png" alt="Partenaire 7"></div>
                    <div class="partner"><img src="imagesonacaf/netvox.png" alt="Partenaire 8"></div>
                    <div class="partner"><img src="imagesonacaf/ZEPHIR.png" alt="Partenaire 9"></div>
                    <div class="partner"><img src="imagesonacaf/maxance.png" alt="Partenaire 10"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section témoignages -->
    <section class="testimonials">
        <div class="container">
            <h2>Ce que nos clients disent</h2>
            <div class="testimonials-slider">
                <div class="testimonial">
                    <div class="testimonial-content">
                        <p>"SONACAF m'a trouvé une assurance auto parfaitement adaptée à mes besoins et à mon budget. Service rapide et efficace !"</p>
                    </div>
                    <div class="testimonial-author">
                        <h4>Marie Dupont</h4>
                        <p>Cliente depuis 2020</p>
                    </div>
                </div>
                <div class="testimonial">
                    <div class="testimonial-content">
                        <p>"Grâce à leur expertise, j'ai pu économiser plus de 30% sur mon assurance habitation tout en améliorant mes garanties."</p>
                    </div>
                    <div class="testimonial-author">
                        <h4>Thomas Martin</h4>
                        <p>Client depuis 2019</p>
                    </div>
                </div>
                <div class="testimonial">
                    <div class="testimonial-content">
                        <p>"Un service client exceptionnel ! Toujours disponibles et à l'écoute pour répondre à mes questions."</p>
                    </div>
                    <div class="testimonial-author">
                        <h4>Sophie Leroy</h4>
                        <p>Cliente depuis 2021</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-logo">
                    <h3>SONACAF</h3>
                    <p>Votre courtier d'assurance de confiance</p>
                </div>
                <div class="footer-links">
                    <h4>Liens rapides</h4>
                    <ul>
                        <li><a href="index.html">Accueil</a></li>
                        <li><a href="services.html">Services</a></li>
                        <li><a href="about.html">À propos</a></li>
                        <li><a href="contact.html">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-services">
                    <h4>Nos services</h4>
                    <ul>
                        <li>Assurance Auto</li>
                        <li>Assurance Habitation</li>
                        <li>Assurance Santé</li>
                        <li>Assurance Vie</li>
                    </ul>
                </div>
                <div class="footer-newsletter">
                    <h4>Newsletter</h4>
                    <p>Inscrivez-vous pour recevoir nos actualités</p>
                    <form>
                        <input type="email" placeholder="Votre email">
                        <button type="submit">S'inscrire</button>
                    </form>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2023 SONACAF. Tous droits réservés.</p>
                <div class="legal-links">
                    <a href="#">Mentions légales</a>
                    <a href="#">Politique de confidentialité</a>
                    <a href="#">CGU</a>
                </div>
            </div>
        </div>
    </footer>


<script src="script.js"></script>
</body>
</html>
