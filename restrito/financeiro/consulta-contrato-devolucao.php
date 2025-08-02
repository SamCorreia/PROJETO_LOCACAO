<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__.'/debug.log');
	require_once('../../config.php');
	require_once('../../functions.php');
	$con = new DBConnection();
	verificaLogin(); getData();
?>
<script>
$(function () {
	$('.sel').multiselect({
		buttonClass: 'btn btn-sm', 
		numberDisplayed: 1,
		maxHeight: 500,
		includeSelectAllOption: true,
		selectAllText: "Selecionar todos",
		enableFiltering: true,
		enableCaseInsensitiveFiltering: true,
		selectAllValue: 'multiselect-all',
		buttonWidth: '100%'
	}); 
	
	$.fn.dataTable.ext.errMode = 'none';
    $('#resultadoConsulta').DataTable({
		"paging": true,
		"pageLength": 50,
		"lengthChange": false,
		"searching": true,
		"ordering": true,
		"info": false,
		"bAutoWidth": false, 
		"columnDefs": [
		    { "orderable": false, "targets": [ -1,-2 ] }
		],
		"order": [ 0, 'desc' ]
    });
	});
</script>
<?php
	if(isset($ac)){
		if($ac=='buscar'){
			if($data_inicial == ''){ $data_inicial = '0001-01-01';}
			if($data_final == ''){ $data_final = $todayTotal; }
			echo '<div class="box box-widget">
			<table id="resultadoConsulta" class="table table-bordered table-striped">
				<thead>
					<tr class="small">
						<th>N</th>
						<th>Empresa:</th>
						<th>Data:</th>
						<th>Valor:</th>
						<th>Editar:</th>
						<th>Excluir:</th>
					</tr>
				</thead> 
			<tbody>';
			$stm = $con->prepare("SELECT contrato_dados.*, localferramentas_cadastroempresa.razao_social, localferramentas_cadastroempresa.cnpj FROM contrato_dados INNER JOIN localferramentas_cadastroempresa ON contrato_dados.empresa = localferramentas_cadastroempresa.id WHERE (contrato_dados.data_contrato BETWEEN ? and ?) AND (razao_social LIKE '%$buscar%' OR cnpj LIKE '%$buscar%') ORDER BY id DESC");
			$stm->execute(array("$data_inicial", "$data_final"));
			$total_vlr_g = 0;
			while($all = $stm->fetch())
			{
				echo '<tr id="thisTr'.$all['id'].'">';
				echo '<td>'.$all['id'].'</td>';
				echo '<td>'.$all['cnpj'].' - '.$all['razao_social'].'</td>';
				$stmvlr = $con->prepare("SELECT * FROM contrato_itens WHERE id_contrato = ?");
				$stmvlr->execute(array($all['id']));
				$total_vlr = 0;
				while($s = $stmvlr->fetch())
				{
					$total_vlr += $s['vlr'];
				}
				$total_vlr_g += $total_vlr;
				echo '<td data-sort="'.$total_vlr.'" align="center">R$&nbsp;'.number_format($total_vlr,2,",",".").'</td>';
				echo '<td>'.implode("/",array_reverse(explode("-",$all['data_contrato']))).'</td>';
				echo '
					<td id="thisTd'.$all['id'].'" align="center">
						<a href="#" onclick=\'$(".resultado").load("financeiro/editar-contrato-devolucao.php?id_contrato='.$all['id'].'")\' class="btn btn-success btn-xs"><span class="glyphicon glyphicon-plus"></span> Editar </a>
					</td>';
				if($acesso_usuario == 'MASTER'){
					echo '
					<td align="center">
						<a href="#" onclick=\'$(".modal-body").load("financeiro/del/excluir-contrato.php?&id='.$all['id'].'")\' data-toggle="modal" data-target="#myModal2"  class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-trash"></span></a>
					</td>';
				}else{
					echo '<td align="center">
						<a href="#" class="btn btn-danger btn-xs disabled"><span class="glyphicon glyphicon-trash"></span></a>
					</td>';
				}
				echo '</tr>';
			}
			echo '</tbody>';
			echo '<tfoot>';
				echo '<tr class="active"><td colspan="3" align="right"><b>Valor Total:&nbsp;&nbsp;&nbsp;</b></td><td colspan="3" align="center"><b>R$&nbsp;'.number_format($total_vlr_g,2,",",".").'</b></td></tr>';
			echo '</tfoot>';
			echo '</table> </div>';
			exit;
		}
	}
?>
	<div class="buttons-top-page">
		<a href="#" style="padding:3px 15px;" title="Cadastrar Novo" class="btn btn-success btn-sm" onclick="ldy('financeiro/cadastro-contrato.php','.conteudo')"><i class="fa fa-plus-circle" aria-hidden="true"></i> Cadastrar</a>				
		<a href="#" style="padding:3px 15px; margin:0px 10px;" title="Atualizar Pagina" class="btn btn-warning btn-sm" onclick="ldy('financeiro/consulta-contrato-devolucao.php','.conteudo')"><i class="fa fa-refresh" aria-hidden="true"></i> Atualizar</a>
	</div>
	<div style="clear: both;">
		<hr></hr>
	</div>
<section class="content-header">
	<h1>Consulta Contratos <small> Devolução.</small></h1>
</section>
<section class="container-fluid">
	<div class="box box-widget box-fix-layout" style="padding-top:5px">
		<div class="box-body">
			<form action="javascript:void(0)" class="form-inline" onSubmit="post(this,'financeiro/consulta-contrato-devolucao.php?ac=buscar','.resultado')">
				<div class="col-lg-4 col-md-5 col-xs-12">
					<label style="width:100%"><small>Buscar: </small>
						<input type="text" name="buscar" placeholder="Buscar CNPJ ou Razao Social" class="form-control" style="width:100%"/>
					</label>
				</div>
				<div class="col-lg-2 col-md-3 col-xs-12">
					<label style="width:100%"><small>Data: </small>
						<input type="date" name="data_inicial" class="form-control" style="width:100%"/>
					</label>
				</div>
				<div class="col-lg-2 col-md-3 col-xs-12">
					<label style="width:100%"><small><br/></small>
						<input type="date" name="data_final"  class="form-control" style="width:100%"/>
					</label>
				</div>
				<div class="col-lg-2 col-md-12 col-xs-12">
					<br/>
					<input type="submit" class="btn btn-success btn-sm" value="Pesquisar" style="width:100%;"/>
				</div>
			</form>
		</div>
	</div>
	<div class="resultado"> </div>
</section>

<div class="modal" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="width:auto;">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" onclick="$('.modal').modal('hide')" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="myModalLabel">Tem certeza disso?</h4>
			</div>
			<div class="modal-body">
				Aguarde um momento &nbsp;&nbsp; <img src="../style/img/loading.gif" alt="Carregando" width="20px"/>
			</div>
		</div>
	</div>
</div>
<div class="modal" id="myModal3" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="width:auto;">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" onclick="$('.modal').modal('hide')" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="myModalLabel">Tem certeza disso?</h4>
			</div>
			<div class="modal-body">
				Aguarde um momento &nbsp;&nbsp; <img src="../style/img/loading.gif" alt="Carregando" width="20px"/>
			</div>
		</div>
	</div>
</div>