<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_WARNING);
ini_set('display_errors', 0);

// Carrega variáveis do .env (sem dependência externa)
$_envFile = realpath(dirname(__FILE__) . '/../.env');
if ($_envFile && is_readable($_envFile)) {
    foreach (file($_envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $_line) {
        if (strpos(trim($_line), '#') === 0) continue;
        if (strpos($_line, '=') === false) continue;
        [$_key, $_val] = explode('=', $_line, 2);
        $_key = trim($_key);
        $_val = trim($_val);
        if ($_key !== '') {
            putenv("$_key=$_val");
            $_ENV[$_key] = $_val;
            $_SERVER[$_key] = $_val;
        }
    }
    unset($_envFile, $_line, $_key, $_val);
}
// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));
// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development'));
// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));
/** Zend_Application */
require_once 'Zend/Application.php';
// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);
define('URL', 'https://sistemameucar.com.br/');
//define('URL', 'http://meucar.local/');
//define('URL', 'http://meucarsistemapro1.hospedagemdesites.ws/');
//define('EMAIL_DESTINO','icomenezes@hotmail.com');
//define('EMAIL_DESTINO','rogerio@b1t.com.br');
define('SUPERVISOR',9);
define('AVALIADOR',8);
define('SECRETARIA',7);
define('ADMINISTRATIVO',6);
define('FUNCIONARIO',5);
define('GERENTE',4);
define('VENDEDOR',3);
define('CONCESSIONARIO',2);
define('ADMINISTRADOR',1);
// define('HOST','meucardedicado.vpshost1432.mysql.dbaas.com.br');
// define('USER','meucardedicado');
// define('PASS','G010502_m3uc4r');
// define('DB','meucardedicado');
//define('HOST','186.202.188.243');
//define('USER','meucar_dedicado');
//define('PASS','m3uc4r');
//define('DB','meucar_dedicado');
define('HOST','localhost');
define('USER','meucar');
define('PASS','LhF75SypNTLdedpt');
define('DB','meucar'); // nome do banco local
$application->bootstrap()
            ->run();