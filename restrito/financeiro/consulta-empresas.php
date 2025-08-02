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
    $('#resultadoConsulta').DataTable({
		"paging": false,
		"lengthChange": false,
		"searching": true,
		"ordering": true,
		"info": false,
		"bAutoWidth": false,
		"columnDefs": [
		    { "orderable": false, "targets": [ -1,-2 ] }
		]
    });
	});
</script>
	<?php
	if(isset($ac)){
		if(@$relatorio == '1'){
			if(!isset($inicial)){ $inicial = '0000-00-00'; }
			if(!isset($final)){ $final = $todayTotal; }
			if(!isset($busca)){ $busca = ''; }
			if(!isset($ti)){
				$ti = array(
					"0" => "0",
					"1" => "1",
					"2" => "2",
					"3" => "3",
				);
			}
			foreach($ti as $tis) { @$tiu .= $tis.','; } $tiu = substr($tiu,0,-1);
			echo '<div class="box box-widget">
					<table id="resultadoConsulta" class="box box-widget table table-bordered table-striped table-min small" style="font-size:10px">
					<thead>
						<tr>
							<th style="text-align:center"><i class="fa fa-list-alt" aria-hidden="true"></i></th>
							<th style="text-align:center">CNPJ</th>
							<th style="text-align:center">Razão Social</th>
							<th style="text-align:center">Telefone</th>
							<th style="text-align:center">Celular</th>
							<th style="text-align:center">Contato</th>
							<th style="text-align:center">Email</th>
							<th style="text-align:center">Editar</th>
						</tr>
					</thead> 
				<tbody>';
				$stm = $con->prepare("SELECT * FROM localferramentas_cadastroempresa WHERE razao_social LIKE '%$busca%' AND tipo_empresa IN($tiu) AND (data_retorno BETWEEN '$inicial' AND '$final')");
				$stm->execute();
				$c = 0;
				while($b = $stm->fetch())
				{
					$c += 1;
					echo '<tr id="thisTr'.$b['id'].'">';
					echo '<td style="text-align:center">'.$c.'</td>';
					echo '<td width="15%">'.$b['cnpj'].'</td>';
					echo '<td>'.strtoupper($b['razao_social']).'</td>';
					echo '<td>'.$b['telefone'].'</td>';
					echo '<td>'.$b['celular'].'</td>';
					echo '<td>'.$b['contato'].'</td>';
					echo '<td>'.$b['email'].'</td>';
					//if($id_usuario_logado == '1'){
					echo '<td width="5px"><a href="#" Onclick=\'$(".modal-body").load("financeiro/editar-empresa.php?id='.$b['id'].'")\' data-toggle="modal" data-target="#myModal" class="btn btn-success btn-xs" style="margin:0px; font-weight:bold;"><span class="glyphicon glyphicon-pencil"></span></a></td>';
					echo '</tr>';
				}
				echo '</tbody> </table> </div>';
			exit;
		}
		//DETALHADO
		if(@$relatorio == '2'){
			if(!isset($inicial)){ $inicial = '0000-00-00'; }
			if(!isset($final)){ $final = $todayTotal; }
			if(!isset($busca)){ $busca = ''; }
			if(!isset($ti)){
				$ti = array(
					"0" => "0",
					"1" => "1",
					"2" => "2",
					"3" => "3",
				);
			}
			foreach($ti as $tis) { @$tiu .= $tis.','; } $tiu = substr($tiu,0,-1);
				$stm = $con->prepare("SELECT * FROM litoralrent_cadastroempresa WHERE razao_social LIKE '%$busca%' AND tipo_empresa IN($tiu)");
				$stm->execute();
				$c = 0;
				while($b = $stm->fetch())
				{
					$c += 1;
					echo '<div class="box box-widget">
					<table class="box box-widget table table-striped table-condensed small" style="font-size:10px; border:1px solid #ccc;">
					<thead>
						<tr>
							<th>'.$c.' <i class="fa fa-list-alt" aria-hidden="true"></i> &nbsp;&nbsp;| &nbsp;&nbsp;CNPJ: '.$b['cnpj'].'</th>
							<th style="text-align:center" colspan="2"><b>'.strtoupper($b['razao_social']).'</b></th>
							<th style="text-align:center">Data: <b>'.implode("/",array_reverse(explode("-",$b['data_retorno']))).'</b> <a href="#" Onclick=\'$(".modal-body").load("financeiro/editar-empresa.php?id='.$b['id'].'")\' data-toggle="modal" data-target="#myModal" class="btn btn-warning btn-xs pull-right" style="margin:0px; font-weight:bold; font-size:8px;"><span class="glyphicon glyphicon-pencil"></span></a></th>
						</tr>
					</thead> 
					<tbody>
						<tr>
							<td><b>Telefone:</b>'.$b['telefone'].'</td>
							<td><b>Endereço:</b>'.$b['endereco'].'</td>
							<td><b>Contato:</b>'.$b['contato'].'</td>
							<td><b>Email:</b>'.$b['email'].'</td>
						</tr>
						<tr>
							<td><b>Celular:</b>'.$b['celular'].'</td>
							<td><b>Seguimento:</b>'.$b['seguimento'].'</td>
							<td><b>Visita:</b>'.$b['visita'].'</td>
							<td> - </td>
						</tr>
						<tr>
							<td colspan="4"><b>Observações</b>: '.$b['obs'].'</td>
						</tr>';
					echo '</tbody> </table> </div>';
				}
			exit;
		}
	}
	?>

