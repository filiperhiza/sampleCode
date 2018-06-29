<?php
    
    function layout_head($iTitulo){ 


        /*
        <!--
        <link rel="stylesheet" href="'.SITE_HTTP_STATIC.'/bootstrap/css/bootstrap.min.css">
        <link rel="stylesheet" href="'.SITE_HTTP_STATIC.'/bootstrap/css/bootstrap-theme.min.css">
        <link rel="stylesheet" href="'.SITE_HTTP_STATIC.'/bootstrap/css/bootstrap-glyphicons.css">
        <link rel="stylesheet" href="'.SITE_HTTP_STATIC.'/bootstrap/css/bootstrap-menu.css">
        <link rel="stylesheet" href="'.SITE_HTTP_STATIC.'/files/css/style.css?a=' . time() . '" type="text/css" media="all" />
        -->
        */

        return '<head lang="pt_BR">
                <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
                    <!--[if lt IE 9]>
                    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
                    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
                    <![endif]-->

                    <title>' . $iTitulo . ' - ' . EMPRESA_NOME . '</title>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
                    <meta http-equiv="x-ua-compatible" content="ie=edge">

                    

                    <link rel="stylesheet" href="'.SITE_HTTP_STATIC.'/startui/css/lib/lobipanel/lobipanel.min.css">
                    <link rel="stylesheet" href="'.SITE_HTTP_STATIC.'/startui/css/separate/vendor/lobipanel.min.css">
                    <link rel="stylesheet" href="'.SITE_HTTP_STATIC.'/startui/css/lib/jqueryui/jquery-ui.min.css">
                    <link rel="stylesheet" href="'.SITE_HTTP_STATIC.'/startui/css/separate/pages/widgets.min.css">
                    <!--
                    <link rel="stylesheet" href="'.SITE_HTTP_STATIC.'/files/css/style.css?a=' . time() . '" type="text/css" media="all" />
                    -->


                    <link rel="stylesheet" href="'.SITE_HTTP_STATIC.'/startui/css/lib/flatpickr/flatpickr.min.css">
                    <link rel="stylesheet" href="'.SITE_HTTP_STATIC.'/startui/css/separate/vendor/flatpickr.min.css">



                    <link rel="stylesheet" href="'.SITE_HTTP_STATIC.'/startui/css/separate/vendor/blockui.min.css">
                    <link rel="stylesheet" href="'.SITE_HTTP_STATIC.'/startui/css/lib/font-awesome/font-awesome.min.css">
                    <link rel="stylesheet" href="'.SITE_HTTP_STATIC.'/startui/css/lib/bootstrap/bootstrap.min.css">
                    
                    <link rel="stylesheet" href="'.SITE_HTTP_STATIC.'/startui/css/lib/datatables-net/datatables.min.css">
                    <link rel="stylesheet" href="'.SITE_HTTP_STATIC.'/startui/css/separate/vendor/datatables-net.min.css">
                    <link rel="stylesheet" href="'.SITE_HTTP_STATIC.'/startui/css/main.css">

                    <link rel="stylesheet" href="'.SITE_HTTP_STATIC.'/files/css/custom.css?a=' . time() . '">

                    <link rel="shortcut icon" href="'.SITE_HTTP_STATIC.'/files/img/favicon.jpg">
                </head>';
    }
    
    function layout_top_login() {
        return '<div id="topo"> 
                    <h1>Bem-vindo ao sistema</h1> 
                </div>';
    }
    
    function layout_body_login( $body ) {
        return $body . layout_rodape();
    }
    
    function layout_top() {
        global $Banco;
        /*
        return '<header id="topo">
                    <div class="navbar">
                        <div id="hide-menu" class="btn-header pull-right">
                            <span class="button_menu"><a href="javascript:void(0);" title="Collapse Menu"><i class="glyphicon glyphicon-th-list"></i></a></span>
                        </div>
                        <div class="navbar-header">
                            <p class="navbar-text navbar-left"><span class="glyphicon glyphicon-user"></span> ' . $_SESSION['NOME'] . " " . $_SESSION['SOBRENOME'] . '</p>
                        </div>
                    </div>
                </header>
                <aside id="left-panel">
                    <nav>
                        ' . monta_menu() . '
                    </nav>
                </aside>';
                */
        $sql = "SELECT
                        Orc.*,
                        Usu.NOME as USUARIO,
                        Cli.NOME AS CLIENTE
                FROM
                        ORCAMENTO AS Orc
                INNER JOIN
                        USUARIO AS Usu ON Usu.ID = Orc.ID_USUARIO
                INNER JOIN
                        CLIENTE AS Cli ON Cli.ID = Orc.ID_CLIENTE
                WHERE
                        Orc.CLIENTE_MASTER_ID = '" . $_SESSION['CLIENTE_MASTER_ID'] . "'
                AND
                        DATE(Orc.DATA_EVENTO) >= DATE(NOW() - INTERVAL 7 DAY)
                AND
                        DATE(Orc.DATA_EVENTO) <= DATE(NOW() + INTERVAL 7 DAY)
                AND
                        Orc.SITUACAO = 'Aprovado'";

        $Banco->query( $sql, "load_numero_pedidos" );


        $iNumeroPedidos = $Banco->getNumRows("load_numero_pedidos");

        $iImagemCliente = '/startui/img/avatar-1-256.png';

        if ( $_SESSION['CLIENTE_MASTER_ID'] != '' && file_exists( $_SERVER['DOCUMENT_ROOT'] . '/files/logo/' . $_SESSION['CLIENTE_MASTER_ID'] . '.jpg' ) ) {
            $iImagemCliente = '/files/logo/' . $_SESSION['CLIENTE_MASTER_ID'] . '.jpg?a=' . time();

        }

        //verificando se ele pode mudar de clinte
        $iHtmlTrocaCM = $iHtmlTrocaPlano = "";
        if ( isset( $_SESSION['PODE_TROCAR'] ) && $_SESSION['PODE_TROCAR'] == 1 ) {
            if( $_SESSION['CLIENTE_MASTER_ID'] == 1 ) {
                $iComboCM = '';

                $sql = "SELECT
                                ID,
                                NOME
                        FROM
                                CLIENTE_MASTER
                                ";
                    
                $Banco->query( $sql, "load" );

                while ( $row = $Banco->getFetchAssoc( "load" ) ) {
                    $iComboCM .= '<a class="dropdown-item" href="/troca_cm.php?id=' . $row['ID'] . '">' . $row['NOME'] . '</a>';
                }

                $iHtmlTrocaCM = '<div class="dropdown mr-3">
                                    <button class="btn btn-rounded dropdown-toggle" id="dd-header-add" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        CM - ' . $_SESSION['CLIENTE_MASTER_NOME'] . '
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dd-header-add">
                                        ' . $iComboCM . '
                                    </div>
                                </div>';

            }
                $iComboPlano = '';

                $sql = "SELECT
                                ID,
                                NOME
                        FROM
                                PLANO
                                ";
                    
                $Banco->query( $sql, "load" );

                while ( $row = $Banco->getFetchAssoc( "load" ) ) {
                    $iComboPlano .= '<a class="dropdown-item" href="/troca_plano.php?id=' . $row['ID'] . '">' . $row['NOME'] . '</a>';
                }

                $iHtmlTrocaPlano = '<div class="dropdown mr-3">
                                        <button class="btn btn-rounded dropdown-toggle" id="dd-header-add" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            Plano - ' . $_SESSION['PLANO_NOME'] . '
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dd-header-add">
                                            ' . $iComboPlano . '
                                        </div>
                                    </div>';


        }

    $iNotificacao = '';


    $sql = "SELECT
                    TIPO,
                    ID_ENVOLVIDO,
                    DATA
            FROM
                    NOTIFICACAO
            WHERE
                    CLIENTE_MASTER_ID = '" . $_SESSION['CLIENTE_MASTER_ID'] . "'
            ORDER BY
                    ID DESC
            LIMIT
                    10";

$Banco->query( $sql, "load" );

$iNumNotificacao = $Banco->getNumRows("load");

while ( $row = $Banco->getFetchAssoc( "load" ) ) {
    $timestamp = strtotime($row['DATA']);
    if ( $row['TIPO'] == 'NOVO_ORÇAMENTO' ) {
        $iPasta = "/orcamento";

        $sql = "SELECT
                        C.NOME,
                        O.NOME_EVENTO
                FROM
                        ORCAMENTO AS O
                INNER JOIN
                        CLIENTE AS C ON O.ID_CLIENTE = C.ID
                WHERE
                        O.ID = '" . $row['ID_ENVOLVIDO'] . "'";
        
        $Banco->query( $sql, "load2" );
        $row1 = $Banco->getFetchAssoc( "load2" );

        $iNotificacao .= '<div class="dropdown-menu-notif-item">
                            <div class="photo">
                                <img src="img/photo-64-1.jpg" alt="">
                            </div>
                            <div class="dot"></div>
                            Novo orçamento: <a target="_blank" href="' . $iPasta . '/imprimir_orcamento.php?id=' . $row['ID_ENVOLVIDO'] . '">' . $row1['NOME'] . ' </a> ' . $row1['NOME_EVENTO'] . ' 
                            <div class="color-blue-grey-lighter">' . time_elapsed_string($timestamp) . '</div>
                        </div>';

    } elseif ( $row['TIPO'] == 'NOVO_CLIENTE' ) {
        $iPasta = "/cliente";

        $sql = "SELECT
                        NOME
                FROM
                        CLIENTE
                WHERE
                        ID = '" . $row['ID_ENVOLVIDO'] . "'";
        
        $Banco->query( $sql, "load2" );
        $row1 = $Banco->getFetchAssoc( "load2" );

        $iNotificacao .= '<div class="dropdown-menu-notif-item">
                            <div class="photo">
                                <img src="img/photo-64-1.jpg" alt="">
                            </div>
                            <div class="dot"></div>
                            Novo Cliente: <a href="' . $iPasta . '/manu.php?modo=det&id=' . $row['ID_ENVOLVIDO'] . '">' . $row1['NOME'] . '</a>
                            <div class="color-blue-grey-lighter">' . time_elapsed_string($timestamp) . '</div>
                        </div>';
    
    } elseif ( $row['TIPO'] == 'NOVO_PEDIDO' ) {
        $iPasta = "/orcamento";

        $sql = "SELECT
                        C.NOME,
                        O.NOME_EVENTO
                FROM
                        ORCAMENTO AS O
                INNER JOIN
                        CLIENTE AS C ON O.ID_CLIENTE = C.ID
                WHERE
                        O.ID = '" . $row['ID_ENVOLVIDO'] . "'";
        
        $Banco->query( $sql, "load2" );
        $row1 = $Banco->getFetchAssoc( "load2" );

        $iNotificacao .= '<div class="dropdown-menu-notif-item">
                            <div class="photo">
                                <img src="img/photo-64-1.jpg" alt="">
                            </div>
                            <div class="dot"></div>
                            Novo Pedido: <a target="_blank" href="' . $iPasta . '/imprimir_orcamento.php?id=' . $row['ID_ENVOLVIDO'] . '">' . $row1['NOME'] . ' </a> ' . $row1['NOME_EVENTO'] . ' 
                            <div class="color-blue-grey-lighter">' . time_elapsed_string($timestamp) . '</div>
                        </div>';

    } elseif ( $row['TIPO'] == 'NOVO_FORNECEDOR' ) {
        $iPasta = "/fornecedor";

                $sql = "SELECT
                        NOME,
                        NOME_FANTASIA
                FROM
                        FORNECEDOR
                WHERE
                        ID = '" . $row['ID_ENVOLVIDO'] . "'";
        
        $Banco->query( $sql, "load2" );
        $row1 = $Banco->getFetchAssoc( "load2" );

        $iNotificacao .= '<div class="dropdown-menu-notif-item">
                            <div class="photo">
                                <img src="img/photo-64-1.jpg" alt="">
                            </div>
                            <div class="dot"></div>
                            Novo Fornecedor: <a href="' . $iPasta . '/manu.php?modo=det&id=' . $row['ID_ENVOLVIDO'] . '">' . (( $row1['NOME_FANTASIA'] != '') ? $row1['NOME_FANTASIA'] : $row1['NOME'] ) . '</a>
                            <div class="color-blue-grey-lighter">' . time_elapsed_string($timestamp) . '</div>
                        </div>';
     }
}
                return '<body class="with-side-menu theme-side-litmus-blue wet-aspalt-theme">
                            <header class="site-header">
                                <div class="container-fluid">
                                    <a href="/" class="site-logo">
                                        <img class="hidden-md-down" src="/files/img/logo-locja-branca.png" alt="">
                                        <img class="hidden-lg-down" src="img/logo-2-mob.png" alt="">
                                    </a>
                                    <button class="hamburger hamburger--htla">
                                        <span>toggle menu</span>
                                    </button>
                                    <div class="site-header-content">
                                        <div class="site-header-content-in">
                                            <div class="site-header-shown">
                                                <div class="dropdown dropdown-notification notif">
                                                    <a href="#"
                                                       class="header-alarm dropdown-toggle active"
                                                       id="dd-notification"
                                                       data-toggle="dropdown"
                                                       aria-haspopup="true"
                                                       aria-expanded="false">
                                                        <i class="font-icon-alarm"></i>
                                                    </a>
                                                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-notif" aria-labelledby="dd-notification">
                                                        <div class="dropdown-menu-notif-header">
                                                            Notificações
                                                            <span class="label label-pill label-danger">' . $iNumNotificacao . '</span>
                                                        </div>
                                                        <div class="dropdown-menu-notif-list">
                                                            ' . $iNotificacao . '
                                                        </div>
                                                    </div>
                                                </div>
                                                
                            
                            
                                                <div class="dropdown user-menu">
                                                    <button class="dropdown-toggle" id="dd-user-menu" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        <img src="/startui/img/avatar-2-64.png" alt="">
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dd-user-menu">
                                                        <a class="dropdown-item" href="/alterar_senha.php"><span class="font-icon glyphicon glyphicon-lock"></span>Alterar Senha</a>
                                                        <div class="dropdown-divider"></div>
                                                        <a class="dropdown-item" href="/logout.php"><span class="font-icon glyphicon glyphicon-log-out"></span>Logout</a>
                                                    </div>
                                                </div>
                            
                                                <button type="button" class="burger-right">
                                                    <i class="font-icon-menu-addl"></i>
                                                </button>
                                            </div><!--.site-header-shown-->
                            
                                            <div class="mobile-menu-right-overlay"></div>
                                            <div class="site-header-collapsed">
                                                <div class="site-header-collapsed-in">
                                                    <div class="dropdown dropdown-typical">
                                                        <div class="dropdown-menu" aria-labelledby="dd-header-sales">
                                                            <a class="dropdown-item" href="#"><span class="font-icon font-icon-home"></span>Quant and Verbal</a>
                                                            <a class="dropdown-item" href="#"><span class="font-icon font-icon-cart"></span>Real Gmat Test</a>
                                                            <a class="dropdown-item" href="#"><span class="font-icon font-icon-speed"></span>Prep Official App</a>
                                                            <a class="dropdown-item" href="#"><span class="font-icon font-icon-users"></span>CATprer Test</a>
                                                            <a class="dropdown-item" href="#"><span class="font-icon font-icon-comments"></span>Third Party Test</a>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="dropdown dropdown-typical">
                                                        <a href="/index.php" class="dropdown-toggle no-arr">
                                                            <span class="font-icon font-icon-page"></span>
                                                            <span class="lbl">Próximos eventos</span>
                                                            <span class="label label-pill label-danger">' . $iNumeroPedidos . '</span>
                                                        </a>
                                                    </div>
                                                    ' . $iHtmlTrocaPlano . '
                                                    ' . $iHtmlTrocaCM . '
                                                    <div class="dropdown mr-3">
                                                        <button class="btn btn-rounded dropdown-toggle" id="dd-header-add" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            Adicionar
                                                        </button>
                                                        <div class="dropdown-menu" aria-labelledby="dd-header-add">
                                                            <a class="dropdown-item" href="/cliente/manu.php?modo=inc">Cliente</a>
                                                            <a class="dropdown-item" href="/orcamento/manu.php?modo=inc">Orçamento</a>
                                                            <a class="dropdown-item" href="/fornecedor/manu.php?modo=inc">Fornecedor</a>
                                                        </div>
                                                    </div>
                                                    
                                                    <!--.help-dropdown
                                                    <div class="site-header-search-container">
                                                        <form class="site-header-search closed">
                                                            <input type="text" placeholder="Search"/>
                                                            <button type="submit">
                                                                <span class="font-icon-search"></span>
                                                            </button>
                                                            <div class="overlay"></div>
                                                        </form>
                                                    </div>
                                                    -->
                                                </div><!--.site-header-collapsed-in-->
                                            </div><!--.site-header-collapsed-->
                                        </div><!--site-header-content-in-->
                                    </div><!--.site-header-content-->
                                </div><!--.container-fluid-->
                            </header><!--.site-header-->
                            <div class="mobile-menu-left-overlay"></div>
                            <nav class="side-menu">
                                <div class="side-menu-avatar">
                                    <div class="avatar-preview avatar-preview-100">
                                        <img src="' . $iImagemCliente . '" alt="">
                                    </div>
                                </div>
                                ' . monta_menu() . '
                            </nav><!--.side-menu-->';
    }

    function layout_body( $body ) {
        global $Banco;
        
        return layout_top() . '
                    <div class="page-content">
                        <div class="container-fluid">
                            ' . getMensagem() . '
                            ' . $body . '
                        </div>
                    </div>
                    ' . layout_rodape();
    }
    
    function layout_top_orcamento() {
        return '<header id="topo">
                    <div class="navbar">
                        <div id="hide-menu" class="btn-header pull-right">
                            <span class="button_menu"><a href="javascript:void(0);" title="Collapse Menu"><i class="glyphicon glyphicon-th-list"></i></a></span>
                        </div>
                        <div class="navbar-header">
                            <p class="navbar-text navbar-left"><span class="glyphicon glyphicon-user"></span> Visitante</p>
                        </div>
                    </div>
                </header>
                <aside id="left-panel">
                    <nav>
                        ' . monta_menu('orcamento') . '
                    </nav>
                </aside>';
    }
    
    function layout_body_orcamento( $body ) {
        global $Banco;
        
        return layout_top_orcamento() . "
                 <section id='home'>
                    <div id='main'>
                        <div id='conteudo'>
                            " . $body . "
                        </div>
                    </div>
                    " . layout_rodape() ."
                </section>";
    }
    
    /*function layout_topo() {
        return '<div id="topo">
                    <span><a href="' . SITE_HTTP . '">' . EMPRESA_NOME . '</a></span>
                </div>';
    }*/
    
    function layout_rodape() {
        return '<script src="' . SITE_HTTP_STATIC . '/startui/js/lib/jquery/jquery-3.2.1.min.js"></script>
                <script src="' . SITE_HTTP_STATIC . '/startui/js/lib/popper/popper.min.js"></script>
                <script src="' . SITE_HTTP_STATIC . '/startui/js/lib/tether/tether.min.js"></script>
                <script src="' . SITE_HTTP_STATIC . '/startui/js/lib/bootstrap/bootstrap.min.js"></script>
                <script src="' . SITE_HTTP_STATIC . '/startui/js/plugins.js"></script>
                <script src="' . SITE_HTTP_STATIC . '/startui/js/lib/blockUI/jquery.blockUI.js"></script>
                <script src="' . SITE_HTTP_STATIC . '/startui/js/lib/datatables-net/datatables.min.js"></script>
                <script src="' . SITE_HTTP_STATIC . '/startui/js/lib/moment/moment-with-locales.min.js"></script>
                <script src="' . SITE_HTTP_STATIC . '/startui/js/lib/flatpickr/flatpickr.min.js"></script>

                <script src="' . SITE_HTTP_STATIC . '/startui/js/app.js"></script>


                <script src="' . SITE_HTTP_STATIC . '/files/js/jquery.fancybox.js"></script>
                <script src="' . SITE_HTTP_STATIC . '/files/js/jquery.blockUI.js"></script>
                <script src="' . SITE_HTTP_STATIC . '/files/js/jquery.maskedinput-1.1.4.pack.js"></script> 
                <script src="' . SITE_HTTP_STATIC . '/files/js/maskMoney-2.js"></script> 
                <script src="' . SITE_HTTP_STATIC . '/files/js/jquery.autocomplete.js?a=' . time() . '"></script>

                <script src="' . SITE_HTTP_STATIC . '/files/js/global.js?a=' . time() . '"></script>
                
                ' . ( ( !IS_DEV ) ? '<!-- Global site tag (gtag.js) - Google Analytics -->
                <script async src="https://www.googletagmanager.com/gtag/js?id=UA-111269928-2"></script>
                <script>
                  window.dataLayer = window.dataLayer || [];
                  function gtag(){dataLayer.push(arguments);}
                  gtag(\'js\', new Date());

                  gtag(\'config\', \'UA-111269928-2\');
                </script>' : '' ) . '
                
';


    }
    
    
    function monta_menu($visitante = '') {
        global $iRetornoMenu;
        
        if($visitante == 'orcamento' ){
            $iMenu = montaArrayMenu();
        } else {
            $iMenu = montaArrayMenu( $_SESSION['ID'] );
        }
        
        $iRetornoMenu = '<ul class="side-menu-list">';

        
        // Raiz
        foreach ($iMenu['root'] as $ID => $iLabel) {

            //echo "<pre>" . __FILE__ . ":" . __LINE__ . "\n";print_r($iLabel); echo "</pre>";
            //$iRetornoMenu .= "<li> " . $iLabel['Nome'];

            $iOpened = (boolean)$iLabel['PASTA_ATUAL'];
            $iOpenedPagina = (boolean)$iLabel['ARQUIVO_ATUAL'];

            if (isset($iMenu[$ID])) {
                $iRetornoMenu .= "<li class='grey with-sub " . (( $iOpened )?'opened':'') . "'>
                                    <span class='lbl'><i class='font-icon " . $iLabel['ICONE'] . "'></i>" . $iLabel['NOME'] . "</span>";

                monta_menu2($ID, $visitante);
            } else {
                $iRetornoMenu .= "<li " . (( $iOpenedPagina )?' class="opened" ':'') . ">
                                    <a href='" . $iLabel['URL'] . "' title='" . $iLabel['NOME'] . "' " . ($visitante == 'orcamento' ? 'onclick="return false;"' : '') . "><span class='lbl'><i class='font-icon " . $iLabel['ICONE'] . "''></i>" . $iLabel['NOME'] . "</span></a>";
            }

            $iRetornoMenu .= "</li>";
        }
        $iRetornoMenu .= "</ul>";
         
        return $iRetornoMenu;
    }
    
    function montaArrayMenu( $iUsuario = null, $iPermissaoID = null ) {
        global $iMenu, $Banco;
        $iDiretorioPasta = explode("/", $_SERVER['SCRIPT_NAME']);
        $iDiretorioPasta = "/". $iDiretorioPasta[1];

        /*
        $path_parts = pathinfo('/www/htdocs/index.html');
        echo $path_parts['dirname'], "\n";
        */

        $iDiretorioPagina = pathinfo($_SERVER['SCRIPT_NAME']);
        $iDiretorioPagina = $iDiretorioPagina['dirname'];
        $iWhere = '';
        
        if ( $_SESSION['CLIENTE_MASTER_ID'] != 1 ){
            $iWhere = " AND Pag.NOME != 'Cliente Master' ";
        }

        if ( $_SESSION['CLIENTE_MASTER_ID'] == 2 ){
            $iWhere .= " AND Pag.NOME != 'Alterar Senha' ";
        }

        if ( $_SESSION['PLANO_ID'] == 1 ){
            $iWhere .= " AND Pag.NOME != 'Relatórios'
                         AND Pag.NOME != 'Pedidos'";
        }

        if ( $_SESSION['PLANO_ID'] == 2 ){
            $iWhere .= " AND Pag.NOME != 'Relatórios'";
        }



        if ( is_null( $iUsuario ) && is_null( $iPermissaoID ) ) {
            $sql = "SELECT
                                Pag.NOME,
                                Pag.ID,
                                Pag.URL_PHP as URL,
                                Pag.ID_PAI,
                                Pag.ICONE,
                                IF ( Pag.URL = '" . $iDiretorioPasta . "', 1, 0 ) as PASTA_ATUAL,
                                IF ( Pag.URL LIKE '" . $iDiretorioPagina . "%'  , 1, 0 ) as ARQUIVO_ATUAL
                                
                     FROM
                                PAGINAS AS Pag
                     LEFT JOIN
                                PAGINAS AS Pag2 ON Pag2.ID_PAI = Pag.ID
                     WHERE
                                Pag.BLN_MOSTRAR = '1'
                                $iWhere
                     ORDER BY
                                Pag.ORDEM, Pag.NOME";
        } else if ( $iPermissaoID ) {
        	$sql = "SELECT
                                Pag.NOME,
                                Pag.ID,
                                Pag.URL_PHP as URL,
                                Pag.ID_PAI,
                                Pag.ICONE,
                                IF ( Pag.URL = '" . $iDiretorioPasta . "' , 1, 0 ) as PASTA_ATUAL,
                                IF ( Pag.URL LIKE '" . $iDiretorioPagina . "%'  , 1, 0 ) as ARQUIVO_ATUAL
                     FROM
                                PAGINAS AS Pag
                     INNER JOIN
                                PERMISSAO_has_PAGINAS as Per ON Per.PAGINAS_ID = Pag.ID AND Per.PERMISSAO_ID = '" . $iPermissaoID . "'
                     LEFT JOIN
                                PAGINAS AS Pag2 ON Pag2.ID = Pag.ID_PAI
                     WHERE
                                Pag.BLN_MOSTRAR = '1'
                                $iWhere
                     ORDER BY
                                Pag.ORDEM, Pag.NOME";
        } else {
            $sql = "SELECT
                                Pag.NOME,
                                Pag.ID,
                                Pag.URL_PHP as URL,
                                Pag.ID_PAI,
                                Pag.ICONE,
                                IF ( Pag.URL = '" . $iDiretorioPasta . "' , 1, 0 ) as PASTA_ATUAL,
                                IF ( Pag.URL LIKE '" . $iDiretorioPagina . "%'  , 1, 0 ) as ARQUIVO_ATUAL
                     FROM
                                PAGINAS AS Pag
                     INNER JOIN
                                PERMISSAO_has_PAGINAS as Per ON Per.PAGINAS_ID = Pag.ID AND Per.PERMISSAO_ID = '" . $_SESSION['PERMISSAO_ID'] . "'
                     LEFT JOIN
                                PAGINAS AS Pag2 ON Pag2.ID = Pag.ID_PAI
                     WHERE
                                Pag.BLN_MOSTRAR = '1'
                                $iWhere
                     ORDER BY
                                Pag.ORDEM, Pag.NOME";
        }
        
        
        $Banco->query( $sql, "load_menu" );
        
        $iMenu = array();
        while ( $pagina = $Banco->getFetchAssoc("load_menu") ) {
            if (is_null($pagina['ID_PAI'])) {
                $iMenu['root'][$pagina['ID']] = array (
                                                           'NOME' => $pagina['NOME'],
                                                           'URL' => $pagina['URL'],
                                                           'ID' => $pagina['ID'],
                                                           'ICONE' => $pagina['ICONE'],
                                                           'PASTA_ATUAL' => $pagina['PASTA_ATUAL'],
                                                           'ARQUIVO_ATUAL' => $pagina['PASTA_ATUAL']
                                                   );
            } else {
                $iMenu[$pagina['ID_PAI']][$pagina['ID']] = array (
                                                           'NOME' => $pagina['NOME'],
                                                           'URL' => $pagina['URL'],
                                                           'ID' => $pagina['ID'],
                                                           'ICONE' => $pagina['ICONE'],
                                                           'PASTA_ATUAL' => $pagina['PASTA_ATUAL'],
                                                           'ARQUIVO_ATUAL' => $pagina['ARQUIVO_ATUAL']
                                                   );
            }
        }

        return $iMenu;
    }