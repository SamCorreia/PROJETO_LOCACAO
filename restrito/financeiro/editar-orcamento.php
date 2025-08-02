<?php
	require_once('../../config.php');
	require_once('../../functions.php');
	$con = new DBConnection();
	verificaLogin(); getData();
	$stm = $con->query("SELECT *, (select razao_social from localferramentas_cadastroempresa where id = orcamento_dados.empresa) as empresa_nome, (select cnpj from localferramentas_cadastroempresa where id = orcamento_dados.empresa) as cnpj FROM orcamento_dados WHERE id = '$id_orcamento'");
	while($b = $stm->fetch()){ 
		extract ($b); 
	}
	if(empty($del_list)){
		$del_list = 'no';
	}
	if(isset($ac)){
		if($ac=='add'){
			try 
			{
				$stm = $con->prepare("INSERT INTO orcamento_itens (id_orcamento, id_item, qtd, desconto_vlr, acres_vlr) VALUES (?, ?, ?, ?, ?)");
				$stm->execute(array($id_orcamento, $itemInput, $qtdInput, $descontoInput, $acres_vlrInput));
				echo '<script>ldy("financeiro/editar-orcamento.php?ac=listar&del_list=yes&id_orcamento='.$id_orcamento.'","#listar") </script>';
			}
			catch(PDOException $e)
			{
			  echo 'Erro: '.$e->getMessage();
			}
			exit;
		}
		if($ac=='listar'){
			echo '<div class="box box-warning">
				<table class="table table-bordered table-striped">
				<thead>
				<tr style="font-size: smaller">
					<th style="width:50%">Item</th>
					<th style="text-align:center; width:10%;">Qtd</th>
					<th style="text-align:center; width:10%;">Vlr</th>
					<th style="text-align:center; width:10%;">Desconto</th>
					<th style="text-align:center; width:10%;">Acresc.</th>
					<th style="text-align:center; width:10%;">Total</th>';
					if($del_list == 'yes'){
						echo '<th style="text-align:center; width:10%;">Excluir</th>';
					}
				echo '</tr>
				</thead>
				<tbody>';
			$stm = $con->prepare("SELECT *, (SELECT descricao FROM notas_itens WHERE id = orcamento_itens.id_item) as descricao FROM orcamento_itens WHERE id_orcamento = ? ");
			$stm->execute(array($id_orcamento));
			while($s = $stm->fetch())
			{
				$total_item = 0;
				echo '<tr id="thisTr'.$s['id'].'">';
				echo '<td>'.$s['descricao'].'</td>';
				echo '<td align="center">'.$s['qtd'].'</td>';
				$stm2 = $con->query("SELECT * FROM notas_itens WHERE id = '".$s['id_item']."'");
				$valor_array = $stm2->fetch(PDO::FETCH_ASSOC);
				switch($medicao){
					case '30':
						$valor_un = $valor_array['valor30'];
					break;
					case '15':
						$valor_un = $valor_array['valor15'];
					break;
					case '7':
						$valor_un = $valor_array['valor07'];
					break;
					case '3':
						$valor_un = $valor_array['valor03'];
					break;
				}
				
				echo '<td align="center">R$ '.number_format($valor_un,2,",",".").'</td>';
				echo '<td align="center">R$ '.number_format($s['desconto_vlr'],2,",",".").'</td>';
				echo '<td align="center">R$ '.number_format($s['acres_vlr'],2,",",".").'</td>';
				$total_item = (($valor_un - $s['desconto_vlr']) + $s['acres_vlr']) * $s['qtd'];
				echo '<td align="center">R$ '.number_format($total_item,2,",",".").'</td>';
				if($del_list == 'yes'){
				echo '
				<td align="center">
					<a href="#" onclick=\'$(".modal-body").load("financeiro/del/excluir-orcamento-item.php?&id='.$s['id'].'")\' data-toggle="modal" data-target="#myModal2"  class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-trash"></span></a>
				</td>';
				}
			echo '</tr>';
			}
			echo ' </tbody> </table> </div>';
			exit;
		}
		exit;
	}
