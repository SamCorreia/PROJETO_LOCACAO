<?php
	require_once('../../config.php');
	require_once('../../functions.php');
	$con = new DBConnection();
	verificaLogin(); getData();
	$stm = $con->query("SELECT *, (select razao_social from localferramentas_cadastroempresa where id = orcamento_dados.empresa) as empresa_nome, (select cnpj from localferramentas_cadastroempresa where id = orcamento_dados.empresa) as cnpj FROM orcamento_dados WHERE id = '$id_orcamento'");
	while($b = $stm->fetch()){ 
		extract ($b); 
		
	}
	if(isset($ac)){
		if($ac=='adicionar2'){
			echo 'teste';
			exit;
		}
		if($ac=='listar2'){
			echo 'teste';
			exit;
		}
		/*if($ac=='add'){
			try 
			{
				$stm = $con->prepare("INSERT INTO orcamento_itens (id_orcamento, id_item, qtd, desconto_vlr) VALUES (?, ?, ?, ?)");
				$stm->execute(array($id_orcamento, $itemInput, $qtdInput, $descontoInput));
				echo '<script>ldy("financeiro/editar-orcamento.php?ac=listar&id_orcamento='.$id_orcamento.'","#listar") </script>';
			}
			catch(PDOException $e)
			{
			  echo 'Erro: '.$e->getMessage();
			}
			exit;
		}*/
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
		<h3 style="font-family: 'Oswald', sans-serif; letter-spacing:1px;">Orçamento Aprovado<small></small>
		
		<a href="financeiro/imprimir-orcamento.php?id_orcamento=<?= $id_orcamento ?>" target="_blank" id="btnPrint" style="letter-spacing:5px; margin-top:5px;" class="hidden-xs hidden-print pull-right btn btn-warning btn-sm"> <span class="glyphicon glyphicon-print" aria-hidden="true"></span>&nbsp;Imprimir</a></h3>
		
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
	<script>ldy("financeiro/editar-orcamento.php?ac=listar&id_orcamento=<?php echo $id_orcamento ?>","#listar")</script>
	<div id="listar"></div>
</section>