<?php
	require_once('../../../config.php');
	require_once('../../../functions.php');
	$con = new DBConnection();
	verificaLogin(); getData();
	if(isset($ac)){
		if($ac=='editarAdendo'){
			$con->prepare("UPDATE  SET data_adendo = '$data_adendoInput', obra = '$obraInput', obs = '$obsInput' WHERE id = '$id_adendo'");
			$query = $con->query("UPDATE `contrato_adendo` SET `data_adendo`='$data_adendoInput', `obra`='$obraInput', `obs`='$obsInput' WHERE id = '$id_adendo'");
			if($query) {
				echo '<div class="alert alert-success" role="alert">Informações atualizadas com sucesso!</div>';
			}else{ 
				echo '<div class="alert alert-danger" role="alert">'.mysql_error().'</div>';
			}
		}
		exit;
	}

	$adendo = $con->query("SELECT * FROM contrato_adendo WHERE id = '$id_adendo'");
	while($x = $adendo->fetch())
	{
?>
	<div class="editarAdendo" style="margin-top:10px;"></div>
	<div class="box box-widget box-fix-layout">
		<div class="container-fluid" style="padding:5px">
			<form action="javascript:void(0)" onSubmit="post(this,'financeiro/modal/editar-adendo.php?ac=editarAdendo&id_contrato=<?php echo $id_contrato ?>&id_adendo=<?=$id_adendo ?>','.editarAdendo')">
				<div class="box-body">
					<div class="col-md-12 col-xs-12">
						<div class="form-group">
							<label><small>Data:</small></label>
							<input type="date" name="data_adendoInput" min="2018-01-01" max="<?= $todayTotal ?>" value="<?= $x['data_adendo'] ?>" class="form-control input-sm" required />
						</div>
					</div>
					<div class="col-md-12 col-xs-12">
						<div class="form-group">
							<label><small>Contrato (Obra):</small></label>
							<select id="EditarAdendo" name="obraInput" class="form-control input-sm selectAuto" required>
								<option value="0">SEM OBRA</option>
								<?php 
									$id_empresa_contrato = $con->query("SELECT empresa FROM contrato_dados WHERE id = '$id_contrato' ")->fetchColumn();
									$obras_consulta = $con->query("SELECT * FROM empresa_obras WHERE id_empresa = '$id_empresa_contrato'");
									while($l = $obras_consulta->fetch()) {
										if($x['obra'] == $l['id']){
											echo '<option value="'.$l['id'].'" selected>'.$l['nome'].'</option>'; 
										}else{
											echo '<option value="'.$l['id'].'">'.$l['nome'].'</option>'; 
										}
									}
								?>	
							</select>
						</div>
					</div>
					<div class="col-md-12 col-xs-12">
						<div class="form-group">
							<label><small>Observações:</small></label>
							<input type="text" name="obsInput" value="<?= $x['obs'] ?>" class="form-control input-sm" />
						</div>
					</div>
					<div class="col-md-12 col-xs-12">
						<div class="form-group" style="text-align:center"><br/>
							<input type="submit" class="btn btn-success" value="Salvar" style="width:150px;">
						</div> 
					</div>
				</div>
			</form>
		</div>
	</div>
<?php 
	}
	?>