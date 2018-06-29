<?php

class IndexController extends Zend_Controller_Action {
    
    public function init(){
        
        $this->view->translate = Zend_Registry::get('Zend_Translate')->setLocale( Zend_Registry::get('Zend_Locale') );
        $this->view->baseUrl = $this->getRequest()->getBaseUrl(); //setando o base Url
        $this->view->controller = $this->getRequest()->getControllerName(); //setando o nome do controller
        $this->view->action = $this->getRequest()->getActionName(); //setando o nome do controller
        $this->view->cliente = Zend_Auth::getInstance()->setStorage(new Zend_Auth_Storage_Session('Cliente'))->getIdentity();
        $this->view->user = Zend_Auth::getInstance()->setStorage(new Zend_Auth_Storage_Session('Administrador'))->getIdentity();
        $this->view->usuario = Zend_Auth::getInstance()->setStorage(new Zend_Auth_Storage_Session('Usuario'))->getIdentity();
        
        $this->view->str_body_id = "index";
        
        /*
        //Recuperando os usuÃ¡rios online
        //Zend_Loader::loadClass('UsuariosOnline');
        $UsuariosOnline = new UsuariosOnline();
        $UsuariosOnline->verificaLocalidade();
        */
        
        //$this->view->bln_sem_publicidade = true;
        
    }
    