?>
<script>
$(document).ready(function () {
	$('#autoComplete1').selectToAutocomplete();
	//Multi Select
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
});
</script>
<section class="container-fluid" style="clear:both; padding-left:0px; margin-left:0px">
	<div class="resultadoCadastro"></div>
	<div class="container-fluid" id="alert1" style="margin:0px; padding:0px;">
		<h2 style="font-family: 'Oswald', sans-serif; letter-spacing:1px;">Orçamento<small>  Adicionar itens</small>
		
		<a href="financeiro/imprimir-orcamento.php?id_orcamento=<?= $id_orcamento ?>" target="_blank" id="btnPrint" style="letter-spacing:5px; margin-top:5px;" class="hidden-xs hidden-print pull-right btn btn-warning btn-sm"> <span class="glyphicon glyphicon-print" aria-hidden="true"></span>&nbsp;Imprimir</a></h2>
		
	</div>
	<section class="container-fluid" style="padding:0px;">
		<div class="box box-primary" style="padding-top:10px">
			<table class="table table-striped table-bordered">
				<tr class="small">
					<th>Empresa:</th>
					<th>Assunto:</th>
					<th>Data:</th>
					<th>Medição:</th>
				</tr>
				<tr>
					<td><?= $cnpj.' - '.$empresa_nome ?></td>
					<td><?= $assunto ?></td>
					<td><?= implode("/",array_reverse(explode("-",$data))) ?></td>
					<td><?= $medicao ?> Dias</td>
				</tr>
			</table>
		</div>
	</section>
	<div class="box box-widget box-fix-layout" style=" background:#f0fbf9">
		<div class="container-fluid" style="padding:5px">
		<form action="javascript:void(0)" onSubmit="post(this,'financeiro/editar-orcamento.php?ac=add&id_orcamento=<?php echo $id_orcamento ?>','#listar')">
			<div class="box-body">
				<div class="col-md-6 col-xs-12">
					<div class="form-group">
						<label><small>Selecione o Item:</small></label>
						<select name="itemInput" id="autoComplete1" class="form-control input-sm" required>
							<option value="" selected>Selecione um item</option>
							<?php
							$stm = $con->query("SELECT * FROM notas_itens WHERE status = 0 ORDER BY descricao ASC");
							while($b = $stm->fetch()){
								echo '<option value="'.$b['id'].'">'.$b['descricao'].'</option>';
							}
							?>
						</select>
					</div>
				</div>
				<div class="col-md-1 col-xs-12">
					<div class="form-group">
						<label><small>Quantidade:</small></label>
						<input type="number" name="qtdInput" step="0.01" min="1" class="form-control input-sm" required>
					</div>
				</div>
				<div class="col-md-2 col-xs-12">
					<div class="form-group">
						<label><small>Desconto (UN):</small></label>
						<input type="number" name="descontoInput" step="0.01" min="0" class="form-control input-sm">
					</div>
				</div>
				<div class="col-md-2 col-xs-12">
					<div class="form-group">
						<label><small>Acrescimo (UN):</small></label>
						<input type="number" name="acres_vlrInput" step="0.01" min="0" class="form-control input-sm">
					</div>
				</div>
				<div class="col-md-3 col-xs-12">
					<div class="form-group center-input"><br/>
						<input type="submit" class="btn btn-success" value="Adicionar" style="width:150px;">
					</div> 
				</div>
			</div>
		</form>
		</div>
	</div>
	<script>ldy("financeiro/editar-orcamento.php?ac=listar&del_list=yes&id_orcamento=<?php echo $id_orcamento ?>","#listar")</script>
	<div id="listar" style="margin-top:20px;"></div>
</section>

<div class="modal" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="width:auto;">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" onclick="$('.modal').modal('hide')" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="myModalLabel">Excluir Item</h4>
			</div>
			<div class="modal-body">
				Aguarde um momento &nbsp;&nbsp; <img src="../style/img/loading.gif" alt="Carregando" width="20px"/>
			</div>
		</div>
	</div>
</div>