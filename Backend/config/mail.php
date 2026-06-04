<?php
/**
 * SMTP Configuration Template
 * ⚠️ MANUAL INPUT REQUIRED: Update these values with your SMTP provider details.
 */
return [
    'host'       => 'sandbox.smtp.mailtrap.io', // e.g., smtp.gmail.com or sandbox.smtp.mailtrap.io
    'auth'       => true,
    'username'   => 'YOUR_SMTP_USERNAME',       // ⚠️ UPDATE THIS
    'password'   => 'YOUR_SMTP_PASSWORD',       // ⚠️ UPDATE THIS
    'secure'     => 'tls',                      // 'ssl' or 'tls'
    'port'       => 587,                         // 465 (ssl) or 587 (tls) or 2525
    'from_email' => 'no-reply@shaheenenterprise.com',
    'from_name'  => 'Shaheen Enterprise',
];
?>
