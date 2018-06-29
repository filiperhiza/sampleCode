<?php

class Categoria extends Zend_Db_Table_Abstract
{
     protected $_name = 'tab_achagora_categoria2';
     protected $_primary = 'num_id';
     
     
     public function getNumeroEmpresas( $rr_ids_categoria ) {
         
         if ( !count( $rr_ids_categoria ) ) {
             return array(
                    'num_empresas' => array(),
                    'num_visitas' =>  array()
                );
         }
         
         $sql = "SELECT
                        num_categoria_id,
                        num_empresas
                FROM
                        `tab_numero_empresas`
                WHERE
                        num_categoria_id IN ('" . implode("','", $rr_ids_categoria) . "')
                AND
                        num_estado_id IS NULL
                AND
                        num_cidade_id IS NULL";
                        
         $db = Zend_Registry::get('db');
                             
        $stmt = $db->query($sql);
        $rr_empresas = array();
        foreach( $stmt->fetchAll( Zend_Db::FETCH_OBJ ) as $cat ) {
            $rr_empresas[$cat->num_categoria_id] = $cat->num_empresas;
        }
        
        //pegando os acessos dos últimos 3 meses
        $sql = "SELECT
                        SUM(num_visualizacoes) as num_visualizacoes,
                        dte_inicio as dte,
                        num_categoria_id,
                        DATE(dte_inicio) as mes_aux,
                        DATE(NOW()) as date_aux
                FROM
                        tab_visualizacao_agrupada
                WHERE
                        DATE(dte_inicio) >= '" . date( 'Y-m-d', strtotime("-6 months") ) . "'
                AND
                        num_categoria_id IN ('" . implode("','", $rr_ids_categoria) . "')
                /*
                AND
                        num_estado_id = 0
                AND
                        num_cidade_id = 0
                */
                GROUP BY
                        num_categoria_id, dte_inicio";
                        
                        //echo"<pre>".__FILE__.":".__LINE__."\n";print_r($sql);echo"</pre>";
                        
        $stmt = $db->query($sql);
        $rr_visitas = array();
        $num_categoria_old = 0;
        $rr_meses = array();
        foreach( $stmt->fetchAll( Zend_Db::FETCH_OBJ ) as $cat ) {
            
            $d1 = new DateTime($cat->date_aux);
            $d2 = new DateTime($cat->mes_aux);
            
            /*
            echo"<pre>".__FILE__.":".__LINE__."\n";print_r($cat->date_aux);echo"</pre>";
            echo"<pre>".__FILE__.":".__LINE__."\n";print_r($cat->mes_aux);echo"</pre>";
            */
            
            $num_mes = $d2->format('M');
            $rr_meses[] = $num_mes;
            
            /*
            echo"<pre>".__FILE__.":".__LINE__."\n";print_r($num_mes);echo"</pre>";
            echo "<hr />";
            */
            
            $rr_visitas[$cat->num_categoria_id][$num_mes] = $cat->num_visualizacoes;
        }
        
        $rr_meses = array_unique($rr_meses);
        
        $rr_return = array(
            'num_empresas' => $rr_empresas,
            'num_visitas' => $rr_visitas,
            'rr_meses' => $rr_meses
        );
                        
        return $rr_return;
     }
     
     public function insereAcesso( $rr_num_id ) {
         if ( !is_array($rr_num_id) || empty($rr_num_id) ) {
             return;
         }
         
         if ( isCrawler( isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : '' ) ) {
             return;
         }
         
         $sql = "\nUPDATE tab_achagora_categoria2 SET num_visitas = num_visitas + 1 WHERE num_id IN (" . implode( ",", $rr_num_id ) . ");";
         //file_put_contents( $_SERVER['DOCUMENT_ROOT'] . "/cron/files/atualiza_visitas.sql", $sql, FILE_APPEND );
         $this->_db->query($sql);
     }
     
