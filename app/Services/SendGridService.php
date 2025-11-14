<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use SendGrid;
use SendGrid\Mail\Mail;

class SendGridService
{
    protected $sendGrid;

    public function __construct()
    {
        $apiKey = env('SENDGRID_API_KEY');
        if (!$apiKey) {
            throw new \Exception('SENDGRID_API_KEY no está configurada en el archivo .env');
        }
        $this->sendGrid = new SendGrid($apiKey);
    }

    /**
     * Enviar código OTP para activación de cuenta
     */
    public function sendActivationOTP(string $email, string $otpCode): bool
    {
        try {
            $fromEmail = env('SENDGRID_FROM_EMAIL');
            $fromName = env('SENDGRID_FROM_NAME', 'Módulo Usuario API');

            if (!$fromEmail) {
                throw new \Exception('SENDGRID_FROM_EMAIL no está configurada en el archivo .env');
            }

            $mail = new Mail();
            $mail->setFrom($fromEmail, $fromName);
            $mail->setSubject('Código de activación - Módulo Usuario');
            $mail->addTo($email);

            $htmlContent = $this->getActivationEmailTemplate($otpCode);
            $mail->addContent('text/html', $htmlContent);

            $response = $this->sendGrid->send($mail);

            if ($response->statusCode() >= 200 && $response->statusCode() < 300) {
                Log::info("Correo de activación enviado a: {$email}");
                return true;
            } else {
                Log::error("Error enviando correo de activación: " . $response->body());
                return false;
            }
        } catch (\Exception $e) {
            Log::error("Error en SendGridService::sendActivationOTP: " . $e->getMessage());
            throw new \Exception('No se pudo enviar el correo de activación: ' . $e->getMessage());
        }
    }

    /**
     * Enviar código OTP para recuperación de contraseña
     */
    public function sendPasswordRecoveryOTP(string $email, string $otpCode): bool
    {
        try {
            $fromEmail = env('SENDGRID_FROM_EMAIL');
            $fromName = env('SENDGRID_FROM_NAME', 'Módulo Usuario API');

            if (!$fromEmail) {
                throw new \Exception('SENDGRID_FROM_EMAIL no está configurada en el archivo .env');
            }

            $mail = new Mail();
            $mail->setFrom($fromEmail, $fromName);
            $mail->setSubject('Recuperación de contraseña - Módulo Usuario');
            $mail->addTo($email);

            $htmlContent = $this->getPasswordRecoveryEmailTemplate($otpCode);
            $mail->addContent('text/html', $htmlContent);

            $response = $this->sendGrid->send($mail);

            if ($response->statusCode() >= 200 && $response->statusCode() < 300) {
                Log::info("Correo de recuperación enviado a: {$email}");
                return true;
            } else {
                Log::error("Error enviando correo de recuperación: " . $response->body());
                return false;
            }
        } catch (\Exception $e) {
            Log::error("Error en SendGridService::sendPasswordRecoveryOTP: " . $e->getMessage());
            throw new \Exception('No se pudo enviar el correo de recuperación: ' . $e->getMessage());
        }
    }

    /**
     * Template HTML para email de activación
     */
    protected function getActivationEmailTemplate(string $otpCode): string
    {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='utf-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #4F46E5; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
                .content { background-color: #f9fafb; padding: 30px; border-radius: 0 0 5px 5px; }
                .otp-code { font-size: 32px; font-weight: bold; text-align: center; color: #4F46E5; padding: 20px; background-color: white; border-radius: 5px; margin: 20px 0; letter-spacing: 5px; }
                .footer { text-align: center; margin-top: 20px; color: #6b7280; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>Bienvenido a Módulo Usuario</h2>
                </div>
                <div class='content'>
                    <p>Gracias por registrarte. Tu código de verificación es:</p>
                    <div class='otp-code'>{$otpCode}</div>
                    <p>Este código expira en 10 minutos.</p>
                    <p>Si no solicitaste este registro, ignora este mensaje.</p>
                </div>
                <div class='footer'>
                    <p>Este es un correo automático, por favor no respondas.</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }

    /**
     * Template HTML para email de recuperación de contraseña
     */
    protected function getPasswordRecoveryEmailTemplate(string $otpCode): string
    {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='utf-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #DC2626; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
                .content { background-color: #f9fafb; padding: 30px; border-radius: 0 0 5px 5px; }
                .otp-code { font-size: 32px; font-weight: bold; text-align: center; color: #DC2626; padding: 20px; background-color: white; border-radius: 5px; margin: 20px 0; letter-spacing: 5px; }
                .footer { text-align: center; margin-top: 20px; color: #6b7280; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>Recuperación de contraseña</h2>
                </div>
                <div class='content'>
                    <p>Hemos recibido una solicitud para restablecer tu contraseña.</p>
                    <p>Tu código de verificación es:</p>
                    <div class='otp-code'>{$otpCode}</div>
                    <p>Este código expira en 10 minutos.</p>
                    <p>Si no solicitaste este cambio, ignora este mensaje.</p>
                </div>
                <div class='footer'>
                    <p>Este es un correo automático, por favor no respondas.</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }
}

