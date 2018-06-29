<?php
define("SITE_PATH_REL", "../"); 
require(SITE_PATH_REL."include/inicializar.php");


$iStart = get_form('start');
$iLength = get_form('length');
$iDraw = get_form('draw');
$iSearch = get_form('search');

//echo "<pre>" . __FILE__ . ":" . __LINE__ . "\n";print_r($_GET); echo "</pre>";
global $iPaginaAtual;
$iPaginaAtual = ( $iStart + $iLength ) / $iLength;

//echo "<pre>" . __FILE__ . ":" . __LINE__ . "\n";print_r($iPaginaAtual); echo "</pre>";


$iWhere = '';
if ( isset( $iSearch['value'] ) && $iSearch['value'] != '' ) {
    $iWhere = "AND NOME LIKE '%" . utf8_decode($iSearch['value']) . "%'";
}

//echo "<pre>" . __FILE__ . ":" . __LINE__ . "\n";print_r($iSearch); echo "</pre>";

/*
echo "<pre>" . __FILE__ . ":" . __LINE__ . "\n";print_r($iStart); echo "</pre>";
echo "<pre>" . __FILE__ . ":" . __LINE__ . "\n";print_r($iLength); echo "</pre>";
*/
//echo "<pre>" . __FILE__ . ":" . __LINE__ . "\n";print_r($_GET); echo "</pre>";
//die;

$iRetorno = array();

$iRetorno['draw'] = $iDraw;
 
$sql = "SELECT
                *
        FROM
                FORNECEDOR
        WHERE
                CLIENTE_MASTER_ID = '" . $_SESSION['CLIENTE_MASTER_ID'] . "'
        $iWhere";
                
$pg = new Paginacao('paginacao', $iLength, $sql, '');


$iRetorno['recordsTotal'] = $pg->nRegs;
$iRetorno['recordsFiltered'] = $pg->nRegs;


$sql = "SELECT
                *
        FROM
                FORNECEDOR
        WHERE
                CLIENTE_MASTER_ID = '" . $_SESSION['CLIENTE_MASTER_ID'] . "'
        $iWhere" . $pg->getLimit();

                //echo "<pre>" . __FILE__ . ":" . __LINE__ . "\n";print_r($sql); echo "</pre>";
                
$Banco->query( $sql, "load" );
$iRetorno['data'] = array();

$i = 0;
if ( $Banco->getNumRows( "load" ) ) {
    while ( $row = $Banco->getFetchAssoc( "load" ) ) {

        $iAux = array(
            $row['ID'],
            $row['NOME'],
            
            '<div align="center">                 
             <a href="manu.php?modo=det&id=' . $row['ID'] . '" title="Detalhar"><span class="btn btn-info glyphicon glyphicon-search"></span></a>
             <a href="manu.php?modo=alt&id=' . $row['ID'] . '" title="Alterar"><span class="btn btn-warning glyphicon glyphicon-edit"></span></a>
             <a href="manu.php?modo=exc&id=' . $row['ID'] . '" title="Excluir"><span class="btn btn-danger glyphicon glyphicon-remove"></span></a> 
             </div>'
        );
        /*
        foreach( $iAux as $k => $v ) {
            $iAux[$k] = utf8_encode($v);
        }
        */
        $iRetorno['data'][] = $iAux;

        /*
        $showBody .= '$showBody .= '<tr ' . (( $i++%2 )?'class="escuro"':'') . '>
                         <td>' . $row['ID'] . '</td>
                         <td>' . $row['NOME'] . '</td>
                         <td>' . $row['EMAIL'] . '</td>
                         <td>' . ( ($row['TIPO'] == 0) ? 'Tel: ' : 'Cel: ' ) . $row['TELEFONE'] . (( $row['TELEFONE_2'] != '')?' / ' . ( ($row['TIPO_2'] == 0) ? 'Tel: ' : 'Cel: ' ) . $row['TELEFONE_2']:'') . '</td>
                         <td align="center" width="25%">
                             <a href="' . $iPasta . '/manu.php?modo=det&id=' . $row['ID'] . '" title="Detalhar"><span class="btn btn-info glyphicon glyphicon-search"></span></a>
                             <a href="' . $iPasta . '/manu.php?modo=alt&id=' . $row['ID'] . '" title="Alterar"><span class="btn btn-warning glyphicon glyphicon-edit"></span></a>
                             <a href="' . $iPasta . '/manu.php?modo=exc&id=' . $row['ID'] . '" title="Excluir" onclick="return confirm(\'Tem certeza?\');"><span class="btn btn-danger glyphicon glyphicon-remove"></span></a>
                         </td>
                     </tr>';';
                     */
    }
}

//echo "<pre>" . __FILE__ . ":" . __LINE__ . "\n";print_r($iRetorno); echo "</pre>";

echo json_encode($iRetorno);
//json_encode($iRetorno)