     public function retornaMaisVisitadas( $num_id_cidade, $num_categorias = 20 ) {
         
          $sql = "SELECT
                          Cat.str_nome,
                          Cat.str_url,
                          Cat.str_slug,
                          Cat.num_visitas
                  FROM
                          tab_achagora_categoria2 AS Cat
                  INNER JOIN
                          tab_numero_empresas as Num ON Num.num_categoria_id = Cat.num_id
                  INNER JOIN
                          tab_achagora_cidade AS Cid ON Cid.num_id = Num.num_cidade_id AND Num.num_estado_id = Cid.num_estado_id
                  WHERE
                          Cat.bln_publicado = 1
                  AND
                          Cid.num_id = '" . $num_id_cidade  . "'
                  ORDER BY
                          Cat.num_visitas DESC, Cat.str_nome
                  LIMIT
                          " . $num_categorias;
                          
                          //echo "<pre>".__FILE__.":".__LINE__.":\n"; print_r($sql); echo "</pre>";
                                  /*
         $sql = "SELECT
                         Cat.str_nome,
                         Cat.str_url,
                         Cat.num_visitas
                 FROM
                         tab_achagora_categoria2 AS Cat
                 WHERE
                        Cat.bln_publicado = 1
                 ORDER BY
                         Cat.num_visitas DESC, Cat.str_nome
                 LIMIT
                         " . $num_categorias;
        */
        
         $stmt = $this->_db->query($sql);
         return $stmt->fetchAll( Zend_Db::FETCH_OBJ );
         
     }
     
     public function retornaCategoriasRelacionadas( $rr_parametros, $num_limite = 40 ) {
        global $cache_oito;

         //verificando os paremetros
         if ( !isset( $rr_parametros['categoria'] ) ) {
             return array();
         }


         //Recuperando a categoria
         //$Categoria = new Categoria();
         
         //$Categoria = $Categoria->fetchRow("str_slug = '" . $rr_parametros['categoria'] . "'");
         $Categoria = $rr_parametros['obj_categoria'];
         if ( !$Categoria ) {
             return array();
         }
         
         $str_where = "RelCat.num_categoria_pai_id = '" . $Categoria->num_id . "'";
         
         //Verificando se existe o estado e a cidade
         if( isset( $rr_parametros['estado'] ) ) {
             //Pegando qual estado
             //$Estado = new Estado();
             //$Estado = $Estado->fetchRow("str_slug = '" . $rr_parametros['estado'] . "'");
            $Estado = $rr_parametros['obj_estado'];
             
             if ( !$Estado ) {
                 throw new Exception( "Estado não encontrado. Parâmetro de estado recebido: ".$rr_parametros['estado']."" );
             }
             
             $str_where .= (( $str_where != "" )? " AND " : "" ) . "Num.num_estado_id = '" . $Estado->num_id . "'";
         } else {
             $str_where .= (( $str_where != "" )? " AND " : "" ) . "Num.num_estado_id IS NULL";
         }
         
         
         if( isset( $rr_parametros['cidade'] ) ) {
             //Pegando qual estado                      
             //$Cidade = new Cidade();
             //$Cidade = $Cidade->fetchRow("str_slug = '" . $rr_parametros['cidade'] . "' AND num_estado_id = '" . $Estado->num_id . "'");
            $Cidade = $rr_parametros['obj_cidade'];
             
             if ( !$Cidade ) {
                 trigger_error( "Cidade não encontrado" );
             }
             
             $str_where .= (( $str_where != "" )? " AND " : "" ) . "Num.num_cidade_id = '" . $Cidade->num_id . "'";
         } else {
             $str_where .= (( $str_where != "" )? " AND " : "" ) . "Num.num_cidade_id IS NULL";
         }

         //Selecionando as categorias relacionadas
         $sql = "SELECT DISTINCT
                            Cat.str_nome,
                            Cat.str_url,
                            Cat.str_slug,
                            Num.num_empresas
                 FROM
                            tab_achagora_categoria2 AS Cat
                 INNER JOIN
                            tab_achagora_rel_categoria_categoria as RelCat ON RelCat.num_categoria_filho_id = Cat.num_id
                 INNER JOIN
                            tab_numero_empresas as Num ON Num.num_categoria_id = RelCat.num_categoria_filho_id
                 WHERE
                            " . $str_where . "
                 ORDER BY
                            Num.num_empresas DESC
                 LIMIT
                            $num_limite";

                            //echo "<pre>".__FILE__.":".__LINE__."\n";print_r($sql);echo "</pre>";
                            ///die;

        $str_cache_nome = md5($sql . SITE_HOST);
                        
        if( !$row = $cache_oito->load( $str_cache_nome ) ) {
                 
            $stmt = $this->_db->query($sql);
            $row = $stmt->fetchAll( Zend_Db::FETCH_OBJ );
             
            $cache_oito->save($row, $str_cache_nome);
            //echo "<h1>fez cache</h1>";   
         } else {
            //echo "<h1>leu cache</h1>";
         }


         return $row;
     }
     
     
     /*
     public function retornaCategoriasRelacionadas( $str_categoria_url ) {
         
         //pegando o id da categoria
         //Zend_Loader::loadClass('Categoria');
         $Categoria = new Categoria();
         $Categoria = $Categoria->fetchRow("str_url = '" . $str_categoria_url . "'");
         if ( !$Categoria ) {
             return false;
         }
         //Selecionando as categorias relacionadas
         $sql = "SELECT
                            Cat.str_nome,
                            Cat.str_url
                 FROM
                            tab_achagora_categoria2 AS Cat
                 INNER JOIN
                            tab_achagora_rel_categoria_categoria as RelCat ON RelCat.num_categoria_filho_id = Cat.num_id
                 WHERE
                            RelCat.num_categoria_pai_id = '" . $Categoria->num_id . "'";
                            
         $stmt = $this->_db->query($sql);
         return $stmt->fetchAll( Zend_Db::FETCH_OBJ );
     }
     */
     