<section class="content">
	<div class="buttons-top-page">
		<a href="#" style="padding:3px 15px;" title="Cadastrar Novo" class="btn btn-success btn-sm" onclick='$(".modal-body").load("financeiro/cadastro-empresa.php")' data-toggle="modal" data-target="#myModal"><i class="fa fa-plus-circle" aria-hidden="true"></i> Cadastrar</a>				
		<a href="#" style="padding:3px 15px; margin:0px 10px;" title="Atualizar Pagina" class="btn btn-warning btn-sm" onclick="ldy('financeiro/consulta-empresas.php','.conteudo')"><i class="fa fa-refresh" aria-hidden="true"></i> Atualizar</a>
	</div>
	<div style="clear: both;">
		<hr></hr>
	</div>
	<form action="javascript:void(0)" id="form1" class="hidden-print">
		<div class="well well-sm" style="padding:10px 10px 5px 10px;">
			<div class="container-fluid">
				<div class="col-xs-12 col-md-5" style="padding:0px 5px">
					<label for="" style="width:100%"><small>Buscar:</small> <br/>
						<input type="text" name="busca" placeholder="Nome da empresa" class="form-control input-sm" />
					</label>
				</div>
				<div class="col-xs-12 col-md-2" style="padding:0px 5px">
					<label for="" style="width:100%"><small>De:</small> <br/>
						<input type="date" name="inicial" class="form-control input-sm" />
					</label>
				</div>
				<div class="col-xs-12 col-md-2" style="padding:0px 5px">
					<label for="" style="width:100%"><small>ate:</small> <br/>
						<input type="date" name="final" class="form-control input-sm" />
					</label>
				</div>
				<div class="col-xs-12 col-md-2" style="padding:0px 5px">
					<label for="" style="width:100%"><small>Tipo Empresa:</small> <br/>
						<select name="ti[]" class="sel" multiple >
							<option value="0" selected>CLIENTE</option>
							<option value="1" selected>FORNECEDOR</option>
							<option value="2" selected>CLIENTE/FORNECEDOR</option>
							<option value="3" selected>PESSOA FISICA</option>
						</select>
					</label>
				</div>
				<div class="col-xs-12 col-md-2" style="padding:0px 5px">
					<label for="" style="width:100%"><small>Relatorio:</small> <br/>
						<select name="relatorio" class="form-control input-sm" style="width:100%">
							<option value="1">SIMPLES</option>
							<option value="2">DETALHADO</option>
						</select>
					</label>
				</div>
				<div class="col-xs-12 col-md-2" style="padding:0px 5px">
					<label><br/>
						<input type="submit" value="Pesquisar" style="width:150px; margin-left:10px;" onClick="post('#form1','financeiro/consulta-empresas.php?ac=consulta','.retorno')" class="btn btn-success btn-sm">
					</label>
				</div>
			</div>
		</div>
	</form>
	<div class="retorno"></div>
</section>
	<div class="modal" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog"  style="width:90%;">
			<div class="modal-content"> 
				<div class="modal-header box box-info" style="margin:0px;">
					<button type="button" class="close" style="opacity:1" onclick="$('.modal').modal('hide'); $('.modal-body').empty()" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="myModalLabel">Painel Administrativo</h4>
				</div>
				<div class="modal-body">
					Aguarde um momento &nbsp;&nbsp; <img src="../style/img/loading.gif" alt="Carregando" width="20px"/>
				</div>
			</div>
		</div>
	</div>