<?php

class ContatoController extends Zend_Controller_Action {
    
    public function init(){
        
        $this->view->translate          = Zend_Registry::get('Zend_Translate')->setLocale( Zend_Registry::get('Zend_Locale') );
        $this->view->baseUrl            = $this->getRequest()->getBaseUrl(); //setando o base Url
        $this->view->controller         = $this->getRequest()->getControllerName(); //setando o nome do controller
        $this->view->action             = $this->getRequest()->getActionName(); //setando o nome do controller
        $this->view->cliente            = Zend_Auth::getInstance()->setStorage(new Zend_Auth_Storage_Session('Cliente'))->getIdentity();
        $this->view->user               = Zend_Auth::getInstance()->setStorage(new Zend_Auth_Storage_Session('Administrador'))->getIdentity(); 
        $this->view->usuario            = Zend_Auth::getInstance()->setStorage(new Zend_Auth_Storage_Session('Usuario'))->getIdentity();
        
        /*
        //Recuperando os usuários online
        //Zend_Loader::loadClass('UsuariosOnline');
        $UsuariosOnline = new UsuariosOnline();
        $UsuariosOnline->verificaLocalidade();
        */
        
    }
    
    /*
    public function indexAction(){
        if ($this->getRequest()->isPost()) {
            $str_nome       = trim($this->_request->getPost('str_nome'));
            $str_email      = trim($this->_request->getPost('str_email'));
            $str_assunto    = trim($this->_request->getPost('str_assunto'));
            $str_telefone   = trim($this->_request->getPost('str_telefone'));
            $txt_mensagem   = trim($this->_request->getPost('txt_mensagem'));
            if ($str_nome != '' && $str_email != '' && $str_assunto != "" && $txt_mensagem != "") {
                
                $str_via = "";
                $str_xfor = "";
                $str_agente = "";
                if(isset($_SERVER['HTTP_VIA']))                 $str_via        = $_SERVER['HTTP_VIA'];
                if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))     $str_xfor       = $_SERVER['HTTP_X_FORWARDED_FOR'];
                if(isset($_SERVER['HTTP_USER_AGENT']))          $str_agente     = $_SERVER['HTTP_USER_AGENT'];
        
                $data = array(
                    'str_nome'      => $str_nome,
                    'str_email'     => $str_email,
                    'str_assunto'   => $str_assunto,
                    'str_telefone'  => $str_telefone,
                    'txt_mensagem'  => $txt_mensagem,
                    'dte'           => date('Y-m-d H:i:s'),
                    'str_ip'        => $_SERVER['REMOTE_ADDR'],
                    'str_via'       => $str_via,
                    'str_xfor'      => $str_xfor,
                    'str_agente'    => $str_agente,
                    );
                $Contato = new Contato();
                $Contato->insert($data);
                
                //enviando o e-mail de contato
                //Zend_Loader::loadClass('Zend_Mail');
                //Zend_Loader::loadClass('Zend_Mail_Transport_Smtp');
                $tr = new Zend_Mail_Transport_Smtp('localhost');
                Zend_Mail::setDefaultTransport($tr);
                
                $mail = new Zend_Mail( "UTF-8" );
                $bodyEmail = "<ol>
                                  <li>Nome: $str_nome</li>
                                  <li>E-mail: $str_email</li>
                                  " . (($str_telefone != "")?"<li>Telefone: $str_telefone</li>":"")."
                                  <li>Assunto: $str_assunto</li>
                                  <li>Mensagem: " . nl2br($txt_mensagem) . "</li>
                              </ol>";
                $mail->setBodyHtml($bodyEmail);
                $mail->setFrom($str_email, $str_nome);
                $mail->addTo('contato@achagora.com.br', Zend_Controller_Front::getInstance()->getParam('EMPRESA_ID'));
                //$mail->addBcc(Zend_Controller_Front::getInstance()->getParam('EMPRESA_EMAIL_MINITORAMENTO'), Zend_Controller_Front::getInstance()->getParam('EMPRESA_ID'));
                $mail->setSubject("[" . Zend_Controller_Front::getInstance()->getParam('EMPRESA_ID') . "] Contato - " . $str_assunto);
                $mail->send();
                
                $str_retorno =  "<strong>Seu e-mail foi enviado com sucesso.</strong>";
            }else{
                $str_retorno = "<strong>Todos os dados são Obrigatórios</strong>";
            }
            echo $str_retorno;
            die();
        }else{
            $this->view->titulo             = "Contato";
            $this->view->descricao          = "Aqui você pode entrar em contato conosco para tirar as súas dúvidas ou fazer sugestões.";
            $this->view->palavrasChave      = "contato, guia online, cadastro";
            
        }
    }
    */
    
    
        public function indexAction(){
            
            $this->view->bln_single = true;
            $this->view->titulo = $this->view->translate( 'Fale com o GuiaJá' );
            
            $this->view->migalha = array();
            /*
            $this->view->migalha[] = array(
                                                'str_nome' => $this->view->translate->_('Página Inicial'),
                                                'str_url' => '/'
                                            );
                                            */
            $this->view->migalha[] = array(
                                                'str_nome' => $this->view->translate->_('Contato'),
                                                'str_url' => '/contato'
                                            );
            
            /*//Zend_Loader::loadClass('Zend_Form');
            //Zend_Loader::loadClass('Zend_Form_Element_Text');
            //Zend_Loader::loadClass('Zend_Form_Element_Select');
            //Zend_Loader::loadClass('Zend_Form_Element_Textarea');
            //Zend_Loader::loadClass('Zend_Form_Element_Submit');
            //Zend_Loader::loadClass('Zend_Form_Element_Captcha');
            //Zend_Loader::loadClass('Vold_Form');
            ////Zend_Loader::loadClass('Vold_Form_ContatoForm');
            include_once( "Vold/Form/ContatoForm.php" );
            
            $form_contato = new ContatoForm();
            $this->view->form = $form_contato;
            
            $rr_retorno = array();
            */
            if ($this->_request->isPost()) {
                
                $formData = $this->_request->getPost();
                $str_retorno = false;
                
                if ($formData['str_nome'] == "") {
                    $rr_retorno['str_nome'] = '<span class="error_indique">' . $this->view->translate->_( "Nome é obrigatório" ) . '</span>';
                    $str_retorno = true;
                }

                if (!isEmailValid($formData['str_email'])) {
                    $rr_retorno['str_email'] = '<span class="error_indique">' . $this->view->translate->_( "E-mail deve ser válido" ) . '</span>';
                    $str_retorno = true;
                }
                if ($formData['str_assunto'] == "") {
                    $rr_retorno['str_assunto'] = '<span class="error_indique">' . $this->view->translate->_( "Assunto é obrigatório") . '</span>';
                    $str_retorno = true;
                }
                if ($formData['txt_mensagem'] == "" || stripos( $formData['txt_mensagem'], '[url=' ) ) {
                    $rr_retorno['txt_mensagem'] = '<span class="error_indique">' . $this->view->translate->_( "Mensagem é obrigatória") . '</span>';
                    $str_retorno = true;
                }

                if ( !isset( $_SESSION['str_captcha'] ) || $formData['str_captcha'] == "" || ( isset( $_SESSION['str_captcha'] ) && $formData['str_captcha'] != $_SESSION['str_captcha'] ) ) {
                    $rr_retorno['str_captcha'] = '<span class="error_indique">' . $this->view->translate->_( "Verifique o código de verificação" ) . '</span>';
                    $str_retorno = true;
                }

                if ( $str_retorno == false ) {
                    
                    $str_via = "";
                    $str_xfor = "";
                    $str_agente = "";
                    if(isset($_SERVER['HTTP_VIA']))                 $str_via        = $_SERVER['HTTP_VIA'];
                    if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))     $str_xfor       = $_SERVER['HTTP_X_FORWARDED_FOR'];
                    if(isset($_SERVER['HTTP_USER_AGENT']))          $str_agente     = $_SERVER['HTTP_USER_AGENT'];
                    $data = array(
                        'str_nome'         => $formData['str_nome'],
                        'str_email'        => $formData['str_email'],
                        'str_assunto'      => $formData['str_assunto'],
                        'str_telefone'     => $formData['str_telefone'],
                        'str_nome_empresa' => $formData['str_nome_empresa'],
                        'txt_mensagem'     => $formData['txt_mensagem'],
                        'dte'              => date('Y-m-d H:i:s'),
                        'str_ip'           => $_SERVER['REMOTE_ADDR'],
                        'str_via'          => $str_via,
                        'str_xfor'         => $str_xfor,
                        'str_agente'       => $str_agente
                        );
                    
                    $Contato = new Contato();
                    $Contato->insert($data);
                    
                    $mail = getMailler();
                    $mail->From = Zend_Controller_Front::getInstance()->getParam('EMPRESA_EMAIL');
                    $mail->FromName = Zend_Controller_Front::getInstance()->getParam('EMPRESA_REMETENTE');
                    $mail->AddReplyTo($formData['str_email'],$formData['str_nome']);

                    if ( defined('SMTP_HOST') ) {
                        $mail->SMTPAuth   = true; // enable SMTP authentication
                        $mail->Host       = SMTP_HOST; // sets the SMTP server
                        $mail->Port       = SMTP_PORT;                    // set the SMTP port for the GMAIL server
                        $mail->Username   = SMTP_USER; // SMTP account username
                        $mail->Password   = SMTP_PASSWORD;
                    }
                    
                    $mail->Subject = Zend_Controller_Front::getInstance()->getParam('EMPRESA_REMETENTE') . " " . montaTituloEmail(sprintf($this->view->translate( "Contato - %s" ), $formData['str_assunto'] ) );
            
                    /*$bodyEmail = "<ol>
                                      <li><strong>Nome:</strong> " . $formData['str_nome'] . "</li>
                                      <li><strong>E-mail:</strong> " . $formData['str_email'] . "</li>
                                      " . (($formData['str_telefone'] != "")?"<li><strong>Telefone:</strong> " . $formData['str_telefone'] . "</li>":"")."
                                      " . (($formData['str_nome_empresa'] != "")?"<li><strong>Nome da Empresa:</strong> " . $formData['str_nome_empresa'] . "</li>":"")."
                                      <li><strong>Assunto:</strong> " . $formData['str_assunto'] . "</li>
                                      <li><strong>Mensagem:</strong> " . nl2br($formData['txt_mensagem']) . "</li>
                                  </ol>";*/

                    $mail->Body = monta_email(sprintf($this->view->translate("<ol><li><strong>Nome:</strong> %s</li><li><strong>E-mail:</strong> %s</li><li><strong>Telefone:</strong> %s</li><li><strong>Nome da Empresa:</strong> %s</li><li><strong>Assunto:</strong> %s</li><li><strong>Mensagem:</strong> %s</li></ol>"), $formData['str_nome'], $formData['str_email'], $formData['str_telefone'], $formData['str_nome_empresa'], $formData['str_assunto'], nl2br($formData['txt_mensagem'])), $mail->Subject);
                    
                    /*
                    if ( $_SERVER['REMOTE_ADDR'] == '191.255.74.14' ) {
                        echo"<pre>".__FILE__.":".__LINE__."\n";print_r($mail->Body);echo"</pre>";
                        die;
                    }
                    */
                    
                    $mail->AddAddress(Zend_Controller_Front::getInstance()->getParam('EMPRESA_EMAIL'), Zend_Controller_Front::getInstance()->getParam('EMPRESA_REMETENTE'));
                    $mail->addBcc(Zend_Controller_Front::getInstance()->getParam('EMPRESA_EMAIL_MINITORAMENTO'), Zend_Controller_Front::getInstance()->getParam('EMPRESA_REMETENTE'));
                    //$mail->addBcc(Zend_Controller_Front::getInstance()->getParam('anderson@rhizatech.com.br'), Zend_Controller_Front::getInstance()->getParam('EMPRESA_REMETENTE'));
                    
                    if ( !$mail->Send() ) {
                        $rr_retorno[] = '<span class="error_indique">' . $this->view->translate( "Não foi possível enviar e-mail, tente novamente mais tarde." ) . '</span>';
                    } else {
	                    /*$rr_retorno[] =  '<span class="sucesso_indique">' . $this->view->translate( "Seu e-mail foi enviado com sucesso." ) . '</span>';
	                    $this->view->retorno_contato = $rr_retorno;*/
	                    $this->_redirect('/contato/sucesso');
	                    return;
                    }
                    
                }
                $this->view->retorno_form = $formData;
                $this->view->retorno_contato = $rr_retorno;
            }
        }
        
        public function sucessoAction(){
            $this->view->titulo = $this->view->translate( 'Contato efetuado com sucesso' );
        }
        
}