     public function getCategoriasMaisAcessadas( $num_cidade_id ) {
         
         if ( !$num_cidade_id ) { // o Bot do google precisa deste parametro se nÃ£o da um erro sempre que ele for acessar.
             $num_cidade_id = 9422;
         }
         
         //Selecionando as empresas mais acessadas desta cidade
         $sql = "SELECT
                         Emp.num_id
                 FROM
                         tab_achagora_empresa AS Emp
                 WHERE
                         Emp.num_cidade_id = " . $num_cidade_id . "
                 ORDER BY
                         Emp.num_visitas DESC
                 LIMIT
                         50";
                         
         $stmt = $this->_db->query($sql);
         $rr_num_empresa_id = array(); 
         foreach( $stmt->fetchAll( Zend_Db::FETCH_OBJ ) as $empresa ) {
             $rr_num_empresa_id[] = $empresa->num_id;
             //Selecionando as categorias da empresa
         }
         
         //Seleiconando as categorias mais acessadas para esta cidade
         $sql = "SELECT DISTINCT
                        Cat.num_id,
                        Cat.str_url,
                        Cat.str_nome,
                        Cat.num_visitas
                 FROM
                        tab_achagora_categoria2 AS Cat
                 INNER JOIN
                        tab_achagora_rel_empresa_categoria AS RelEmp ON RelEmp.num_categoria_id = Cat.num_id
                 WHERE
                        RelEmp.num_empresa_id IN (" . implode(",", $rr_num_empresa_id) . ")
                 ORDER BY
                        Cat.num_visitas DESC
                 LIMIT
                        50";
         
         unset($stmt);
         $stmt = $this->_db->query($sql);
         return $stmt->fetchAll( Zend_Db::FETCH_OBJ );
         
     }
     
