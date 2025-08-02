<?php
	require_once('../../config.php');
	require_once('../../functions.php');
	$con = new DBConnection();
	verificaLogin(); getData();
	if(isset($ac)){
		$query = $con->query("UPDATE `notas_itens` SET `descricao`='$descricaoInput', `categoria`='$categoriaInput', `valor30`='$valor30Input', `valor15`='$valor15Input', `valor07`='$valor07Input', `valor03`='$valor03Input', `status`='$statusInput', `data_edit` = now() WHERE id = '$id'");
		if($query) {
			echo '<div class="alert alert-success" role="alert">Informações atualizadas com sucesso!</div>';
		}else{ 
			echo '<div class="alert alert-danger" role="alert">'.mysql_error().'</div>';
		}
		exit;
	}
?>
<?php
$stm = $con->query("SELECT * FROM notas_itens WHERE id = '$id'");
while($b = $stm->fetch()){ extract ($b); }
?>

<section class="content">
	<div class="resultadoEditar"></div>
	<div class="box box-primary" style="padding-top:10px">
		<form action="javascript:void(0)" onSubmit="post(this,'financeiro/editar-item.php?ac=update&id=<?=$id ?>','.resultadoEditar')">
			<div class="box-body">
				<div class="form-group">
					<label>Descrição:</label>
					<input type="text" name="descricaoInput" value="<?= $descricao; ?>" class="form-control input-sm" required>
				</div>
				<div class="form-group">
					<label>Categoria:</label>
					<select name="categoriaInput" class="form-control input-sm" required>
						<?php
							$acesso = $con->query("select * from notas_categorias where status = '0'");
							while($ace = $acesso->fetch()) {
								if($categoria == $ace['id']){
									echo '<option value="'.$ace['id'].'" selected>'.$ace['descricao'].'</option>';
								}else{
									echo '<option value="'.$ace['id'].'">'.$ace['descricao'].'</option>';
								}
							}
						?>		
					</select>
				</div>
				<div class="form-group">
					<label>Valor 30:</label>
					<input type="number" step="0.01" name="valor30Input" value="<?= $valor30; ?>" class="form-control input-sm" required>
				</div>
				<div class="form-group">
					<label>Valor 15:</label>
					<input type="number" step="0.01" name="valor15Input" value="<?= $valor15; ?>" class="form-control input-sm" required>
				</div>
				<div class="form-group">
					<label>Valor 07:</label>
					<input type="number" step="0.01" name="valor07Input" value="<?= $valor07; ?>" class="form-control input-sm" required>
				</div>
				<div class="form-group">
					<label>Valor 03:</label>
					<input type="number" step="0.01" name="valor03Input" value="<?= $valor03; ?>" class="form-control input-sm" required>
				</div>
				<div class="form-group">
					<label style="width:100%">Status:
						<select name="statusInput" class="form-control input-sm">
							<?php if($status == '0') { ?>
								<option value="0" selected>ATIVO</option>
								<option value="1">INATIVO</option>
							<?php }else{ ?>
								<option value="0">ATIVO</option>
								<option value="1" selected>INATIVO</option>
							<?php } ?>
						</select>
					</label>
				</div>
				<div class="box-footer" style="text-align:center">
					<input type="submit" style="width:50%" class="btn btn-success btn-sm submit-empresa" value="Salvar">
				</div>
			</div>
		</form>
	</div>
</section>