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
<?php
	if(isset($ac)){
		$query = $con->prepare("INSERT INTO orcamento_dados (data, empresa, assunto, medicao, frete, pagamento, user) VALUES (?,?,?,?,?,?, ?)");
		$query->execute(array($dataInput, $empresaInput, $assuntoInput, $medicaoInput, $freteInput, $pagamentoInput, $id_usuario_logado));
		$id_orcamento = $con->lastInsertId();
		echo '<script>ldy("financeiro/editar-orcamento.php?id_orcamento='.$id_orcamento.'",".conteudo")</script>'; 
		echo 'ok';
		exit;
	}
	
	?>

<section class="container-fluid">
	<div class="resultadoCadastro"></div>
	<section class="content-header" id="alert1" style="margin:0px;">
		<h2 style="font-family: 'Oswald', sans-serif; letter-spacing:1px;">Cadastro Orçamento<small> </small></h2>
	</section>
	<section class="content">
		<div class="box box-primary" style="padding-top:10px">
			<form action="javascript:void(0)" onSubmit="post(this,'financeiro/cadastro-orcamento.php?ac=ins','.resultadoCadastro')">
				<div class="box-body">
					<div class="form-group">
						<label>Data:</label>
						<input type="date" name="dataInput" value="<?= $todayTotal ?>" max="<?= $todayTotal ?>" min="2018-10-01" class="form-control input-sm" required>
					</div>
					<div class="form-group">
						<label>Empresa:</label>
						<select name="empresaInput" id="autoComplete1" class="form-control input-sm">
							<?php
								$acesso = $con->query("select * from litoralrent_cadastroempresa");
								while($ace = $acesso->fetch()) {
									echo '<option value="'.$ace['id'].'">'.$ace['cnpj'].' - '.strtoupper($ace['razao_social']).'</option>';
								}
							?>		
						</select>
					</div>
					<div class="form-group">
						<label>Assunto:</label>
						<input type="text" name="assuntoInput" class="form-control input-sm" required>
					</div>
					<div class="form-group">
						<label>Medição:</label>
						<select name="medicaoInput" class="form-control input-sm" required>
							<option value="30">30 Dias</option>
							<option value="15">15 Dias</option>
							<option value="7">07 Dias</option>
							<option value="3">03 Dias</option>
						</select>
					</div>
					<div class="form-group">
						<label>Frete:</label>
						<select name="freteInput" class="form-control input-sm" required>
							<option value="0">Gratis</option>
							<option value="1">Embutido</option>
						</select>
					</div>
					<div class="form-group">
						<label>Pagamento:</label>
						<select name="pagamentoInput" class="form-control input-sm" required>
							<option value="0">A VISTA</option>
							<option value="1">10 Dias</option>
							<option value="2">20 Dias</option>
							<option value="3">30 Dias</option>
							<option value="4">40 Dias</option>
						</select>
					</div>
					<div class="box-footer" style="text-align:center">
						<input type="submit" style="width:50%" class="btn btn-success btn-sm submit-empresa" value="Salvar">
					</div>
				</div>
			</form>
		</div>
	</section>
</section>