<?php
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
		if($ac=='atualizar'){
			$status_comp = $con->query("SELECT status FROM orcamento_dados WHERE id = '$id_recup'")->fetchColumn();
			if($status_comp == '0'){
				echo '<a href="#" onclick=\'$(".resultado").load("financeiro/editar-orcamento.php?id_orcamento='.$id_recup.'")\'  class="btn btn-success btn-xs"><span class="glyphicon glyphicon-plus"></span> Editar / Adicionar </a>';
			}else if($status_comp == '1'){
				echo '<a href="#" onclick=\'$(".resultado").load("financeiro/editar-orcamento-aprovado.php?id_orcamento='.$id_recup.'")\' class="btn btn-warning btn-xs"><span class="glyphicon glyphicon-eye-open"></span> Visualizar </a>';
			}else if($status_comp == '2'){
				echo '<a href="#" class="btn btn-danger btn-xs" style="opacity:0.7" disabled><span class="glyphicon glyphicon-plus"></span> Visualizar </a>';
			}
			exit;
		}
		if($ac=='buscar'){
			if(empty($st)){ 
				echo '<span class="text-danger">Selecione todos os campos obrigatorios</span>'; 
				exit; 
			}else{
				$catg = ''; $subg = ''; $sta = ''; $sit = '';
				foreach($st as $sts)  { $sta .= $sts.',';   } $sta = substr($sta,0,-1);
			}
			echo '<div class="box box-widget">
			<table id="resultadoConsulta" class="table table-bordered table-striped">
				<thead>
					<tr class="small">
						<th>N</th>
						<th>Empresa:</th>
						<th>Assunto:</th>
						<th>Data:</th>
						<th>Status:</th>
						<th>Editar:</th>
						<th>Excluir:</th>
					</tr>
				</thead> 
			<tbody>';
			$stm = $con->prepare("SELECT orcamento_dados.*, localferramentas_cadastroempresa.razao_social, localferramentas_cadastroempresa.cnpj FROM orcamento_dados INNER JOIN localferramentas_cadastroempresa ON orcamento_dados.empresa = localferramentas_cadastroempresa.id WHERE (orcamento_dados.data BETWEEN ? and ?) AND (razao_social LIKE '%$buscar%' OR cnpj LIKE '%$buscar%') AND orcamento_dados.status IN($sta)");
			$stm->execute(array("$data_inicial", "$data_final"));
			while($all = $stm->fetch())
			{
				echo '<tr id="thisTr'.$all['id'].'">';
				echo '<td>'.$all['id'].'</td>';
				echo '<td>'.$all['cnpj'].' - '.$all['razao_social'].'</td>';
				echo '<td>'.$all['assunto'].'</td>';
				echo '<td>'.implode("/",array_reverse(explode("-",$all['data']))).'</td>';
				if($all['status'] == '0') {
					echo '<td align="center"><a href="#" onclick=\'$(".modal-body").load("financeiro/del/modal-aprovar.php?&id='.$all['id'].'")\' data-toggle="modal" data-target="#myModal3"  class="btn btn-primary btn-cor2 btn-xs"><i class="fas fa-thumbs-up"></i> Status</a></td>';
				}else if($all['status'] == '1'){
					echo '<td align="center"><span class="text-success" style="font-size:10px; font-weight:bold;">APROVADO</span></td>';
				}else if($all['status'] == '2'){
					echo '<td align="center"><span class="text-danger" style="font-size:10px; font-weight:bold;">REPROVADO</span></td>';
				}
				if($all['status'] == '0'){
					echo '
					<td id="thisTd'.$all['id'].'" align="center">
						<a href="#" onclick=\'$(".resultado").load("financeiro/editar-orcamento.php?id_orcamento='.$all['id'].'")\' class="btn btn-success btn-xs"><span class="glyphicon glyphicon-plus"></span> Editar / Adicionar </a>
					</td>';
				}else if($all['status'] == '1'){
					echo '
					<td id="thisTd'.$all['id'].'" align="center">
						<a href="#" onclick=\'$(".resultado").load("financeiro/editar-orcamento-aprovado.php?id_orcamento='.$all['id'].'")\' class="btn btn-warning btn-xs"><span class="glyphicon glyphicon-eye-open"></span> Visualizar </a>
					</td>';	
				}else if($all['status'] == '2'){
					echo '
					<td id="thisTd'.$all['id'].'" align="center">
						<a href="#" class="btn btn-danger btn-xs" style="opacity:0.6" disabled><span class="glyphicon glyphicon-plus"></span> Visualizar </a>
					</td>';
				}
				echo '
				<td align="center">
					<a href="#" onclick=\'$(".modal-body").load("financeiro/del/excluir-orcamento.php?&id='.$all['id'].'")\' data-toggle="modal" data-target="#myModal2"  class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-trash"></span></a>
				</td>';
				echo '</tr>';
			}
			echo '</tbody> </table> </div>';
			exit;
		}
	}
?>
	<div class="buttons-top-page">
		<a href="#" style="padding:3px 15px;" title="Cadastrar Novo" class="btn btn-success btn-sm" onclick="ldy('financeiro/cadastro-orcamento.php','.conteudo')"><i class="fa fa-plus-circle" aria-hidden="true"></i> Cadastrar</a>				
		<a href="#" style="padding:3px 15px; margin:0px 10px;" title="Atualizar Pagina" class="btn btn-warning btn-sm" onclick="ldy('financeiro/consulta-orcamento.php','.conteudo')"><i class="fa fa-refresh" aria-hidden="true"></i> Atualizar</a>
	</div>
	<div style="clear: both;">
		<hr></hr>
	</div>
<section class="content-header">
	<h1>Consulta Orçamento<small> Consulte, edite e exclua, orçamentos.</small></h1>
</section>
<section class="container-fluid">
	<div class="box box-widget box-fix-layout" style="padding-top:5px">
		<div class="box-body">
			<form action="javascript:void(0)" class="form-inline" onSubmit="post(this,'financeiro/consulta-orcamento.php?ac=buscar','.resultado')">
				<div class="col-lg-4 col-md-5 col-xs-12">
					<label style="width:100%"><small>Buscar: </small>
						<input type="text" name="buscar" placeholder="Buscar CNPJ ou Razao Social" class="form-control" style="width:100%"/>
					</label>
				</div>
				<div class="col-lg-2 col-md-3 col-xs-12">
					<label style="width:100%"><small>Data: </small>
						<input type="date" name="data_inicial" value="<?php echo $inicioMes; ?>" class="form-control" style="width:100%"/>
					</label>
				</div>
				<div class="col-lg-2 col-md-3 col-xs-12">
					<label style="width:100%"><small><br/></small>
						<input type="date" name="data_final" value="<?php echo $todayTotal; ?>" class="form-control" style="width:100%"/>
					</label>
				</div>
				<div class="col-xs-12 col-md-2" style="padding:2px;">
						<label style="width:100%"><small>Status:</small>
							<select name="st[]" class="sel" multiple="multiple">
								<option value="0" selected>Em Aberto</option>
								<option value="1" selected>Aprovado</option>
								<option value="2" selected>Reprovado</option>
							</select>
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