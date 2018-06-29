<?php
define("SITE_PATH_REL", "../"); 
require(SITE_PATH_REL."include/inicializar.php");

$iPasta = "/fornecedor";

$iModo = get_form("modo");
$iID = get_form("id");
$iCNPJ = get_form("iCNPJ");
$iIE = get_form("iIE");
$iNome = get_form("iNome");
$iTipoTelefone = get_form("iTipoTelefone");
$iNomeFantasia = get_form("iNomeFantasia");
$iEmail = get_form("iEmail");
$iSite = get_form("iSite");
$iTelefone = get_form("iTelefone");
$iTelefone2 = get_form("iTelefone2");
$iLogradouro = get_form("iLogradouro");
$iEndereco = get_form("iEndereco");
$iNumero = get_form("iNumero");
$iComplemento = get_form("iComplemento");
$iBairro = get_form("iBairro");
$iCEP = get_form("iCEP");
$iCidadeID = get_form("iCidadeID");
$iEstadoID = get_form("iEstadoID");
$iAcao = get_form("iAcao");


$iErro = array();

if ( $iAcao != "" || $iModo == 'exc' ) {
    switch( $iModo ) {
        case 'inc':
            
            //if ( $iCNPJ == "" /* && validaCNPJ( $iCNPJ ) */ ) $iErro['iCNPJ'] = "CNPJ inválido";
            if ( $iNome == "" ) $iErro['iNome'] = "Nome inválido";
            //if ( $iNomeFantasia == "" ) $iErro['iNomeFantasia'] = "Nome fantasia inválido";
            //if ( $iTelefone == "" ) $iErro['iTelefone'] = "Telefone inválido";
            //if ( $iEndereco == "" ) $iErro['iEndereco'] = "Endereco inválido";
            //if ( $iNumero == "" ) $iErro['iNumero'] = "Número inválido";
            //if ( $iBairro == "" ) $iErro['iBairro'] = "Bairro inválido";
            //if ( $iCEP == "" ) $iErro['iCEP'] = "CEP inválido";
            //if ( $iEstadoID == "" ) $iErro['iEstadoID'] = "Estado inválido";
            //if ( $iCidadeID == "" ) $iErro['iCidadeID'] = "Cidade inválida";
            
            if ( count( $iErro ) ) { //verificando erros
                break;
            }
            
            $sql = "INSERT INTO FORNECEDOR (
                                               CNPJ,
                                               IE,
                                               NOME,
                                               NOME_FANTASIA,
                                               TELEFONE,
                                               TELEFONE_2,
                                               LOGRADOURO,
                                               ENDERECO,
                                               NUMERO,
                                               COMPLEMENTO,
                                               BAIRRO,
                                               CEP,
                                               CIDADE_ID,
                                               ESTADO_ID,
                                               SITE,
                                               EMAIL,
                                               CLIENTE_MASTER_ID
                                           ) VALUES (
                                               '" . $iCNPJ . "',
                                               '" . $iIE . "',
                                               '" . $iNome . "',
                                               '" . $iNomeFantasia . "',
                                               '" . $iTelefone . "',
                                               '" . $iTelefone2 . "',
                                               '" . $iLogradouro . "',
                                               '" . $iEndereco . "',
                                               '" . $iNumero . "',
                                               '" . $iComplemento . "',
                                               '" . $iBairro . "',
                                               '" . $iCEP . "',
                                               '" . $iCidadeID . "',
                                               '" . $iEstadoID . "',
                                               '" . $iSite . "',
                                               '" . $iEmail . "',
                                               '" . $_SESSION['CLIENTE_MASTER_ID'] . "'
                                           )";
                                           
            $Banco->execute( $sql );
            
            $iRegistroID = $Banco->getLastInsertId();
            inserelog('FORNECEDOR','insert',$iRegistroID, $_REQUEST);
            
            insereNotificacao($iRegistroID, 'NOVO_FORNECEDOR');

            $_SESSION['mensagem'] = 'Inserido com sucesso';
            header("location: index.php");
            die;
        break;
        case 'alt':
            
            //if ( $iCNPJ == "" /* && validaCNPJ( $iCNPJ ) */ ) $iErro['iCNPJ'] = "CNPJ inválido";
            if ( $iNome == "" ) $iErro['iNome'] = "Nome inválido";
            //if ( $iNomeFantasia == "" ) $iErro['iNomeFantasia'] = "Nome fantasia inválido";
            //if ( $iTelefone == "" ) $iErro['iTelefone'] = "Telefone inválido";
            //if ( $iEndereco == "" ) $iErro['iEndereco'] = "Endereco inválido";
            //if ( $iNumero == "" ) $iErro['iNumero'] = "Número inválido";
            //if ( $iBairro == "" ) $iErro['iBairro'] = "Bairro inválido";
            //if ( $iCEP == "" ) $iErro['iCEP'] = "CEP inválido";
            //if ( $iEstadoID == "" ) $iErro['iEstadoID'] = "Estado inválido";
            //if ( $iCidadeID == "" ) $iErro['iCidadeID'] = "Cidade inválida";
            
            if ( count( $iErro ) ) { //verificando erros
                break;
            }
            
            $sql = "UPDATE
                            FORNECEDOR
                    SET
                            CNPJ = '" . $iCNPJ . "',    
                            IE = '" . $iIE . "',
                            NOME = '" . $iNome . "',
                            NOME_FANTASIA = '" . $iNomeFantasia . "',
                            TELEFONE = '" . $iTelefone . "',
                            TELEFONE_2 = '" . $iTelefone2 . "',
                            LOGRADOURO = '" . $iLogradouro . "',
                            ENDERECO = '" . $iEndereco . "',
                            NUMERO = '" . $iNumero . "',
                            COMPLEMENTO = '" . $iComplemento . "',
                            BAIRRO = '" . $iBairro . "',
                            CEP = '" . $iCEP . "',
                            CIDADE_ID = '" . $iCidadeID . "',
                            ESTADO_ID = '" . $iEstadoID . "',
                            EMAIL = '" . $iEmail . "',
                            SITE = '" . $iSite . "'
                    WHERE
                            ID = '" . $iID . "'
                    AND 
                            CLIENTE_MASTER_ID = '" . $_SESSION['CLIENTE_MASTER_ID'] . "'";
                            
            $Banco->execute( $sql );
            
            $iRegistroID = $iID;
            inserelog('FORNECEDOR','update',$iRegistroID, $_REQUEST);
            
            $_SESSION['mensagem'] = 'Editado com sucesso';
            header("location: index.php");
            die;
        break;
        case 'exc':
            $sql = "DELETE FROM FORNECEDOR WHERE ID = '" . $iID . "'";
            $Banco->execute( $sql );
            
            $iRegistroID = $iID;
            inserelog('FORNECEDOR','delete',$iRegistroID, $_REQUEST);
            
            $_SESSION['mensagem'] = 'Excluído com sucesso';
            header("location: index.php");
            die;
        break;
    }
}

