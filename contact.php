<?php
// Autoload PHPMailer si installé via Composer
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}
// Configuration
// Destinataire et sujet
$to_email = "djorweboulga4@gmail.com";
$subject_prefix = "[Portfolio] ";

// Configuration SMTP (Gmail)
$smtp_host = 'smtp.gmail.com';
$smtp_port = 587;
$smtp_secure = 'tls';
$smtp_user = 'djorweboulga4@gmail.com';
// Le code fourni contient des espaces, on les retire pour un app password Gmail
// Corrige l'utilisation de str_replace: retirer les espaces du mot de passe d'application si fourni avec espaces
$smtp_pass = str_replace(' ', '', 'nlps stlr gkff mgzg');
// Utiliser un expéditeur fixe (domaine valide requis en prod) et Reply-To pour l'adresse de l'expéditeur
$from_email = "no-reply@localhost"; // A remplacer par no-reply@votre-domaine.tld

// Vérification de la méthode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Récupération et nettoyage des données
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    // Validation des données
    $errors = [];
    
    if (empty($name)) {
        $errors[] = "Le nom est requis.";
    }
    
    if (empty($email)) {
        $errors[] = "L'email est requis.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "L'email n'est pas valide.";
    }
    
    if (empty($subject)) {
        $errors[] = "Le sujet est requis.";
    }
    
    if (empty($message)) {
        $errors[] = "Le message est requis.";
    }
    
    // Vérification anti-spam basique
    if (strlen($message) < 10) {
        $errors[] = "Le message est trop court.";
    }
    
    // Si pas d'erreurs, envoi de l'email
    if (empty($errors)) {
        
        // Préparation de l'email (une information par ligne)
        $email_subject = $subject_prefix . $subject;
        $email_body =
            "Nouveau message reçu depuis votre portfolio:<br>" . "\r\n\r\n" . 
            "Nom: " . htmlspecialchars($name) . "<br>\r\n" . 
            "Email: " . htmlspecialchars($email) . "<br>\r\n" . 
            // Sujet et Message sur la même ligne, comme demandé
            "Sujet: " . htmlspecialchars($subject) . " " .
            "Message: " . htmlspecialchars($message) . "<br>\r\n" . 
            "--- " .
            "Envoyé le " . date('d/m/Y à H:i:s') . "<br> " . 
            "IP: " . $_SERVER['REMOTE_ADDR'];
        
        // Si PHPMailer est disponible, utiliser SMTP Gmail
        // Vérifications d'environnement
        $opensslLoaded = extension_loaded('openssl');
        $smtpDebug = [];

        if (class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
            try {
                $mailer = new PHPMailer\PHPMailer\PHPMailer(true);
                $mailer->isSMTP();
                $mailer->Host = $smtp_host;
                $mailer->SMTPAuth = true;
                $mailer->Username = $smtp_user;
                $mailer->Password = $smtp_pass;
                $mailer->SMTPSecure = $smtp_secure; // tls par défaut
                $mailer->Port = $smtp_port; // 587 par défaut
                // Debug SMTP capturé dans la réponse JSON en cas d'erreur
                $mailer->SMTPDebug = 2; // messages client/serveur
                $mailer->Debugoutput = function($str, $level) use (&$smtpDebug) {
                    $smtpDebug[] = trim($str);
                };

                $mailer->CharSet = 'UTF-8';
                $mailer->setFrom($smtp_user, 'Portfolio - Djorwe Boulga');
                $mailer->addReplyTo($email, $name ?: $email);
                $mailer->addAddress($to_email);
                $mailer->Subject = $email_subject;
                $mailer->Body = $email_body;
                $mailer->AltBody = strip_tags($email_body);

                try {
                    $mailer->send();
                } catch (Exception $firstEx) {
                    // Retry fallback en SSL:465 si TLS:587 échoue
                    $mailer->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS; // ssl
                    $mailer->Port = 465;
                    $mailer->send();
                }

                // Email de confirmation à l'expéditeur
                try {
                    $confirm = new PHPMailer\PHPMailer\PHPMailer(true);
                    $confirm->isSMTP();
                    $confirm->Host = $smtp_host;
                    $confirm->SMTPAuth = true;
                    $confirm->Username = $smtp_user;
                    $confirm->Password = $smtp_pass;
                    $confirm->SMTPSecure = $smtp_secure;
                    $confirm->Port = $smtp_port;
                    $confirm->CharSet = 'UTF-8';
                    $confirm->setFrom($smtp_user, 'Portfolio - Djorwe Boulga');
                    $confirm->addAddress($email, $name ?: $email);
                    $confirm->Subject = 'Confirmation - Votre message a été envoyé';
                    $confirm->Body = "Bonjour " . htmlspecialchars($name) . ",\n\nMerci pour votre message ! Je vous répondrai dans les plus brefs délais.\n\nVotre message :\n" . htmlspecialchars($message) . "\n\nCordialement,\nDjorwe Boulga";
                    $confirm->AltBody = strip_tags($confirm->Body);
                    $confirm->send();
                } catch (Exception $e) {
                    // on ignore l'échec de confirmation
                }

                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Votre message a été envoyé avec succès !']);
                exit;
            } catch (Exception $e) {
                // Journalisation SMTP détaillée
                $logDir = __DIR__ . DIRECTORY_SEPARATOR . 'logs';
                if (!is_dir($logDir)) { @mkdir($logDir, 0777, true); }
                $errorLine = sprintf('[%s] SMTP Error: %s', date('Y-m-d H:i:s'), $e->getMessage());
                @file_put_contents($logDir . DIRECTORY_SEPARATOR . 'mail.log', $errorLine."\n", FILE_APPEND);

                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'Erreur SMTP lors de l\'envoi via Gmail.',
                    'details' => $e->getMessage(),
                    'openssl' => $opensslLoaded ? 'loaded' : 'missing',
                    'hint' => 'Vérifiez app password, 2FA, pare-feu ports 587/465, et extension OpenSSL.',
                    'smtp_debug' => $smtpDebug
                ]);
                exit;
            }
        }

        // Sinon, fallback mail() local (peut échouer sur WAMP sans SMTP local)
        $from_email = 'djorweboulga4@gmail.com';
        $headers = [
            'From: ' . $from_email,
            'Reply-To: ' . $email,
            'X-Mailer: PHP/' . phpversion(),
            'Content-Type: text/plain; charset=UTF-8'
        ];
        $mail_sent = @mail($to_email, $email_subject, $email_body, implode("\r\n", $headers));
        if ($mail_sent) {
            
            // Email de confirmation pour l'expéditeur
            $confirmation_subject = "Confirmation - Votre message a été envoyé";
            $confirmation_body = "
            Bonjour " . htmlspecialchars($name) . ",
            
            Merci pour votre message ! Je vous répondrai dans les plus brefs délais.
            
            Votre message :
            " . htmlspecialchars($message) . "
            
            Cordialement,
            Djorwe Boulga
            Développeur Fullstack
            ";
            
            mail($email, $confirmation_subject, $confirmation_body, implode("\r\n", [
                'From: ' . $to_email,
                'X-Mailer: PHP/' . phpversion(),
                'Content-Type: text/plain; charset=UTF-8'
            ]));
            
            // Réponse de succès
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Votre message a été envoyé avec succès !'
            ]);
            exit;
            
        } else {
            // Journaliser et sauvegarder localement le message pour diagnostic
            $logDir = __DIR__ . DIRECTORY_SEPARATOR . 'logs';
            $msgDir = __DIR__ . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'messages';
            if (!is_dir($logDir)) { @mkdir($logDir, 0777, true); }
            if (!is_dir($msgDir)) { @mkdir($msgDir, 0777, true); }

            $timestamp = date('Y-m-d_His');
            $logMessage = sprintf(
                "[%s] Envoi échoué -> To:%s | From:%s | Reply-To:%s | Subject:%s\n",
                $timestamp,
                $to_email,
                $from_email,
                $email,
                $email_subject
            );
            @file_put_contents($logDir . DIRECTORY_SEPARATOR . 'mail.log', $logMessage, FILE_APPEND);
            @file_put_contents($msgDir . DIRECTORY_SEPARATOR . $timestamp . '.txt', $email_body);

            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => "PHPMailer non détecté: installez-le pour l'envoi SMTP Gmail (composer require phpmailer/phpmailer), ou configurez un SMTP local. Le message a été sauvegardé.",
                'hint' => 'Sur WAMP, mail() échoue souvent sans serveur SMTP local. Préférez PHPMailer + SMTP Gmail.',
            ]);
            exit;
        }
        
    } else {
        // Erreurs de validation
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Erreurs de validation :',
            'errors' => $errors
        ]);
        exit;
    }
    
} else {
    // Méthode non autorisée
    header('HTTP/1.1 405 Method Not Allowed');
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Méthode non autorisée.'
    ]);
    exit;
}
?>
