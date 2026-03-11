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

        // Envia erro automaticamente para o Telegram
        if ($errors->exception && $errors->type == Zend_Controller_Plugin_ErrorHandler::EXCEPTION_OTHER) {
            try {
                require_once 'Classes/TelegramAPI.php';

                $msg  = "🚨 <b>ERRO - Sistema Meu Car</b>\n\n";
                $msg .= "📅 <b>Data/Hora:</b> " . date('d/m/Y H:i:s') . "\n";
                $msg .= "👤 <b>Usuário:</b> " . (isset($_SESSION['sessionUser']['nome']) ? $_SESSION['sessionUser']['nome'] : 'Não logado') . "\n";
                $msg .= "🏢 <b>Empresa:</b> " . (isset($_SESSION['sessionUser']['nome_fantasia']) ? $_SESSION['sessionUser']['nome_fantasia'] : '') . "\n";
                $msg .= "🔗 <b>URL:</b> " . $_SERVER['REQUEST_URI'] . "\n";
                $msg .= "❌ <b>Mensagem:</b> " . $errors->exception->getMessage() . "\n";
                $msg .= "📄 <b>Arquivo:</b> " . basename($errors->exception->getFile()) . ":" . $errors->exception->getLine() . "\n";

                $traceLinhas = array_slice(explode("\n", $errors->exception->getTraceAsString()), 0, 8);
                $msg .= "\n<b>Stack Trace:</b>\n<pre>" . implode("\n", $traceLinhas) . "</pre>";

                TelegramAPI::send($msg, true);
            } catch (Exception $e) {
                error_log("TelegramAPI: " . $e->getMessage());
            }
        }

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
            require_once 'Classes/TelegramAPI.php';

            $msg  = "🚨 <b>ERRO - Sistema Meu Car</b>\n\n";
            $msg .= "📅 <b>Data/Hora:</b> " . $errorData['date'] . "\n";
            $msg .= "👤 <b>Usuário:</b> " . $errorData['user'] . "\n";
            $msg .= "🏢 <b>Empresa:</b> " . $errorData['empresa'] . "\n";
            $msg .= "🔗 <b>URL:</b> " . $errorData['url'] . "\n";
            $msg .= "❌ <b>Mensagem:</b> " . $errorData['message'] . "\n";
            $msg .= "📄 <b>Arquivo:</b> " . basename($errorData['file']) . ":" . $errorData['line'] . "\n";

            if ($observacao) {
                $msg .= "💬 <b>Observação:</b> " . $observacao . "\n";
            }

            $traceLinhas = array_slice(explode("\n", $errorData['trace']), 0, 8);
            $msg .= "\n<b>Stack Trace:</b>\n<pre>" . implode("\n", $traceLinhas) . "</pre>";

            $result = TelegramAPI::send($msg, false);

            if ($result) {
                echo json_encode(['success' => true, 'msg' => 'Erro reportado com sucesso!']);
            } else {
                echo json_encode(['success' => false, 'msg' => 'Falha ao enviar pelo Telegram']);
            }

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'msg' => 'Falha ao enviar: ' . $e->getMessage()]);
        }
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