$iInputs = '<input type="hidden" name="iID" id="iID" value="' . $iID . '" />
            <input type="hidden" name="iModo" id="iModo" value="' . $iModo . '" />';

if ( $iModo == "alt" ) {
    $iInputs .= '<input type="submit" name="iAcao" id="iAcao" value="Alterar" class="btn btn-primary"/> <a href="' . $iPasta . '/index.php" title="Cancelar" class="btn btn-danger" />Cancelar</a>';
} else if ( $iModo == 'inc' ) {
    $iInputs .= '<input type="submit" name="iAcao" id="iAcao" value="Criar" class="btn btn-success" /> <a href="' . $iPasta . '/index.php" title="Cancelar" class="btn btn-danger" />Cancelar</a>';
} else if ( $iModo == 'det' ) {
    $iInputs .= '<a href="' . $iPasta . '/manu.php?modo=alt&id=' . $iID . '" title="Alterar" class="btn btn-primary">Alterar</a> <a href="' . $iPasta . '/index.php" title="Cancelar" class="btn btn-danger" />Cancelar</a>';
}

if ( ( $iModo == 'det' || $iModo == 'alt' ) && $iAcao == "" ) {
    $sql = "SELECT
                    *
            FROM
                    FORNECEDOR
            WHERE
                    ID = '" . $iID . "'";
                    
    $Banco->query( $sql, "load" );
    
    if ( !$row = $Banco->getFetchAssoc( "load" ) ) {
        trigger_error("Registro não encontrado");
        die;
    }
    
    $iCNPJ = $row['CNPJ'];   
    $iIE = $row['IE'];
    $iNome = $row['NOME'];
    $iNomeFantasia = $row['NOME_FANTASIA'];
    $iTelefone = $row['TELEFONE'];
    $iTelefone2 = $row['TELEFONE_2'];
    $iLogradouro = $row['LOGRADOURO'];
    $iEndereco = $row['ENDERECO'];
    $iNumero = $row['NUMERO'];
    $iComplemento = $row['COMPLEMENTO'];
    $iBairro = $row['BAIRRO'];
    $iCEP = $row['CEP'];
    $iCidadeID = $row['CIDADE_ID'];
    $iEstadoID = $row['ESTADO_ID'];
    $iSite = $row['SITE'];
    $iEmail = $row['EMAIL'];
}

