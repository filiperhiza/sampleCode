<?php
define("SITE_PATH_REL", "../"); 
require(SITE_PATH_REL."include/inicializar.php");

$iPasta = "/fornecedor";

$iEstadoID = get_form("iEstadoID");
$iCidadeID = get_form("iCidadeID");
    
$iComboCidade = '<option value="">Escolha uma cidade</option>';
                     
if ( $iEstadoID != '' ) {
    $sql = "SELECT
                   *
            FROM
                   CIDADE
            WHERE
                   ESTADO_ID = '" . $iEstadoID . "'";
                   
    $Banco->query( $sql, "load" );
    
    while( $row_cidade = $Banco->getFetchAssoc("load") ) {
        $iComboCidade .= '<option value="' . $row_cidade['ID'] . '" ' . (( $row_cidade['ID'] == $iCidadeID )? 'selected="selected"':'' ) . '>' . $row_cidade['NOME']. '</option>'; 
    }
}
 
$iComboCidade .= '</select>';

die( trata_ajax_retorno($iComboCidade) );
