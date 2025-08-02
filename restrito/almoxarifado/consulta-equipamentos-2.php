<?php
// Configuração de erros
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__.'/php_errors.log');

// Conexão com banco de dados e includes
require_once('../../config.php');
require_once('../../functions.php');

// Verifica login e obtém dados
$con = new DBConnection();
verificaLogin(); 
getData();

// Verifica se há ação a ser processada
if(isset($_GET['ac'])) {
    $ac = $_GET['ac'];
    $relatorio = isset($_POST['relatorio']) ? $_POST['relatorio'] : null;
    $busca = isset($_POST['busca']) ? $_POST['busca'] : '';
    
    // Processa os filtros
    $cat = isset($_POST['cat']) ? $_POST['cat'] : array();
    $sub = isset($_POST['sub']) ? $_POST['sub'] : array();
    $to = isset($_POST['to']) ? $_POST['to'] : array();
    $si = isset($_POST['si']) ? $_POST['si'] : array();
    $ct = isset($_POST['ct']) ? $_POST['ct'] : array();

    // Validação dos campos obrigatórios
    if($relatorio == '1' && (empty($cat) || empty($sub) || empty($to) || empty($si) || empty($ct))) {
        echo '<span class="text-danger">Selecione todos os campos obrigatórios</span>';
        exit;
    } elseif($relatorio == '2' && (empty($cat) || empty($sub) || empty($to) || empty($si))) {
        echo '<span class="text-danger">Selecione todos os campos obrigatórios</span>';
        exit;
    }

    // Prepara os valores para a consulta SQL
    $catg = implode(",", $cat);
    $subg = implode(",", $sub);
    $sta = implode(",", $to);
    $sit = implode(",", $si);
    $ctt = ($relatorio == '1') ? implode(",", $ct) : '';

    // Cabeçalho do relatório
    $header = '
    <div class="container-fluid hidden-xs visible-print" style="border-bottom:1px solid #CCC; padding-bottom:20px; margin:15px;">
        <div class="col-xs-2" style="padding:0px">
            <img src="../style/img/litoralrent-logo.png" class="img-responsive" width="100px" />
        </div>
        <div class="col-xs-10" style="text-align:right; font-size:8px">
            <b><small>LITORAL RENT LOCADORA E CONSTRUÇÕES LTDA.</small></b><br/>
            Av Antônio Emmerick, 723, Jardim Guassu, São Vicente/SP - CEP 11370-001<br/>
            Telefone: (13) 3043-4211 &nbsp;&nbsp;&nbsp; Email: contato@litoralrent.com.br
        </div>
    </div>
    <center>
        <h5 class="hidden-xs visible-print" style="font-family: \'Oswald\', sans-serif; letter-spacing:4px; text-align:center; margin-bottom:20px;">
            <p><small>RELATÓRIO DE PATRIMÔNIO</small> <br/> <small>NOTA: '.htmlspecialchars($busca).'</small></p>
        </h5>
    </center>';

    // Relatório Simples
    if($relatorio == '1') {
        echo $header;
        
        // Consulta SQL com prepared statement
        $sql = "SELECT * FROM notas_equipamentos 
                WHERE desconto LIKE ? 
                AND categoria IN($catg) 
                AND sub_categoria IN($subg) 
                AND status IN($sta) 
                AND situacao IN($sit) 
                AND controle IN($ctt)";
        
        $stm = $con->prepare($sql);
        $stm->execute(["%$busca%"]);
        
        echo '<div class="box box-widget">
                <table id="resultadoConsulta" class="box box-widget table table-striped table-min small" style="font-size:10px">
                <thead>
                    <tr>
                        <th style="text-align:center"><i class="fa fa-list-alt" aria-hidden="true"></i></th>
                        <th style="text-align:center">BP:</th>
                        <th style="text-align:center">Fornecedor:</th>
                        <th style="text-align:center">Motor:</th>
                        <th style="text-align:center">Chassi:</th>
                        <th style="text-align:center">Nota:</th>
                        <th style="text-align:center">Sub-Categoria:</th>
                        <th style="text-align:center">Valor:</th>
                        <th style="text-align:center">Status:</th>';
        
        if($acesso_usuario == 'MASTER') {
            echo '<th class="hidden-print" style="text-align:center">Editar:</th>';
        }
        
        echo '</tr>
            </thead> 
            <tbody>';
        
        $c = 0;
        while($b = $stm->fetch()) {
            $c++;
            echo '<tr id="thisTr'.$b['id'].'">';
            echo '<td style="text-align:center">'.$c.'</td>';
            echo '<td>'.htmlspecialchars($b['patrimonio']).'</td>';
            echo '<td>'.htmlspecialchars($b['obs']).'</td>';
            echo '<td>'.htmlspecialchars($b['placa']).'</td>';
            echo '<td>'.htmlspecialchars($b['chassi']).'</td>';
            echo '<td>'.htmlspecialchars($b['desconto']).'</td>';
            
            // Sub-categoria
            $subCat = $con->query("SELECT descricao FROM notas_cat_sub WHERE id = ".$b['sub_categoria'])->fetchColumn();
            echo '<td>'.htmlspecialchars($subCat).'</td>';
            
            echo '<td data-sort="'.$b['valor'].'">R$&nbsp;'.number_format($b['valor'], 2, ',', '.').'</td>';
            
            echo '<td style="text-align:center">';
            if($b['status'] == 0) {
                echo '<span class="label label-success">ATIVO</span>'; 
            } elseif($b['status'] == 1) { 
                echo '<span class="label label-danger">INATIVO</span>'; 
            } elseif($b['status'] == 2) {
                echo '<span class="label label-warning">ROUBADO</span>'; 
            }
            echo '</td>';
            
            if($acesso_usuario == 'MASTER') {
                echo '<td class="hidden-print" style="text-align:center" width="5px">
                        <a href="#" onclick="$(\'.modal-body\').load(\'almoxarifado/editar-equipamento-master.php?id='.$b['id'].'\')" 
                           data-toggle="modal" data-target="#myModal" 
                           class="btn btn-success btn-xs hidden-print" style="margin:0px; font-weight:bold;">
                           <span class="glyphicon glyphicon-pencil"></span>
                        </a>
                      </td>';
            }
            
            echo '</tr>';
        }
        
        echo '</tbody></table></div>';
        exit;
    }
    
    // Relatório Detalhado
    if($relatorio == '2') {
        echo $header;
        
        $sql = "SELECT * FROM notas_equipamentos 
                WHERE desconto LIKE ? 
                AND categoria IN($catg) 
                AND sub_categoria IN($subg) 
                AND status IN($sta) 
                AND situacao IN($sit)";
        
        $stm = $con->prepare($sql);
        $stm->execute(["%$busca%"]);
        
        $c = 0;
        while($b = $stm->fetch()) {
            $c++;
            extract($b);
            
            echo '<div class="box box-widget">
                    <table class="box box-widget table table-striped table-condensed" style="font-size:12px; border:1px solid #ccc;">
                    <thead>
                        <tr>
                            <th style="text-align:center">'.$con->query("SELECT razao_social FROM localferramentas_cadastroempresa WHERE id = $empresa")->fetchColumn().'</th>
                            <th style="text-align:center"><small>Categoria: &nbsp;</small> '.$con->query("SELECT descricao FROM notas_cat_e WHERE id = $categoria")->fetchColumn().'</th>
                            <th style="text-align:center"><small>Sub-Categoria: &nbsp; </small>'.$con->query("SELECT descricao FROM notas_cat_sub WHERE id = $sub_categoria")->fetchColumn().'</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td width="30%"><small><b>Nº Motor:</b></small> '.htmlspecialchars($placa).'</td>
                            <td width="30%"><small><b>Marca:</b></small> '.htmlspecialchars($marca).'</td>
                            <td width="30%"><small><b>BP:</b></small> '.htmlspecialchars($patrimonio).'</td>
                        </tr>
                        <tr>
                            <td><small><b>Chassi: </b></small>'.htmlspecialchars($chassi).'</td>
                            <td><small><b>Valor: </b></small> R$ '.number_format($valor, 2, ',', '.').'</td>
                            <td><small><b>Tipo: </b></small>'.$con->query("SELECT descricao FROM notas_eq_situacao WHERE id = $situacao")->fetchColumn().'</td>
                        </tr>';
            
            $status_print = ($status == 0) ? 'ATIVO' : 'INATIVO';
            $entrada_formatada = implode('/', array_reverse(explode('-', $entrada)));
            $saida_formatada = !empty($saida) ? implode('/', array_reverse(explode('-', $saida))) : '';
            
            echo '<tr>
                    <td><small><b>Nº Apólice: </b></small>'.htmlspecialchars($patrimonio2).'</td>
                    <td><small><b>Tipo: </b></small>'.$status_print.'</td>
                    <td><small><b>Entrada: </b></small>'.$entrada_formatada.' - <small><b>Saída: </b></small>'.$saida_formatada.'</td>
                  </tr>
                  <tr>
                    <td><small><b>Chassi / Nº série: </b></small>'.htmlspecialchars($chassi).'</td>
                    <td><small><b>Ano: </b></small>'.htmlspecialchars($ano).'</td>
                    <td><small><b>Nota: </b></small>'.htmlspecialchars($desconto);
            
            if($acesso_usuario == 'MASTER') {
                echo '<a href="#" onclick="$(\'.modal-body\').load(\'almoxarifado/editar-equipamento-master.php?id='.$id.'\')" 
                       data-toggle="modal" data-target="#myModal" 
                       class="pull-right btn btn-warning btn-xs hidden-print" style="margin:0px; font-weight:bold;">
                       <span class="glyphicon glyphicon-pencil"></span>
                     </a>';
            }
            
            echo '</td></tr></tbody></table></div>';
        }
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Consulta de Equipamentos</title>
    <style>
        @media print {
            .hidden-print { display: none !important; }
        }
    </style>
</head>
<body>
<section class="content">
    <div class="buttons-top-page hidden-print">
        <?php if($acesso_usuario == 'MASTER') { ?>
            <a href="#" style="padding:3px 15px;" title="Cadastrar Novo" class="btn btn-success btn-sm" onclick="$('.modal-body').load('almoxarifado/cadastro-equipamentos.php')" data-toggle="modal" data-target="#myModal">
                <i class="fa fa-plus-circle" aria-hidden="true"></i> Cadastrar
            </a>	
        <?php } ?>
        <a href="#" style="padding:3px 15px; margin:0px 10px;" title="Atualizar Página" class="btn btn-warning btn-sm" onclick="ldy('almoxarifado/consulta-equipamentos-2.php','.conteudo')">
            <i class="fa fa-refresh" aria-hidden="true"></i> Atualizar
        </a>
        <a href="javascript:window.print()" style="padding:3px 15px; margin:0px 10px;" class="hidden-xs hidden-print pull-right btn btn-warning btn-sm">
            <span class="glyphicon glyphicon-print" aria-hidden="true"></span>&nbsp;Imprimir
        </a>
    </div>
    <div class="hidden-print" style="clear: both;">
        <hr>
    </div>
    
    <form action="javascript:void(0)" id="form1" class="hidden-print">
        <div class="well well-sm" style="padding:10px 10px 5px 10px;">
            <div class="container-fluid">
                <div class="col-xs-12 col-md-3" style="padding:0px 5px">
                    <label style="width:100%"><small>Nota:</small> <br/>
                        <input type="text" name="busca" placeholder="Nota fiscal" class="form-control input-sm">
                    </label>
                </div>
                <div class="col-xs-12 col-md-2" style="padding:2px">
                    <label style="width:100%"><small>Relatório:</small> <br/>
                        <select name="relatorio" class="form-control input-sm" style="width:100%">
                            <option value="1">SIMPLES</option>
                            <option value="2">DETALHADA</option>
                        </select>
                    </label>
                </div>
                <div class="col-xs-12 col-md-2" style="padding:2px">
                    <label style="width:100%"><small>Categoria:</small>
                        <select name="cat[]" onChange="$('#itens_categoria').load('../functions/functions-load.php?atu=categoria&control=2&categoria=' + $(this).val() + '');" class="sel" multiple="multiple" required> 
                            <?php 
                            $stms = $con->query("SELECT * FROM notas_cat_e WHERE oculto = '0' ORDER BY descricao ASC");
                            while($l = $stms->fetch()) {
                                echo '<option value="'.$l['id'].'" selected>'.htmlspecialchars($l['descricao']).'</option>'; 
                            }
                            ?>		
                        </select>
                    </label>
                </div>
                <div class="col-xs-12 col-md-2" style="padding:2px;">
                    <div id="itens_categoria">
                        <label style="width:100%"><small>Sub-Categoria:</small><br/>
                            <select name="sub[]" class="sel" multiple="multiple" required>
                                <?php 
                                $stms = $con->query("SELECT * FROM notas_cat_sub ORDER BY descricao ASC");
                                while($l = $stms->fetch()) {
                                    echo '<option value="'.$l['id'].'" selected>'.htmlspecialchars($l['descricao']).'</option>'; 
                                }
                                ?>		
                            </select>
                        </label>
                    </div>
                </div>
                <div class="col-xs-12 col-md-2" style="padding:2px;">
                    <label style="width:100%"><small>Tipo: </small>
                        <select name="si[]" class="sel" multiple="multiple">
                            <?php
                            $stms = $con->query("SELECT * FROM notas_eq_situacao WHERE status = '0' ORDER BY descricao ASC");
                            while($l = $stms->fetch()) {
                                echo '<option value="'.$l['id'].'" selected>'.htmlspecialchars($l['descricao']).'</option>';
                            }
                            ?>
                        </select>
                    </label>
                </div>
                <div class="col-xs-12 col-md-2" style="padding:2px;">
                    <label style="width:100%"><small>Situação: </small>
                        <select name="to[]" class="sel" multiple="multiple">
                            <option value="0" selected>ATIVO</option>
                            <option value="1" selected>INATIVO</option>
                            <option value="2" selected>ROUBADO</option>
                        </select>
                    </label>
                </div>
                <div class="col-xs-12 col-md-2" style="padding:2px;">
                    <label style="width:100%"><small>Status: </small>
                        <select name="ct[]" class="sel" multiple="multiple">
                            <option value="0" selected>DISPONÍVEL</option>
                            <option value="1" selected>LOCADO</option>
                        </select>
                    </label>
                </div>
                <div class="col-xs-12 col-md-2" style="padding:2px;">
                    <label><br/>
                        <input type="submit" value="Pesquisar" style="width:150px; margin-left:10px;" onClick="post('#form1','almoxarifado/consulta-equipamentos-2.php?ac=consulta','.retorno')" class="btn btn-success btn-sm">
                    </label>
                </div>
            </div>
        </div>
    </form>
    
    <div class="retorno"></div>
</section>

<!-- Modal para edição -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width:90%;">
        <div class="modal-content"> 
            <div class="modal-header box box-info" style="margin:0px;">
                <button type="button" class="close" onclick="$('.modal').modal('hide'); $('.modal-body').empty()" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Painel Administrativo</h4>
            </div>
            <div class="modal-body">
                Aguarde um momento &nbsp;&nbsp; <img src="../style/img/loading.gif" alt="Carregando" width="20px">
            </div>
        </div>
    </div>
</div>

<!-- Modal secundário -->
<div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" onclick="$('.modal').modal('hide')" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Excluir Usuário</h4>
            </div>
            <div class="modal-body">
                Aguarde um momento &nbsp;&nbsp; <img src="../../imagens/loading.gif" alt="Carregando" width="20px">
            </div>
        </div>
    </div>
</div>

<!-- Scripts JavaScript -->
<script>
$(function () {
    $('.sel').multiselect({
        buttonClass: 'btn btn-sm', 
        numberDisplayed: 1,
        maxHeight: 500,
        includeSelectAllOption: true,
        selectAllText: 'Selecionar todos',
        enableFiltering: true,
        enableCaseInsensitiveFiltering: true,
        selectAllValue: 'multiselect-all',
        buttonWidth: '100%'
    }); 
    
    $('#resultadoConsulta').DataTable({
        paging: false,
        lengthChange: false,
        searching: true,
        ordering: true,
        info: false,
        autoWidth: false,
        columnDefs: [
            { orderable: false, targets: [ -1 ] }
        ]
    });
});
</script>
</body>
</html>