     public function retornaQueryCategoriaAdsense( $num_id ){
         $sql = "SELECT
                         str_nome
                 FROM
                        tab_achagora_categoria2
                WHERE
                        num_id IN ( '" . implode("','", $num_id) . "' )
                ORDER BY
                        num_visitas
                LIMIT
                        1";
                         
         $stmt = $this->_db->query($sql);
         foreach( $stmt->fetchAll( Zend_Db::FETCH_OBJ ) as $categoria ) {
             return $categoria->str_nome;
         }
         
     }     
     
     
     public function fetchAllCache($str_name, $where = null, $order = null, $count = null, $offset = null) {
         global $cache_oito;
         
         $str_name .= SITE_HOST;
         
         $str_name = md5($str_name);
         
         if( !$cache_oito->test( $str_name ) ) {
             
             $select = new Zend_Db_Select( $this->_db );
             $select->from($this->_name);
             
             if ( $where !== null )
                 $select->where( $where );
             
             if ( $order !== null )
                 $select->order( $order );
             
             if ( $count !== null || $offset !== null )
                 $select->limit( $count, $offset );
             
             $rs = $select->query();
             
             $data = $rs->fetchAll( Zend_DB::FETCH_OBJ );
             
             $cache_oito->save( $data , $str_name );
         } else {
             $data = $cache_oito->load( $str_name );
         }
         
         return $data;
     }
     
     public function retornaTokens(){
         $sql = "SELECT
                        num_id as id,
                        str_nome as name
                FROM
                        tab_achagora_categoria2
                WHERE
                        bln_publicado = 1
                        ".( $_GET["q"] != '' ? "AND str_nome LIKE '%%".addslashes($_GET["q"])."%%' " : "" )."
                ORDER BY
                        str_nome ASC";
                        
         $stmt = $this->_db->query($sql);
         
         return $stmt->fetchAll( Zend_Db::FETCH_OBJ );
     }
     
     public function getCategoriaEstados($slug){
         global $cache_oito;
         
         $sql = "SELECT DISTINCT
                        E.num_id,
                        E.str_nome,
                        E.str_slug,
                        C.str_nome AS categoria,
                        Ne.num_empresas
                 FROM
                        tab_achagora_categoria2 AS C
                 INNER JOIN
                        tab_numero_empresas AS Ne ON Ne.num_categoria_id = C.num_id AND Ne.num_cidade_id IS NULL
                 INNER JOIN
                        tab_achagora_estado AS E ON E.num_id = Ne.num_estado_id
                 WHERE
                        C.str_slug = '" . $slug . "'
                 ORDER BY
                        E.str_nome ASC";
                        
        $str_nome_cache = md5($sql . SITE_HOST . 'a');
                         
        if ( !$row = $cache_oito->load( $str_nome_cache ) ) {
            $stmt = $this->_db->query($sql);
            $row = $stmt->fetchAll( Zend_Db::FETCH_OBJ );
            
            $cache_oito->save( $row, $str_nome_cache );
        }
        
        return $row;
        
     }
     
     
     public function getCategoriaCidades($slug_categoria, $slug_estado){
         $sql = "SELECT DISTINCT
                        Cid.num_id,
                        Cid.str_nome,
                        Cid.str_slug,
                        Cid.str_url,
                        E.str_nome AS nome,
                        E.str_slug AS slug,
                        C.str_nome AS categoria
                 FROM
                        tab_achagora_categoria2 AS C
                 INNER JOIN
                        tab_numero_empresas AS Ne
                 ON
                        Ne.num_categoria_id = C.num_id
                 INNER JOIN
                        tab_achagora_estado AS E
                 ON
                        E.num_id = Ne.num_estado_id
                 INNER JOIN
                        tab_achagora_cidade AS Cid
                 ON
                        Cid.num_id = Ne.num_cidade_id
                 WHERE
                        C.str_slug = '" . $slug_categoria . "'
                 AND
                        E.str_slug = '" . $slug_estado . "'
                 ORDER BY
                        Cid.str_nome ASC";
                        
         $stmt = $this->_db->query($sql);
         return $stmt->fetchAll( Zend_Db::FETCH_OBJ );
     }
     
