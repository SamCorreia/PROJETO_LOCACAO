<?php
	require_once('../../../config.php');
	require_once('../../../functions.php');
	$con = new DBConnection();
	verificaLogin(); getData();
	if(isset($ac)){
		if($ac=='criarAdendo'){
			$count = $con->query("SELECT COUNT(*) FROM contrato_adendo WHERE id_contrato = '$id_contrato' AND tipo = '0'")->fetchColumn();

			if($count > 0){
				echo '<div class="alert alert-danger" style="font-size:12px">
					Finalize os adendos que estão abertos, antes de cadastrar um novo.
				</div>';
			}else{
				try 
				{
					$stm = $con->prepare("INSERT INTO contrato_adendo (id_contrato, data_adendo, obra, obs) VALUES (?, ?, ?, ?)");
					$stm->execute(array($id_contrato, $data_adendoInput, $contratoInput, $obsInput));
					echo '<script>ldy("financeiro/modal/adendo-itens-devolucao.php?id_contrato='.$id_contrato.'",".listarAdendo")</script>';
				}
				catch(PDOException $e)
				{
				  echo 'Erro: '.$e->getMessage();
				}
			}
			exit;
		}
		exit;
	}
?>
<script>
$(document).ready(function () {
	$('#autoComplete2').selectToAutocomplete();
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
	<div class="cadastroAdendo" style="margin-top:10px;"></div>
	<div class="container-fluid" id="alert1" style="margin:0px; padding:0px;">

	</div>
		<div class="box box-widget box-fix-layout" style=" background:#e1f7f3">
		<div class="container-fluid" style="padding:5px">
			<form action="javascript:void(0)" onSubmit="post(this,'financeiro/modal/cadastrar-adendo-devolucao.php?ac=criarAdendo&id_contrato=<?php echo $id_contrato ?>','.cadastroAdendo')">
				<div class="box-body">
					<div class="col-md-2 col-xs-12">
						<div class="form-group">
							<label><small>Data:</small></label>
							<input type="date" name="data_adendoInput" min="2018-01-01" max="<?= $todayTotal ?>" value="<?= $todayTotal ?>" class="form-control input-sm" required />
						</div>
					</div>
					<div class="col-md-2 col-xs-12">
						<div class="form-group">
							<label><small>Contrato (Obra):</small></label>
							<select id="autoComplete" name="contratoInput" class="form-control input-sm selectAuto" required>
								<?php 
									$id_empresa_contrato = $con->query("SELECT empresa FROM contrato_dados WHERE id = '$id_contrato' ")->fetchColumn();
									
									$obras_consulta = $con->query("SELECT * FROM empresa_obras WHERE id_empresa = '$id_empresa_contrato'");
									while($l = $obras_consulta->fetch()) {
										echo '<option value="'.$l['id'].'">'.$l['nome'].'</option>'; 
									}
								?>	
							</select>
						</div>
					</div>
					<div class="col-md-3 col-xs-12">
						<div class="form-group">
							<label><small>Observações:</small></label>
							<input type="text" name="obsInput" class="form-control input-sm" />
						</div>
					</div>
					<div class="col-md-1 col-xs-12">
						<div class="form-group center-input"><br/>
							<input type="submit" class="btn btn-success" value="Adicionar" style="width:150px;">
						</div> 
					</div>
				</div>
			</form>
		</div>
	</div>
	
	<hr/>
		<h2 style="font-family: 'Oswald', sans-serif; letter-spacing:1px;"><small>  Lista de Adendos</small>
		</h2>
	<div class="listarAdendo">
		<script>ldy("financeiro/modal/adendo-itens-devolucao.php?id_contrato=<?php echo $id_contrato ?>",".listarAdendo")</script>
	</div>
<div class="modal" id="myModal3" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="width:auto;">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" onclick="$('.modal').modal('hide')" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="myModalLabel">Alterar Adendo</h4>
			</div>
			<div class="modal-body">
				Aguarde um momento &nbsp;&nbsp; <img src="../style/img/loading.gif" alt="Carregando" width="20px"/>
			</div>
		</div>
	</div>
</div>