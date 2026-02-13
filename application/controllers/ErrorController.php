<?php

class ErrorController extends Zend_Controller_Action
{

    public function errorAction()
    {

      $layout = $this->_helper->layout();
      $layout->setLayout('no-layout');


	$arrHost = explode("/",$_SERVER['REQUEST_URI']);

	if($arrHost[1] == "id_veiculo"){

		$this->_helper->redirector->gotoUrl("/carros-usados/veiculo/id/".$arrHost[2]);

	}



       $errors = $this->_getParam('error_handler');

        if (!$errors || !$errors instanceof ArrayObject) {
            $this->view->message = 'You have reached the error page';
            return;
        }

        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
                $priority = Zend_Log::NOTICE;
                //$this->view->message = 'Page not found';
                $this->view->message = '404';
                break;
            default:
                // application error
                $this->getResponse()->setHttpResponseCode(500);
                $priority = Zend_Log::CRIT;
                $this->view->message = '500';
                break;
        }

        // Log exception, if logger available
        if ($log = $this->getLog()) {
            $log->log($this->view->message, $priority, $errors->exception);
            $log->log('Request Parameters', $priority, $errors->request->getParams());
        }

        // conditionally display exceptions
        if ($this->getInvokeArg('displayExceptions') == true) {
            $this->view->exception = $errors->exception;
        }

        $this->view->request = $errors->request;

        // Passa dados do erro para a view (para o botão reportar)
        if ($errors->exception) {
            $this->view->errorData = base64_encode(json_encode([
                'message' => $errors->exception->getMessage(),
                'file' => $errors->exception->getFile(),
                'line' => $errors->exception->getLine(),
                'trace' => $errors->exception->getTraceAsString(),
                'url' => $_SERVER['REQUEST_URI'],
                'params' => $errors->request->getParams(),
                'date' => date('d/m/Y H:i:s'),
                'user' => isset($_SESSION['sessionUser']['nome']) ? $_SESSION['sessionUser']['nome'] : 'Não logado',
                'empresa' => isset($_SESSION['sessionUser']['nome_fantasia']) ? $_SESSION['sessionUser']['nome_fantasia'] : ''
            ]));
        }
    }

    public function reportAction()
    {
        $layout = $this->_helper->layout();
        $layout->setLayout('no-layout');

        $this->_helper->viewRenderer->setNoRender(true);

        header('Content-Type: application/json; charset=UTF-8');

        if (!$this->getRequest()->isPost()) {
            echo json_encode(['success' => false, 'msg' => 'Método inválido']);
            return;
        }

        $errorDataB64 = $this->_getParam('error_data');
        $observacao = $this->_getParam('observacao', '');

        if (!$errorDataB64) {
            echo json_encode(['success' => false, 'msg' => 'Dados do erro não encontrados']);
            return;
        }

        $errorData = json_decode(base64_decode($errorDataB64), true);

        if (!$errorData) {
            echo json_encode(['success' => false, 'msg' => 'Erro ao decodificar dados']);
            return;
        }

        try {
            $options = $this->getInvokeArg('bootstrap')->getOptions();
            $emailDestino = isset($options['erro']['email']) ? $options['erro']['email'] : 'gesiel.diniz@gmail.com';

            $corpo = '<html><body style="font-family: Arial, sans-serif;">';
            $corpo .= '<h2 style="color: #dc3545;">Erro no Sistema Meu Car</h2>';
            $corpo .= '<table border="0" cellpadding="8" cellspacing="0" style="border-collapse: collapse; width: 100%;">';
            $corpo .= '<tr style="background: #f8f9fa;"><td><strong>Data/Hora:</strong></td><td>' . htmlspecialchars($errorData['date']) . '</td></tr>';
            $corpo .= '<tr><td><strong>Usuário:</strong></td><td>' . htmlspecialchars($errorData['user']) . '</td></tr>';
            $corpo .= '<tr style="background: #f8f9fa;"><td><strong>Empresa:</strong></td><td>' . htmlspecialchars($errorData['empresa']) . '</td></tr>';
            $corpo .= '<tr><td><strong>URL:</strong></td><td>' . htmlspecialchars($errorData['url']) . '</td></tr>';
            $corpo .= '<tr style="background: #f8f9fa;"><td><strong>Mensagem:</strong></td><td style="color: #dc3545;">' . htmlspecialchars($errorData['message']) . '</td></tr>';
            $corpo .= '<tr><td><strong>Arquivo:</strong></td><td>' . htmlspecialchars($errorData['file']) . ':' . $errorData['line'] . '</td></tr>';

            if ($observacao) {
                $corpo .= '<tr style="background: #fff3cd;"><td><strong>Observação do usuário:</strong></td><td>' . htmlspecialchars($observacao) . '</td></tr>';
            }

            $corpo .= '</table>';
            $corpo .= '<h3>Parâmetros:</h3>';
            $corpo .= '<pre style="background: #f8f9fa; padding: 10px; border: 1px solid #dee2e6; font-size: 12px;">' . htmlspecialchars(print_r($errorData['params'], true)) . '</pre>';
            $corpo .= '<h3>Stack Trace:</h3>';
            $corpo .= '<pre style="background: #f8f9fa; padding: 10px; border: 1px solid #dee2e6; font-size: 11px; word-wrap: break-word; white-space: pre-wrap;">' . htmlspecialchars($errorData['trace']) . '</pre>';
            $corpo .= '</body></html>';

            $assunto = '[ERRO] Meu Car - ' . substr($errorData['message'], 0, 80);

            $result = $this->enviaMailerCentralizado($emailDestino, $assunto, $corpo);

            if ($result) {
                echo json_encode(['success' => true, 'msg' => 'Erro reportado com sucesso!']);
            } else {
                echo json_encode(['success' => false, 'msg' => 'Falha ao enviar pelo serviço centralizado']);
            }

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'msg' => 'Falha ao enviar: ' . $e->getMessage()]);
        }
    }

    private function enviaMailerCentralizado($destinatario, $assunto, $corpo)
    {
        $url  = 'https://meucar.com.br/controllers/envia-email-lojistas.php';
        $data = [
            'hash' => md5(@date("Y-m-d H:i") . "envia_centralizado"),
            'destinatario' => $destinatario,
            'assunto' => $assunto,
            'corpo' => $corpo,
            'emailResp' => 'sistemameucar@sistemameucar.com.br',
            'nomeSite' => 'Meu Car - Sistema',
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $success = !curl_errno($ch) && $response !== false;
        curl_close($ch);

        return $success;
    }

    public function getLog()
    {
        $bootstrap = $this->getInvokeArg('bootstrap');
        if (!$bootstrap->hasResource('Log')) {
            return false;
        }
        $log = $bootstrap->getResource('Log');
        return $log;
    }


}
