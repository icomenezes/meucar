<?php

class Internas_MailConfig
{
    const CONTA_SISTEMA  = 'sistema';
    const CONTA_CONTATO  = 'contato';
    const CONTA_PROPOSTA = 'proposta';
    const CONTA_SUPORTE  = 'suporte';
    const CONTA_ADMIN    = 'admin';

    private static function env($key, $default = '')
    {
        $v = getenv($key);
        if ($v !== false && $v !== '') return $v;
        return isset($_ENV[$key]) && $_ENV[$key] !== '' ? $_ENV[$key] : $default;
    }

    public static function getTransport($conta = self::CONTA_SISTEMA)
    {
        $config = self::getConfig($conta);
        $ssl = self::env('SMTP_SSL', 'ssl');
        return new Zend_Mail_Transport_Smtp($config['host'], [
            'auth'     => 'login',
            'username' => $config['username'],
            'password' => $config['password'],
            'port'     => $config['port'],
            'ssl'      => $ssl,
        ]);
    }

    public static function getFrom($conta = self::CONTA_SISTEMA)
    {
        return self::getConfig($conta)['username'];
    }

    private static function getConfig($conta)
    {
        $host = self::env('SMTP_HOST', 'mail.sistemameucar.com.br');
        $port = self::env('SMTP_PORT', '465');

        $map = [
            self::CONTA_SISTEMA  => ['SMTP_USER_SISTEMA',  'SMTP_PASS_SISTEMA',  $host, $port],
            self::CONTA_CONTATO  => ['SMTP_USER_CONTATO',  'SMTP_PASS_CONTATO',  $host, $port],
            self::CONTA_PROPOSTA => ['SMTP_USER_PROPOSTA', 'SMTP_PASS_PROPOSTA', $host, $port],
            self::CONTA_SUPORTE  => ['SMTP_USER_SUPORTE',  'SMTP_PASS_SUPORTE',  $host, $port],
            self::CONTA_ADMIN    => ['SMTP_USER_ADMIN',    'SMTP_PASS_ADMIN',    self::env('SMTP_HOST_ADMIN', $host), $port],
        ];

        if (!isset($map[$conta])) {
            $conta = self::CONTA_SISTEMA;
        }

        [$userEnv, $passEnv, $smtpHost, $smtpPort] = $map[$conta];

        return [
            'host'     => $smtpHost,
            'username' => self::env($userEnv),
            'password' => self::env($passEnv),
            'port'     => $smtpPort,
        ];
    }

    public static function getEmailSuporte()
    {
        return self::env('EMAIL_SUPORTE', 'suporte@sistemameucar.com.br');
    }

    public static function getEmailNotifLojista()
    {
        return self::env('EMAIL_NOTIF_LOJISTA', 'guilherme@selectveiculos.com.br');
    }

    public static function getEmailBccLojista()
    {
        return self::env('EMAIL_BCC_LOJISTA', 'guilherme@sistemameucar.com.br');
    }

    public static function getEmailErro()
    {
        return self::env('EMAIL_ERRO', 'icomenezes@hotmail.com');
    }
}