      public function getTopCategoriasCidadeIndex( $num_cidade, $limite ){
         global $cache_oito;
         
         $sql = "SELECT
                         aux.*
                 FROM (
                        SELECT
                                Cat.str_nome AS tag,
                                Cat.str_slug as str_categoria_slug,
                                Cid.str_slug as str_cidade_slug,
                                Est.str_slug as str_estado_slug,
                                Num.num_acessos as num_quantidade
                                /*
                                ,
                                Ne.num_empresas
                                */
                        FROM
                                tab_categorias_mais_acessadas as Num
                        INNER JOIN
                                tab_achagora_cidade AS Cid ON Cid.num_id = Num.num_cidade_id
                        INNER JOIN
                                tab_achagora_estado as Est ON Est.num_id = Cid.num_estado_id
                        INNER JOIN
                                tab_achagora_categoria2 AS Cat ON Cat.num_id = Num.num_categoria_id
                        /*
                        INNER JOIN
                                tab_numero_empresas as Ne ON Ne.num_cidade_id = Cid.num_estado_id AND Ne.num_categoria_id = Num.num_categoria_id
                        */
                        WHERE
                                Num.num_cidade_id = '" . $num_cidade . "'
                        AND
                               Cat.bln_publicado = 1
                        AND
                                   EXISTS (
                                        SELECT
                                                1
                                        FROM
                                                tab_achagora_rel_empresa_categoria AS R
                                        WHERE
                                                R.num_categoria_id = Cat.num_id
                                        AND
                                                R.num_cidade_id = Num.num_cidade_id
                                   )
                        AND
                            CHAR_LENGTH( Cat.str_nome ) <= 27
                        GROUP BY
                                Cat.num_id,
                                Num.num_acessos
                                /*,
                                Ne.num_empresas
                                */
                        ORDER BY
                                Num.num_acessos DESC
                        LIMIT
                            " . $limite . "
                        ) AS aux
                ORDER BY
                        aux.tag";
                        

        //$str_nome_cache = 'atop_categorias_' . $limite . '_' . $num_cidade . (INT)IS_EN;
        $str_nome_cache = md5($sql . SITE_HOST);

        if( !$row = $cache_oito->load( $str_nome_cache ) ) {
            $stmt = $this->_db->query($sql);
            $row = $stmt->fetchAll( Zend_Db::FETCH_ASSOC );
            
            if ( !count( $row ) ) {
                 
                 $sql = "SELECT
                                 aux.*
                         FROM (
                                SELECT
                                        Cat.str_nome AS tag,
                                        Cat.str_slug as str_categoria_slug,
                                        Cid.str_slug as str_cidade_slug,
                                        Est.str_slug as str_estado_slug,
                                        Num.num_empresas as num_empresas
                                FROM
                                        tab_numero_empresas as Num
                                INNER JOIN
                                        tab_achagora_cidade AS Cid ON Cid.num_id = Num.num_cidade_id
                                INNER JOIN
                                        tab_achagora_estado as Est ON Est.num_id = Cid.num_estado_id
                                INNER JOIN
                                        tab_achagora_categoria2 AS Cat ON Cat.num_id = Num.num_categoria_id
                                WHERE
                                        Num.num_cidade_id = '" . $num_cidade . "'
                                AND
                                        Cat.bln_publicado = 1
                                AND
                                           EXISTS(
                                                SELECT
                                                        1
                                                FROM
                                                        tab_achagora_rel_empresa_categoria AS R
                                                WHERE
                                                        R.num_categoria_id = Cat.num_id
                                                AND
                                                        R.num_cidade_id = Num.num_cidade_id
                                           )
                                GROUP BY
                                        Cat.num_id,
                                        Num.num_empresas
                                ORDER BY
                                        Num.num_empresas DESC
                                LIMIT
                                    " . $limite . "
                                ) AS aux
                        ORDER BY
                                aux.tag";
                                
                               
                   $stmt = $this->_db->query($sql);
                   $row = $stmt->fetchAll( Zend_Db::FETCH_ASSOC );
             }

            $cache_oito->save( $row, $str_nome_cache );
        }
        
        return $row;

     }
     
     public function returnAutocomplete($termo){
         $sql = "SELECT 
                        str_nome as name
                FROM 
                        tab_achagora_categoria2
                WHERE
                        str_nome LIKE '%".addslashes(retira_acentos($termo))."%'
                AND
                        bln_publicado = 1";
         $stmt = $this->_db->query($sql);
         
         return $stmt->fetchAll( Zend_Db::FETCH_OBJ );
     }
     
