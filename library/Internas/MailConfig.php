<?php

class Internas_MailConfig
{
    const CONTA_SISTEMA  = 'sistema';
    const CONTA_CONTATO  = 'contato';
    const CONTA_PROPOSTA = 'proposta';
    const CONTA_SUPORTE  = 'suporte';
    const CONTA_ADMIN    = 'admin';

    private static function cfg($key, $default = '')
    {
        try {
            $config = Zend_Registry::get('config');
            $parts  = explode('.', $key);
            $value  = $config;
            foreach ($parts as $part) {
                if (!isset($value->$part)) return $default;
                $value = $value->$part;
            }
            return (string) $value ?: $default;
        } catch (Exception $e) {
            return $default;
        }
    }

    public static function getTransport($conta = self::CONTA_SISTEMA)
    {
        $config = self::getConfig($conta);
        return new Zend_Mail_Transport_Smtp($config['host'], [
            'auth'     => 'login',
            'username' => $config['username'],
            'password' => $config['password'],
            'port'     => $config['port'],
            'ssl'      => $config['ssl'],
        ]);
    }

    public static function getFrom($conta = self::CONTA_SISTEMA)
    {
        return self::getConfig($conta)['username'];
    }

    private static function getConfig($conta)
    {
        $host = self::cfg('smtp.host', 'mail.sistemameucar.com.br');
        $port = self::cfg('smtp.port', '465');
        $ssl  = self::cfg('smtp.ssl',  'ssl');

        $map = [
            self::CONTA_SISTEMA  => ['smtp.sistema.user',  'smtp.sistema.pass'],
            self::CONTA_CONTATO  => ['smtp.contato.user',  'smtp.contato.pass'],
            self::CONTA_PROPOSTA => ['smtp.proposta.user', 'smtp.proposta.pass'],
            self::CONTA_SUPORTE  => ['smtp.suporte.user',  'smtp.suporte.pass'],
            self::CONTA_ADMIN    => ['smtp.admin.user',    'smtp.admin.pass'],
        ];

        if (!isset($map[$conta])) {
            $conta = self::CONTA_SISTEMA;
        }

        [$userKey, $passKey] = $map[$conta];

        return [
            'host'     => $host,
            'username' => self::cfg($userKey),
            'password' => self::cfg($passKey),
            'port'     => $port,
            'ssl'      => $ssl,
        ];
    }

    public static function getEmailSuporte()
    {
        return self::cfg('email.suporte', 'suporte@sistemameucar.com.br');
    }

    public static function getEmailNotifLojista()
    {
        return self::cfg('email.notif_lojista', 'guilherme@selectveiculos.com.br');
    }

    public static function getEmailBccLojista()
    {
        return self::cfg('email.bcc_lojista', 'guilherme@sistemameucar.com.br');
    }

    public static function getEmailErro()
    {
        return self::cfg('email.erro', 'icomenezes@hotmail.com');
    }
}
