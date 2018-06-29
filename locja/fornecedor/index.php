<?php
define("SITE_PATH_REL", "../"); 
require(SITE_PATH_REL."include/inicializar.php");

$iPasta = "/fornecedor";
$iTabelaBusca = 'FORNECEDOR';

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
            <div class="navbar-right">
                <p class="add_buttom text-right"><a href="' . $iPasta . '/manu.php?modo=inc" title="Crie um Cliente"><span class="btn btn-success glyphicon glyphicon-plus"></span></a></p>
            </div>
            <section class="card">
                <div class="card-block">
                    <table id="datatable" class="display table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>Cod.</th>
                                <th>Nome</th>
                                <th class="table-icon-cell">
                                    <div align="center">
                                        <i class="font-icon glyphicon glyphicon-cog"></i>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>Cod.</th>
                                <th>Nome</th>
                                <th class="table-icon-cell">
                                    <div align="center">
                                        <i class="font-icon glyphicon glyphicon-cog"></i>
                                    </div>
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </section>';

/*
$sql = "SELECT
                *
        FROM
                FORNECEDOR";
$pg = new Paginacao('paginacao', '10', $sql, '');

$sql = "SELECT
                *
        FROM
                FORNECEDOR
        ORDER BY
                ID ASC" . $pg->getLimit();
                
$Banco->query( $sql, "load" );

$i = 0;
if ( $Banco->getNumRows( "load" ) ) {
    while ( $row = $Banco->getFetchAssoc( "load" ) ) {
        $showBody .= '<tr ' . (( $i++%2 )?'class="escuro"':'') . '>
                         <td>' . $row['ID'] . '</td>
                         <td>' . $row['NOME'] . '</td>
                         <td align="center" width="25%">
                             <a href="' . $iPasta . '/manu.php?modo=det&id=' . $row['ID'] . '" title="Detalhar"><span class="btn btn-info glyphicon glyphicon-search"></span></a>
                             <a href="' . $iPasta . '/manu.php?modo=alt&id=' . $row['ID'] . '" title="Alterar"><span class="btn btn-warning glyphicon glyphicon-edit"></span></a>
                             <a href="' . $iPasta . '/manu.php?modo=exc&id=' . $row['ID'] . '" title="Excluir" onclick="return confirm(\'Tem certeza?\');"><span class="btn btn-danger glyphicon glyphicon-remove"></span></a>
                         </td>
                     </tr>';
    }
} else {
    $showBody .= '<tr ' . (( $i++%2 )?'class="escuro"':'') . '>
                      <td colspan="15" align="center">Nenhum registro encontrado</td>
                  </tr>';
}
                            
$showBody .= "  </table>
              </div>"
              . $pg->getPaginacao();

*/

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<?=layout_head("Fornecedor")?>
<body lang="pt" xml:lang="pt">
<?=layout_body( $showBody );?>
<script>
    $(function() {
        $('#datatable').DataTable( {
            "processing": true,
            "serverSide": true,
            "ajax": "paginacao.php",
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.16/i18n/Portuguese-Brasil.json"
            },
            "ordering": false,
            /*
            "columnDefs": [ {
                "targets": 2,
                "orderable": false
                } 
            ],
            */
            "pageLength": 25
        } );
    });
</script>
</body>
</html>
