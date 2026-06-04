<?php
/**
 * EmailService - Handles transactional emails with PHPMailer.
 * Falls back to local logging if SMTP is not configured or fails.
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../../vendor/autoload.php';

class EmailService
{
    private $logFile;
    private $config;

    public function __construct()
    {
        $this->logFile = __DIR__ . '/../../logs/email.log';
        if (!file_exists(dirname($this->logFile))) {
            mkdir(dirname($this->logFile), 0777, true);
        }

        $configFile = __DIR__ . '/../config/mail.php';
        $this->config = file_exists($configFile) ? require $configFile : null;
    }

    /**
     * Sends a Welcome email after registration
     */
    public function sendWelcome($email, $username)
    {
        $subject = "Welcome to Shaheen Enterprise!";
        $body = "
        <div style='font-family: sans-serif; background: #FFF7E2; padding: 50px; color: #1a1a1a;'>
            <div style='max-width: 600px; margin: 0 auto; background: white; padding: 40px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.05);'>
                <h1 style='color: #4F633D; border-bottom: 2px solid #8BA194; padding-bottom: 20px;'>Welcome to the Ritual, $username.</h1>
                <p style='font-size: 16px; line-height: 1.6;'>Thank you for joining Shaheen Enterprise. We are dedicated to bringing you the purest organic wellness products, crafted with care and ancient wisdom.</p>
                <div style='text-align: center; margin: 30px 0;'>
                    <a href='http://localhost/shop.php' style='display: inline-block; background: #4F633D; color: white; padding: 15px 30px; text-decoration: none; border-radius: 4px; font-weight: 600;'>Explore Our Collection</a>
                </div>
                <p style='margin-top: 40px; font-size: 14px; color: #888;'>Best regards,<br>The Shaheen Enterprise Team</p>
            </div>
        </div>";

        return $this->send($email, $subject, $body);
    }

    /**
     * Sends an Order Confirmation email
     */
    public function sendOrderConfirmation($email, $username, $orderId, $total)
    {
        $subject = "Order Confirmed: #$orderId";
        $body = "
        <div style='font-family: sans-serif; background: #FFF7E2; padding: 50px; color: #1a1a1a;'>
            <div style='max-width: 600px; margin: 0 auto; background: white; padding: 40px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.05);'>
                <h1 style='color: #4F633D;'>Thank you for your order!</h1>
                <p style='font-size: 16px; line-height: 1.6;'>Hi $username, we've received your order <strong>#$orderId</strong> and are preparing it with care.</p>
                <div style='background: #fdfdfd; padding: 25px; border-radius: 8px; border: 1px solid #f0f0f0; margin: 25px 0;'>
                    <p style='margin: 0; font-size: 15px;'><strong>Order Total:</strong> $$total</p>
                    <p style='margin: 5px 0 0; font-size: 15px;'><strong>Status:</strong> Processing</p>
                </div>
                <p style='font-size: 14px;'>We will notify you once your botanical treasures are on their way. You can track your order status in your profile.</p>
                <p style='margin-top: 40px; font-size: 14px; color: #888;'>Warmly,<br>The Shaheen Enterprise Team</p>
            </div>
        </div>";

        return $this->send($email, $subject, $body);
    }

    /**
     * Primary mailing logic with PHPMailer and Log Fallback
     */
    private function send($to, $subject, $body)
    {
        // 1. Log the attempt locally
        $this->logEmail($to, $subject, $body);

        // 2. Check if configuration is set for SMTP
        if (!$this->config || $this->config['username'] === 'YOUR_SMTP_USERNAME') {
            return true; // Fallback to log only
        }

        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = $this->config['host'];
            $mail->SMTPAuth   = $this->config['auth'];
            $mail->Username   = $this->config['username'];
            $mail->Password   = $this->config['password'];
            $mail->SMTPSecure = $this->config['secure'] === 'tls' ? PHPMailer::ENCRYPTION_STARTTLS : PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = $this->config['port'];

            // Recipients
            $mail->setFrom($this->config['from_email'], $this->config['from_name']);
            $mail->addAddress($to);

            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;
            $mail->AltBody = strip_tags(str_replace(['<br>', '</div>', '</p>'], ["\n", "\n", "\n\n"], $body));

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("PHPMailer Error: {$mail->ErrorInfo}");
            return false; // Result is logged, but SMTP failed
        }
    }

    private function logEmail($to, $subject, $body)
    {
        $timestamp = date('Y-m-d H:i:s');
        $cleanBody = strip_tags($body);
        $entry = "[$timestamp] TO: $to | SUBJECT: $subject | MOCK_SENT: Yes" . PHP_EOL;
        file_put_contents($this->logFile, $entry, FILE_APPEND);
        return true;
    }
}
?>