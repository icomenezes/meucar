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
        return new Zend_Mail_Transport_Smtp($config['host'], array(
            'auth'     => 'login',
            'username' => $config['username'],
            'password' => $config['password'],
            'port'     => $config['port'],
            'ssl'      => $config['ssl'],
        ));
    }

    /**
     * Verifica rapidamente se o servidor SMTP esta acessivel, com timeout curto.
     *
     * Usado para NUNCA deixar um envio de e-mail travar uma requisicao critica
     * (ex.: login). O Zend usa TIMEOUT_CONNECTION=30s fixo; este pre-teste
     * (default 3s) evita esperar 30s+ quando o SMTP esta fora/lento.
     *
     * @param string $conta
     * @return bool  true se da pra conectar; false se deve pular o envio
     */
    public static function smtpDisponivel($conta = self::CONTA_SISTEMA)
    {
        $config  = self::getConfig($conta);
        $timeout = (int) self::cfg('smtp.connect_timeout', '3');

        $ssl    = strtolower((string) $config['ssl']);
        $prefix = ($ssl === 'ssl') ? 'ssl://' : '';
        $remote = $prefix . $config['host'] . ':' . $config['port'];

        $errno = 0;
        $errstr = '';
        $socket = @stream_socket_client($remote, $errno, $errstr, $timeout);

        if ($socket === false) {
            @error_log('[MailConfig] SMTP indisponivel (' . $remote . '): ' . $errstr);
            return false;
        }

        @fclose($socket);
        return true;
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