     public function getAcessoMesCat( $num_id, $dte_inicio, $dte_fim ) {
         
         
         $sql = "SELECT
                          V.num_categoria_id, 
                          COUNT(*) as num_total
                  FROM
                          tab_visualizacao AS V
                  WHERE
                          V.num_categoria_id = '" . $num_id . "'
                  AND
                          V.dte BETWEEN '" . $dte_inicio . "' AND '" . $dte_fim . "'
                  GROUP BY
                          V.num_categoria_id";
                          
         $stmt = $this->_db->query($sql);
         if ( $retorno = $stmt->fetch( Zend_Db::FETCH_OBJ ) ) {
             return $retorno->num_total;
         } else {
             return (( IS_DEV )?rand(0,50000):0);
             //return number_format((( IS_DEV )?rand(0,50000):0), 0, "", ".");
         }
     }
     
     public static function getCatsTopo( $num_cidade_id, $num_limit = 3 ) {
         global $cache_oito;
         //" . (( $bln_icon )?" AND Cat.str_icon != '' AND Cat.str_icon IS NOT NULL":'') . "

         $db = Zend_Registry::get('db');

          $sql = "SELECT
                          Cat.str_nome,
                          Cat.str_slug,
                          Cat.str_icon,
                          Cat.str_busca,
                          Cat.str_exibe,
                          Cat.num_id
                  FROM
                          tab_achagora_categoria2 AS Cat
                  ". (( $num_cidade_id && 1==2)?
                      "INNER JOIN
                            tab_numero_empresas AS N ON N.num_categoria_id = Cat.num_id AND N.num_cidade_id = '" . $num_cidade_id . "'"
                      :'') ."
                  WHERE
                          Cat.bln_destaque_mobile = '1'
                  GROUP BY
                          Cat.str_nome,
                          Cat.str_slug
                  ORDER BY
                          Cat.num_ordem
                  LIMIT
                          " . $num_limit;
                          
         $str_nome_cache = md5("top-bar-a-" . $sql . SITE_HOST); //NAO PODE MDUAR ESTE NOME< O ADM USA ESSE HASH PARA LIMPAR O CACHE QUANDO TROCA CATEGORIA
                         
         if( !$rr_results = $cache_oito->load( $str_nome_cache ) ) {
             $stmt = $db->query($sql);
             $rr_results = $stmt->fetchAll( Zend_Db::FETCH_OBJ );
             
             $cache_oito->save( $rr_results, $str_nome_cache );
         }
         
         return $rr_results;
          
     }
     
     public function getBairros( $num_categoria_id, $num_cidade_id, $num_limite = 5 ) {
        global $cache_oito;
         if ( is_null( $num_cidade_id ) ) {
             return array();
         }
         
         //pegando os bairros que tem aquela categoria
         $sql = "SELECT DISTINCT
                        B.str_nome,
                        B.str_slug,
                        B.num_id
                 FROM
                        tab_bairro B
                 INNER JOIN
                        tab_bairro_categoria as RelBC ON RelBC.num_bairro_id = B.num_id AND RelBC.num_categoria_id = '" . $num_categoria_id . "' 
                 WHERE 
                        B.num_cidade_id = '" . $num_cidade_id . "'
                 LIMIT
                        " . $num_limite;

         $str_nome_cache = md5("top-bar-bairrosa-" . $sql . SITE_HOST); //NAO PODE MDUAR ESTE NOME< O ADM USA ESSE HASH PARA LIMPAR O CACHE QUANDO TROCA CATEGORIA
                         
         if( !$rr_results = $cache_oito->load( $str_nome_cache ) ) {
             $stmt = $this->_db->query($sql);
             $rr_results = $stmt->fetchAll( Zend_Db::FETCH_OBJ );
             
             $cache_oito->save( $rr_results, $str_nome_cache );
         }
         
         return $rr_results;
     }
}                                                                                           
