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
		    { "orderable": false, "targets": [ -1 ] }
		]
    });
	});
</script>
	<?php
	if(isset($ac)){
		if(!isset($ca)){ echo '<script>alert("1");</script>'; }	
		foreach($ca as $cat) { @$catu .= $cat.','; } $catu = substr($catu,0,-1);
			echo '<div class="box box-widget">
					<table id="resultadoConsulta" class="box box-widget table table-bordered table-striped table-condensed" style="font-size:10px">
					<thead>
						<tr>
							<th style="text-align:center"><i class="fa fa-list-alt" aria-hidden="true"></i></th>
							<th style="text-align:center">Descrição</th>
							<th style="text-align:center">Categoria</th>
							<th style="text-align:center">Valor 30</th>
							<th style="text-align:center">Valor 15</th>
							<th style="text-align:center">Valor 07</th>
							<th style="text-align:center">Valor 03</th>
							<th style="text-align:center">Data Edição</th>
						</tr>
					</thead> 
				<tbody>';
				$stm = $con->query("SELECT * FROM notas_itens WHERE descricao LIKE '%$descricao%' AND categoria IN($catu)");
				$c = 0;
				while($b = $stm->fetch())
				{
					$c += 1;
					echo '<tr id="thisTr'.$b['id'].'">';
					echo '<td style="text-align:center">'.$c.'</td>';
					echo '<td>'.strtoupper($b['descricao']).'</td>';
					$sth = $con->query("SELECT descricao FROM notas_categorias WHERE id = '".$b['categoria']."'");
					$categoria_array = $sth->fetch(PDO::FETCH_ASSOC);
					echo '<td style="text-align:center">'.$categoria_array['descricao'].'</td>';
					echo '<td style="text-align:center">'.$b['valor30'].'</td>';
					echo '<td style="text-align:center">'.$b['valor15'].'</td>';
					echo '<td style="text-align:center">'.$b['valor07'].'</td>';
					echo '<td style="text-align:center">'.$b['valor03'].'</td>';
					echo '<td style="text-align:center" data-order="'.$b['data_edit'].'">'.implode("/",array_reverse(explode("-",$b['data_edit']))).'</td>';
					
					echo '</tr>';
				}
				echo '</tbody> </table> </div>';
			exit;
	}
	?>

<section class="content">
	<div class="buttons-top-page">
		<a href="#" style="padding:3px 15px;" title="Cadastrar Novo" class="btn btn-success btn-sm" onclick='$(".modal-body").load("financeiro/cadastro-item.php")' data-toggle="modal" data-target="#myModal"><i class="fa fa-plus-circle" aria-hidden="true"></i> Cadastrar</a>				
		<a href="#" style="padding:3px 15px; margin:0px 10px;" title="Atualizar Pagina" class="btn btn-warning btn-sm" onclick="ldy('financeiro/consulta-itens.php','.conteudo')"><i class="fa fa-refresh" aria-hidden="true"></i> Atualizar</a>
	</div>
	<div style="clear: both;">
		<hr></hr>
	</div>
	<form action="javascript:void(0)" id="form1" class="hidden-print">
		<div class="well well-sm" style="padding:10px 10px 5px 10px;">
			<div class="col-xs-12 col-md-6" style="padding:0px 5px">
				<label for="" style="width:100%"><small>Buscar:</small> <br/>
					<input type="text" name="descricao" placeholder="Nome do item" class="form-control input-sm" />
				</label>
			</div>
			<div class="col-xs-12 col-md-2" style="padding:0px 5px">
				<label for="" style="width:100%">
					<small>Categoria:</small> <br/>
					<select name="ca[]" class="sel" multiple="multiple" required>
						<?php
							$acesso = $con->query("select * from notas_categorias where status = '0'");
							while($ace = $acesso->fetch()) {
								echo '<option value="'.$ace['id'].'" selected>'.$ace['descricao'].'</option>';
							}
						?>		
					</select>
				</label>
			</div>
			<label><br/>
				<input type="submit" value="Pesquisar" style="width:150px; margin-left:10px;" onClick="post('#form1','financeiro/consulta-itens-2.php?ac=consulta','.retorno')" class="btn btn-success btn-sm">
			</label>
		</div>
	</form>
	<div class="retorno"></div>
</section>
	<div class="modal" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog"  style="width:80%;">
			<div class="modal-content"> 
				<div class="modal-header box box-info" style="margin:0px;">
					<button type="button" class="close" onclick="$('.modal').modal('hide'); $('.modal-body').empty()" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="myModalLabel">Painel Administrativo</h4>
				</div>
				<div class="modal-body">
					Aguarde um momento &nbsp;&nbsp; <img src="../style/img/loading.gif" alt="Carregando" width="20px"/>
				</div>
			</div>
		</div>
	</div>