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
	$('#autoComplete1').children().not('option').remove();
    // Só então aplica o plugin
    $('#autoComplete1').selectToAutocomplete();
	$('#autoComplete').selectToAutocomplete();
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
		$query = $con->prepare("INSERT INTO contrato_dados (data_contrato, empresa, prazo_contrato, prazo_cancelamento, prazo_pagamento, calcao) VALUES (?,?,?,?,?,?)");
		$query->execute(array($data_contratoInput, $empresaInput, $prazo_contratoInput, $prazo_cancelamentoInput, $prazo_pagamentoInput, $calcaoInput));
		$id_contrato = $con->lastInsertId();
		echo '<script>ldy("financeiro/editar-contrato.php?id_contrato='.$id_contrato.'",".conteudo")</script>'; 
		exit;
	}
	
	?>

<section class="container-fluid">
	<div class="resultadoCadastro"></div>
	<section class="content-header" id="alert1" style="margin:0px;">
		<h2 style="font-family: 'Oswald', sans-serif; letter-spacing:1px;">Cadastro de Contrato<small> </small></h2>
	</section>
	<section class="content">
		<div class="box box-primary" style="padding-top:10px">
			<form action="javascript:void(0)" onSubmit="post(this,'financeiro/cadastro-contrato.php?ac=ins','.resultadoCadastro')">
				<div class="box-body">
					<div class="col-xs-12">
						<div class="form-group">
							<label><small>Data Contrato:</small></label>
							<input type="date" name="data_contratoInput" value="<?= $todayTotal ?>" max="<?= $todayTotal ?>" min="2018-10-01" class="form-control input-sm" required>
						</div>
					</div>
					<div class="col-xs-12">
						<div class="form-group">
							<label><small>Empresa:</small></label>
							<select name="empresaInput" id="autoComplete1" class="form-control input-sm">
								<?php
									$acesso = $con->query("select * from localferramentas_cadastroempresa");
									while($ace = $acesso->fetch()) {
										echo '<option value="1'.$ace['id'].'">'.$ace['cnpj'].' - '.strtoupper($ace['razao_social']).'</option>';
									}
								?>		
							</select>
						</div>
					</div>
					<div class="col-xs-12" style="padding:0px">
						<h4 style="width:100%">Prazos</h4>
						<div class="col-xs-4">
							<div class="form-group">
								<label><small>Periodo do Contrato (Meses):</small></label>
								<input type="number" min="1" max="60" step="1" name="prazo_contratoInput" class="form-control input-sm" required />
							</div>
						</div>
						<div class="col-xs-4">
							<div class="form-group">
								<label><small>Prazo para Cancelamento (Dias):</small></label>
								<input type="number" min="1" max="100" step="1" name="prazo_cancelamentoInput" class="form-control input-sm" required />
							</div>
						</div>
						<div class="col-xs-4">
							<div class="form-group">
								<label><small>Pagamento (Dia do Mês):</small></label>
								<input type="number" min="1" max="30" step="1" name="prazo_pagamentoInput" class="form-control input-sm" required />
							</div>
						</div>
						<div class="col-xs-4">
							<div class="form-group">
								<label><small>Calção(%):</small></label>
								<input type="number" min="1" max="100" step="1" name="calcaoInput" class="form-control input-sm" required />
							</div>
						</div>
					</div>
					<div class="col-xs-12">
						<div class="box-footer" style="text-align:center">
							<input type="submit" style="width:50%" class="btn btn-success btn-sm submit-empresa" value="Salvar">
						</div>
					</div>
				</div>
			</form>
		</div>
	</section>
</section>