$iMsgErro = verifica_erro($iErro);

if ( $iModo == 'inc' || $iModo == 'alt' ) {
    $iComboEstado = '<select name="iEstadoID" id="iEstadoID" class="form-control" >
                         <option value="">Escolha um estado</option>';
    $sql = "SELECT
                    *
            FROM
                    ESTADO
            ORDER BY
                    NOME";
                    
    $Banco->query( $sql, "load" );
    while ( $row_estado = $Banco->getFetchAssoc("load") ) {
        $iComboEstado .= '<option value="' . $row_estado['ID'] . '" ' . (( $row_estado['ID'] == $iEstadoID )? 'selected="selected"':'' ) . '>' . $row_estado['NOME']. '</option>';
    }
    
    $iComboEstado .= '</select>';
    
    $iComboCidade = '<select name="iCidadeID" id="iCidadeID" class="form-control" >
                         <option value="">Escolha uma cidade</option>';
                         
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
    
        $iForm = '<div class="box-typical box-typical-padding">
                    <form id="frm_categoria" action="" method="post">
                     ' . Obrigatorio() . '
                     <fieldset>
                         <div class="row">
                             <div class="form-group col-sm-4">
                                <label for="iNome">
                                Nome: <span class="color-red">*</span>
                                </label>
                                <input type="text" name="iNome" id="iNome" value="' . $iNome . '" class="form-control ' . ( (isset($iErro['iNome']) && $iErro['iNome'] != '') ? 'form-control-error' : '') . '" />
                             </div>
                             <div class="form-group col-sm-4 ' . ( (isset($iErro['iCNPJ']) && $iErro['iCNPJ'] != '') ? 'has-error has-feedback' : '') . '">
                                <label for="iCNPJ">CNPJ:</label>
                                <input type="text" name="iCNPJ" id="iCNPJ" value="' . $iCNPJ . '" class="cnpj form-control"/>
                             </div>
                             <div class="form-group col-sm-4 ' . ( (isset($iErro['iIE']) && $iErro['iIE'] != '') ? 'has-error has-feedback' : '') . '">
                                <label for="iIE">IE:</label>
                                <input type="text" name="iIE" id="iIE" value="' . $iIE . '" class="form-control"/>
                             </div>
                         </div>
                         <div class="form-group ' . ( (isset($iErro['iNomeFantasia']) && $iErro['iNomeFantasia'] != '') ? 'has-error has-feedback' : '') . '">
                            <label for="iNomeFantasia">Nome fantasia:</label>
                            <input type="text" name="iNomeFantasia" id="iNomeFantasia" value="' . $iNomeFantasia . '" class="form-control" />
                         </div>
                         <div class="form-group ' . ( (isset($iErro['iEmail']) && $iErro['iEmail'] != '') ? 'has-error has-feedback' : '') . '">
                            <label for="iEmail">E-mail:</label>
                            <input type="text" name="iEmail" id="iEmail" value="' . $iEmail . '" class="form-control" />
                         </div>
                         <div class="form-group ' . ( (isset($iErro['iSite']) && $iErro['iSite'] != '') ? 'has-error has-feedback' : '') . '">
                            <label for="iSite">Site:</label>
                            <input type="text" name="iSite" id="iSite" value="' . $iSite . '" class="form-control" />
                         </div>
                         <div class="row">
                             <div class="form-group col-sm-3 ' . ( (isset($iErro['iTipoTelefone']) && $iErro['iTipoTelefone'] != '') ? 'has-error has-feedback' : '') . '">
                                <label for="iTipoTelefone">Telefone</label>
                                <select name="iTipoTelefone" id="iTipoTelefone" class="form-control">
                                    <option value="0" ' . (( $iTipoTelefone == '0') ? 'selected="selected"' : "" ) . '>Telefone Fixo</option>
                                    <option value="1" ' . (( $iTipoTelefone == '1') ? 'selected="selected"' : "" ) . '>Celular</option>
                                </select>
                             </div>
                             <div class="form-group col-sm-9 ' . ( (isset($iErro['iTelefone']) && $iErro['iTelefone'] != '') ? 'has-error has-feedback' : '') . '">
                                <label for="iTelefone">Número</label>
                                <input type="text" name="iTelefone" id="iTelefone" value="' . $iTelefone . '" class="telefone form-control"/>
                             </div>
                         </div>
                         <div class="row">
                             <div class="form-group col-sm-3 ' . ( (isset($iErro['iTipoTelefone2']) && $iErro['iTipoTelefone2'] != '') ? 'has-error has-feedback' : '') . '">
                                <label for="iTipoTelefone2">Telefone - Adicional</label>
                                <select name="iTipoTelefone2" id="iTipoTelefone2" class="form-control">
                                    <option value="0" ' . (( $iTipoTelefone == '0') ? 'selected="selected"' : "" ) . '>Telefone Fixo</option>
                                    <option value="1" ' . (( $iTipoTelefone == '1') ? 'selected="selected"' : "" ) . '>Celular</option>
                                </select>
                             </div>
                             <div class="form-group col-sm-9 ' . ( (isset($iErro['iTelefone2']) && $iErro['iTelefone2'] != '') ? 'has-error has-feedback' : '') . '">
                                <label for="iTelefone2">Número</label>
                                <input type="text" name="iTelefone2" id="iTelefone2" value="' . $iTelefone2 . '" class="telefone form-control"/>
                             </div>
                         </div>
                         <div id="box_endereco">
                             <div class="row">
                                 <div class="form-group col-sm-2 ' . ( (isset($iErro['iCEP']) && $iErro['iCEP'] != '') ? 'has-error has-feedback' : '') . '">
                                    <label for="iCEP">CEP:</label>
                                    <input type="iCEP" name="iCEP" id="iCEP" value="' . $iCEP . '" class="cep form-control" />
                                    <a href="http://www.buscacep.correios.com.br/servicos/dnec/index.do" target="_black" >Esqueci meu CEP</a>
                                 </div>
                                  <div class="form-group col-sm-2 ' . ( (isset($iErro['iLogradouro']) && $iErro['iLogradouro'] != '') ? 'has-error has-feedback' : '') . '">
                                         <label for="iLogradouro">Logradouro:</label>
                                          <select name="iLogradouro" id="iLogradouro" class="form-control" >
                                              <option value="Rua"        ' . (( $iLogradouro == 'Rua')         ? 'selected="selected"' : "" ) . '>Rua</option>
                                              <option value="Alameda"    ' . (( $iLogradouro == 'Alameda')     ? 'selected="selected"' : "" ) . '>Alameda</option>
                                              <option value="Área"       ' . (( $iLogradouro == 'Área')        ? 'selected="selected"' : "" ) . '>Área</option>
                                              <option value="Avenida"    ' . (( $iLogradouro == 'Avenida')     ? 'selected="selected"' : "" ) . '>Avenida</option>
                                              <option value="Chácara"    ' . (( $iLogradouro == 'Chácara')     ? 'selected="selected"' : "" ) . '>Chácara</option>
                                              <option value="Condomínio" ' . (( $iLogradouro == 'Condomínio')  ? 'selected="selected"' : "" ) . '>Condomínio</option>
                                              <option value="Estrada"    ' . (( $iLogradouro == 'Estrada')     ? 'selected="selected"' : "" ) . '>Estrada</option>
                                              <option value="Fazenda"    ' . (( $iLogradouro == 'Fazenda')     ? 'selected="selected"' : "" ) . '>Fazenda</option>
                                              <option value="Praça"      ' . (( $iLogradouro == 'Praça')       ? 'selected="selected"' : "" ) . '>Praça</option>
                                              <option value="Residencial"' . (( $iLogradouro == 'Residencial') ? 'selected="selected"' : "" ) . '>Residencial</option>
                                              <option value="Rodovia"    ' . (( $iLogradouro == 'Rodovia')     ? 'selected="selected"' : "" ) . '>Rodovia</option>
                                              <option value="Sítio"      ' . (( $iLogradouro == 'Sítio')       ? 'selected="selected"' : "" ) . '>Sítio</option>
                                              <option value="Travessa"   ' . (( $iLogradouro == 'Travessa')    ? 'selected="selected"' : "" ) . '>Travessa</option>
                                              <option value="Via"        ' . (( $iLogradouro == 'Via')         ? 'selected="selected"' : "" ) . '>Via</option>
                                              <option value="Vila"       ' . (( $iLogradouro == 'Vila')        ? 'selected="selected"' : "" ) . '>Vila</option>
                                              <option value="Outros"     ' . (( $iLogradouro == 'Outros')      ? 'selected="selected"' : "" ) . '>Outros</option>
                                          </select>
                                 </div>
                                 <div class="form-group col-sm-6 ' . ( (isset($iErro['iEndereco']) && $iErro['iEndereco'] != '') ? 'has-error has-feedback' : '') . '">
                                          <label for="iEndereco">Endereço:</label>
                                          <input type="text" name="iEndereco" id="iEndereco" value="' . $iEndereco . '" class="form-control" />
                                 </div>
                                 <div class="form-group col-sm-2 ' . ( (isset($iErro['iNumero']) && $iErro['iNumero'] != '') ? 'has-error has-feedback' : '') . '">
                                          <label for="iNumero">Número:</label>
                                          <input type="text" name="iNumero" id="iNumero" value="' . $iNumero . '" class="form-control" />
                                 </div>
                             </div>
                             <div class="row">
                                 <div class="form-group col-sm-8 ' . ( (isset($iErro['iComplemento']) && $iErro['iComplemento'] != '') ? 'has-error has-feedback' : '') . '">
                                          <label for="iComplemento">Complemento:</label>
                                          <input type="text" name="iComplemento" id="iComplemento" value="' . $iComplemento . '" class="form-control" />
                                </div>
                                <div class="form-group col-sm-4 ' . ( (isset($iErro['iBairro']) && $iErro['iBairro'] != '') ? 'has-error has-feedback' : '') . '">
                                      <label for="iBairro">Bairro:</label>
                                      <input type="text" name="iBairro" id="iBairro" value="' . $iBairro . '" class="form-control" />
                                </div>
                             </div>
                             <div class="row">
                                 <div class="form-group col-sm-6 ' . ( (isset($iErro['iEstadoID']) && $iErro['iEstadoID'] != '') ? 'has-error has-feedback' : '') . '">
                                      <label for="iEstadoID">Estado:</label>
                                      ' . $iComboEstado . '
                                 </div>
                                 <div class="form-group col-sm-6 ' . ( (isset($iErro['iCidadeID']) && $iErro['iCidadeID'] != '') ? 'has-error has-feedback' : '') . '">
                                      <label for="iCidadeID">Cidade:</label>
                                      ' . $iComboCidade . '
                                 </div>
                             </div>
                         </div>
                         <div class="form-group">
                            ' . $iInputs . '
                         </div>
                     </fieldset>
                  </form>
                </div>';
} else {
    
    $sql = "SELECT NOME FROM ESTADO WHERE ID = '" . $iEstadoID . "'";
    $Banco->query( $sql, "load" );
    $row_estado = $Banco->getFetchAssoc("load");
    $iEstadoNome = $row_estado['NOME'];
    
    $sql = "SELECT NOME FROM CIDADE WHERE ID = '" . $iCidadeID . "'";
    $Banco->query( $sql, "load" );
    $row_cidade = $Banco->getFetchAssoc("load");
    $iCidadeNome = $row_cidade['NOME'];
    
    $iForm = '<div class="box-typical box-typical-padding">
                <ol class="list-unstyled">
                   <li>
                       <blockquote>
                           <h4>CNPJ:</h4>
                           <footer>' . $iCNPJ . '</footer>
                       </blockquote>
                   </li>
                   <li>
                       <blockquote>
                           <h4>Nome:</h4>
                           <footer>' . $iNome . '</footer>
                       </blockquote>
                   </li>
                   <li>
                       <blockquote>
                           <h4>Nome fantasia:</h4>
                           <footer>' . $iNomeFantasia . '</footer>
                       </blockquote>
                   </li>
                   <li>
                       <blockquote>
                           <h4>Email:</h4>
                           <footer>' . $iEmail . '</footer>
                       </blockquote>
                   </li>
                   <li>
                       <blockquote>
                           <h4>Site:</h4>
                           <footer>' . $iSite . '</footer>
                       </blockquote>
                   </li>
                   <li>
                       <blockquote>
                           <h4>Telefone:</h4>
                           <footer>' . $iTelefone . '</footer>
                       </blockquote>
                   </li>
                   <li>
                       <blockquote>
                           <h4>Telefone - Adicional:</h4>
                           <footer>' . $iTelefone2 . '</footer>
                       </blockquote>
                   </li>
                   <li>
                       <blockquote>
                          <h4>Endereço:</h4>
                          <footer>' . $iLogradouro . ': ' . $iEndereco . ' - Nº' .  $iNumero . ( ($iComplemento != '') ? ', ' . $iComplemento : '') . ' - ' . $iCEP . ', ' . $iBairro . ', ' . $iCidadeNome . '-' . $iEstadoNome . '</footer>
                     </blockquote>
                   </li>
                   <li class="botoes">
                       ' . $iInputs . '
                   </li>
                </ol>
              </div>';
}

$showBody = '<header class="section-header">
                <div class="tbl">
                    <div class="tbl-row">
                        <div class="tbl-cell">
                            <h3>Fornecedor</h3>
                            ' . layout_bread_crumb() . '
                        </div>
                    </div>
                </div>
            </header>
            <div class="panel panel-default">
                 <div class="panel-heading">
            ' . $iMsgErro . $iForm;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<?=layout_head("Fornecedor")?>
<body lang="pt" xml:lang="pt">
<?=layout_body( $showBody );?>
</body>
</html>