    public function indexAction(){
        $this->view->bln_single = true;

        $this->view->bln_index = !(isset( $_SERVER['REQUEST_URI'] ) && strstr( $_SERVER['REQUEST_URI'], LINK_GUIA ));
        
        //$this->view->bln_single = true;
        
        /*
        if ( !isset($_SESSION['num_cidade_id']) || !isset($_SESSION['num_estado_id']) ) {
            
            if ( !IS_EN ) {
                $num_cidade_id = 9422;
                $num_estado_id = 26;
            } else {
                $num_cidade_id = 18848;
                $num_estado_id = 35;
            }
            
        } else {
            
            $num_cidade_id = $_SESSION['num_cidade_id'];
            $num_estado_id = $_SESSION['num_estado_id'];
            
        }
        */
        
        $str_slug = $this->_getParam('str_slug');
        
        $str_cidade_url = $this->_getParam("str_cidade_url");
        $str_estado_url = $this->_getParam("str_estado_url");
        
        
        $rr_parametros = explodeSlug( $str_slug );
        
        //Zend_Loader::loadClass('Cidade');
        $Estado = new Estado();
        $Cidade = new Cidade();

        $this->view->Estados = $Estado->fetchAll(null, "str_nome");
        
        if ( isset( $rr_parametros['cidade'] ) && $rr_parametros['cidade'] != '' && isset( $rr_parametros['estado'] ) && $rr_parametros['estado'] != '' ) {
            $this->view->Estado = $Estado->fetchRow("str_slug = '" . $rr_parametros['estado'] . "'");
            $this->view->Cidade = $Cidade->fetchRow("str_slug = '" . $rr_parametros['cidade'] . "' AND num_estado_id = '" . $this->view->Estado->num_id . "'");
        } else if ( $str_cidade_url != '' && $str_estado_url != '' ) {
            $this->view->Estado = $Estado->fetchRow("str_url = '" . $str_estado_url . "'");
            $this->view->Cidade = $Cidade->fetchRow("str_url = '" . $str_cidade_url . "' AND num_estado_id = '" . $this->view->Estado->num_id . "'");
        }/*
         else {
            $this->view->Estado = $Estado->fetchRow("num_id = '" . $num_estado_id . "'");
            $this->view->Cidade = $Cidade->fetchRow("num_id = '" . $num_cidade_id . "' AND num_estado_id = '" . $num_estado_id . "'");
        }
        */
        
        if ( ( !$this->view->Estado || !$this->view->Cidade ) && !$this->view->bln_index ) {
        	//header( "HTTP/1.1 301 Moved Permanently" );
            header( "Location: " . SITE_PROTOCOL . "://" . SITE_HOST . "/estados" );
            die;
        }
        
        if ( !$this->view->Cidade || !$this->view->Estado ) {
            //$this->view->titulo = $this->view->translate( "Lista TelefÃ´nica GuiaJÃ¡ | Guia de endereÃ§os" );
            $this->view->titulo = Zend_Controller_Front::getInstance()->getParam('SITE_TITULO');
            $this->view->descricao = $this->view->translate( "Lista telefônica online GuiaJá. Utilize nosso guia de endereços para encontrar telefones e endereços de empresas próximas de você e avaliar a sua satisfação com as empresas cadastradas" );
            
            
            //pegando a cidade e o estado do cookie
            
            if ( isset( $_COOKIE['num_cidade_id'] ) && $_COOKIE['num_cidade_id'] != '' ) {
                $this->view->Cidade = $Cidade->fetchRow("num_id = '" . $_COOKIE['num_cidade_id'] . "'");
                $this->view->Estado = $Estado->fetchRow("num_id = '" . $this->view->Cidade->num_estado_id . "'");
            }
        } 
        
        
        if ( isset( $this->view->Estado ) && isset( $this->view->Cidade ) && getUrl() != '/' . LINK_GUIA . '/' . $this->view->Estado->str_slug . "+" . $this->view->Cidade->str_slug . '.html' ) {
            //Header( "HTTP/1.1 301 Moved Permanently" );
            Header( "Location: " . SITE_PROTOCOL . "://" . SITE_HOST . '/' . LINK_GUIA . '/' . $this->view->Estado->str_slug . "+" . $this->view->Cidade->str_slug . '.html' );
            die;
        }
        
        if ( $this->view->Cidade && $this->view->Estado ) {
            setcookie("num_cidade_id", $this->view->Cidade->num_id, time()+86400 * 365 /* um ano */, "/", "." . SITE_HOST);
            setcookie("num_estado_id", $this->view->Estado->num_id, time()+3600 * 365, "/", "." . SITE_HOST );
        }
        
        /*
        //limpando a session
        unset($_SESSION['num_cidade_id'],$_SESSION['str_cidade_nome'], $_SESSION['str_cidade_url'], $_SESSION['num_estado_id'], $_SESSION['str_estado_nome'], $_SESSION['str_estado_url'], $_SESSION['str_cidade_url'], $_SESSION['str_pais_nome'], $_SESSION['num_yahoo_id'], $_SESSION['str_estado_uf']);
        */
        
        //Deixando a cidade e o estado pre selecionados na pesquisa
        /*
        $this->view->busca_num_estado_id = $num_estado_id;
        $this->view->busca_num_cidade_id = $num_cidade_id;
        */
        //$this->view->rr_cidades = $Cidade->fetchAll("num_estado_id = " . $this->view->busca_num_estado_id . "", 'str_nome');
        
        //Zend_Loader::loadClass('Categoria');
        $Categoria = new Categoria();
        
        /*
        $this->view->categorias_mais_acessadas = $Categoria->retornaMaisVisitadas( $this->view->Cidade->num_id, 14 );
        */
        
        //$this->view->str_previsao_do_tempo_compacta = Cidade::retornaPrevisaoCompacta( $this->view->Cidade->str_nome, $this->view->Estado->str_nome );
        //$this->view->str_previsao_do_tempo_compacta = Cidade::retornaPrevisao( $this->view->Cidade );
        
        //Zend_Loader::loadClass('VWEmpresasPublicadas');
        $VWEmpresasPublicadas = new VWEmpresasPublicadas();
        //$this->view->empresas_cidade = $VWEmpresasPublicadas->getEmpresasByCidade( $this->view->Cidade->num_id, (( $this->view->str_previsao_do_tempo_compacta )? 3 : 6 ) );
        if ( $this->view->Cidade ) {
            //$this->view->empresas_cidade = $VWEmpresasPublicadas->getEmpresasByCidade( $this->view->Cidade->num_id, 9 );
            //$this->view->previsao = Cidade::retornaPrevisaoMinMax($this->view->Cidade);
            
            /*
            if ( isset( $_GET['teste'] ) && $_GET['teste'] == 1 ) {
                echo"<pre>".__FILE__.":".__LINE__."\n";print_r($this->view->topCategoriasIndex);echo"</pre>";
                echo "<h1>";
                echo"<pre>".__FILE__.":".__LINE__."\n";print_r(count($this->view->topCategoriasIndex));echo"</pre>";
                echo "</h1>";
            }
            */
            
            //$this->view->cidades_mais_proximas_trocar = $Cidade->retornaCidadesMaisProximasTrocar(  $this->view->Cidade->num_id, count($this->view->topCategoriasIndex) );

            $this->view->cidades_mais_proximas_trocar = $Cidade->retornaCidadesMaisProximasTrocar(  $this->view->Cidade->num_id, 9 );
            
            $this->view->topCategorias = $Categoria->getTopCategoriasCidade( $this->view->Cidade->num_id, 10 );
            $this->view->topCategorias = $this->view->topCategorias['categorias']; 
            
            if ( count($this->view->topCategoriasIndex) == 0 ) {
                
                $cidades_vizinhas = $Cidade->retornaCidadesVizinhas(  $this->view->Cidade->num_id, 3 );
                
                $rr_cidades = array();
                
                foreach( $cidades_vizinhas as $k => $cidade_vizinha ){
                    
                    $rr_cat_cid = $Categoria->getTopCategoriasCidadeIndex( $cidade_vizinha, 10 );
                    //$rr_cat_cid = $rr_cat_cid['categorias'];
                    
                    if ( !count( $rr_cat_cid ) ) {
                        continue;
                    }
                    
                    $rr_cidades[$cidade_vizinha] = $rr_cat_cid;
                }
                
                $this->view->cidades_vizinhas = $rr_cidades;
            }
            
            
            
            #NOVOOOO
            //selecionando as principais categorias da cidade
            $this->view->principais_categorias = $Categoria->getTopCategoriasCidadeIndex( $this->view->Cidade->num_id, 10 );
            
        }
        
        //Recuperando as cidades mais prÃ³ximas
        //$this->view->cidades_mais_proximas = $Cidade->retornaCidadesMaisProximasGuia(  $this->view->Cidade->num_id, 11 );
        
        
        //$this->view->titulo = $this->view->escape( ucwords(strtolower($this->view->Cidade->str_nome)) ) . ", " . ucwords(strtolower($this->view->Estado->str_nome));
        
        if ( !$this->view->bln_index || isset( $this->view->Cidade ) ) {
            $this->view->titulo = sprintf($this->view->translate( "Guia de endereços de %s" ),$this->view->escape( ucwords(strtolower($this->view->Cidade->str_nome)) ) . " - " . strtoupper($this->view->Estado->str_uf));
            $this->view->titulo_h1 = sprintf($this->view->translate( "Lista Telefônica de %s" ),$this->view->escape( ucwords(strtolower($this->view->Cidade->str_nome)) ) . " - " . strtoupper($this->view->Estado->str_uf));
            $this->view->descricao = sprintf( $this->view->translate( "Lista telefônica online de %s - %s. Utilize nosso guia de endereços para encontrar telefones e endereços de empresas em %s - %s" ), ucwords(strtolower($this->view->Cidade->str_nome)), strtoupper($this->view->Estado->str_uf), ucwords(strtolower($this->view->Cidade->str_nome)), strtoupper($this->view->Estado->str_uf) );
        }
        
        $this->view->palavrasChave = Zend_Controller_Front::getInstance()->getParam('SITE_KEYWORDS');
        $this->view->no_cache = true;
        
        //$this->view->tagCloud = $Categoria->tagCloud( $this->view->Cidade->num_id, 30 );
        $Comentarios = new Comentarios();
        $this->view->lastComents = $Comentarios->lastComents(4);
        
        if ( isset ( $this->view->Cidade->num_id ) && $this->view->Cidade->num_id != 0 ) {
            $_SESSION['num_cidade_id'] = $this->view->Cidade->num_id;
        }

    }
    
}
