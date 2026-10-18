<?php

if (!function_exists('nfs_email_cuerpo')) {
    /**
     * Envuelve el texto del mensaje en un HTML simple para el correo.
     */
    function nfs_email_cuerpo(string $mensaje): string
    {
        $html = nl2br(htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8'));

        return '<div style="font-family:Arial,Helvetica,sans-serif;font-size:15px;color:#222;line-height:1.55;">'
            . $html
            . '<br><br>'
            . '<hr style="border:none;border-top:1px solid #dddddd;margin:18px 0;">'
            . '<p style="color:#888888;font-size:13px;margin:0;">New Fitness Systems</p>'
            . '</div>';
    }
}

if (!function_exists('nfs_email_configurado')) {
    /**
     * Indica si la configuracion SMTP esta cargada en el .env.
     */
    function nfs_email_configurado(): bool
    {
        $config = config('Email');

        return !empty($config->SMTPUser)
            && !empty($config->SMTPPass)
            && !empty($config->fromEmail);
    }
}

if (!function_exists('nfs_completar_plantilla')) {
    /**
     * Reemplaza los marcadores {clave} de un mensaje por sus valores.
     *
     * @param array<string, string> $datos
     */
    function nfs_completar_plantilla(string $mensaje, array $datos): string
    {
        foreach ($datos as $clave => $valor) {
            $mensaje = str_replace('{' . $clave . '}', (string) $valor, $mensaje);
        }

        return $mensaje;
    }
}

if (!function_exists('nfs_enviar_email')) {
    /**
     * Envia un correo. Devuelve ['ok' => bool, 'error' => string].
     *
     * @return array{ok:bool, error:string}
     */
    function nfs_enviar_email(string $para, string $asunto, string $mensaje): array
    {
        if (!nfs_email_configurado()) {
            return ['ok' => false, 'error' => 'Falta configurar el correo (SMTP) en el archivo .env.'];
        }

        if (!filter_var($para, FILTER_VALIDATE_EMAIL)) {
            return ['ok' => false, 'error' => 'La direccion de correo no es valida.'];
        }

        $email = \Config\Services::email();
        $email->clear(true);
        $email->setTo($para);
        $email->setSubject($asunto);
        $email->setMessage(nfs_email_cuerpo($mensaje));

        if (!$email->send(false)) {
            return ['ok' => false, 'error' => strip_tags($email->printDebugger(['headers']))];
        }

        return ['ok' => true, 'error' => ''];
    }
}
