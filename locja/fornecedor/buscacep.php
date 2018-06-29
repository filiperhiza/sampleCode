<?php
define("SITE_PATH_REL", "../"); 
require(SITE_PATH_REL."include/inicializar.php");

$cep = str_replace("-", "", get_form("cep"));

$url = "http://cep.republicavirtual.com.br/web_cep.php?cep=" . $cep . "&formato=query_string";

$dados = file_get_contents($url);
$dados = mb_convert_encoding($dados, 'UTF-8', mb_detect_encoding($dados, 'UTF-8, ISO-8859-1', true));
if(count($dados) > 0){
    parse_str($dados, $retorno);
    
    $sql = "SELECT
                    C.ID CIDADE,
                    C.NOME,
                    E.ID ESTADO
            FROM
                    CIDADE AS C
            INNER JOIN
                    ESTADO AS E
            ON
                    C.ESTADO_ID = E.ID
            WHERE
                    C.NOME = '" . utf8_encode($retorno['cidade']) . "'
            AND
                    E.UF = '" . utf8_encode($retorno['uf']) . "'";

                    //echo "<pre>" . __FILE__ . ":" . __LINE__ . "\n";print_r($sql); echo "</pre>";

    $Banco->query($sql, "load");
    
    if($row = $Banco->getFetchAssoc("load")){
        $retorno['estado_id'] = $row['ESTADO'];
        $retorno['cidade_id'] = $row['CIDADE'];
    }
    
    $retorno['logradouro'] = substr($retorno['logradouro'], 0, strpos($retorno['logradouro'], " - " ));
    $retorno['cidade'] = $retorno['cidade'];
    $retorno['bairro'] = $retorno['bairro'];
    $retorno['tipo_logradouro'] = $retorno['tipo_logradouro'];
    $retorno['resultado_txt'] = $retorno['resultado_txt'];
    
    $retorno = array_map('utf8_encode', $retorno);
    $retorno = array_map('trata_ajax_retorno', $retorno);
        
    echo json_encode($retorno);
}
die(); 

?>