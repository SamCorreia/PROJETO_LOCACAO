<?php
	require_once('../../config.php');
	require_once('../../functions.php');
	$con = new DBConnection();
	verificaLogin(); getData();
?>
<script>
$(document).ready(function(){
	$(".up").keyup(function() {
		$(this).val($(this).val().toUpperCase());
	});
});
</script>
<?php
if(@$ac == 'update') {
	try {
		$query = $con->query("UPDATE `notas_eq_situacao` SET `descricao`='$descricao', `status`='$statusInput' WHERE id = '$id'");
	}
	catch( PDOException $e ){
		echo '<p class="text-danger">Algo aconteceu de errado!</p>'; 
	}
	echo '<div class="alert alert-success" role="alert">
  <strong>Sucesso!!!</strong> As informações foram atualizadas.
</div>';
	exit;
}

?>

<div class="retorno"></div>
<form action="javascript:void(0)" onsubmit='post(this,"gestor/editar-situacao-eqp.php?ac=update&id=<?php echo $id ?>",".retorno")'>
	<?php
		$stm2 = $con->query("select * from notas_eq_situacao where id = '$id'");
		while($c = $stm2->fetch()){ extract($c); ?>
		<label style="width:100%">Descrição:
			<input type="text" name="descricao" value="<?php echo $descricao; ?>" class="form-control input-sm up" size="100" required/>
		</label>
		<label style="width:100%">Status:
			<select name="statusInput" class="form-control" style="width:100%">
				<?php 
				if($status == '0'){ 
					echo '<option value="0" selected>ATIVO</option>';
					echo '<option value="1">INATIVO</option>';
				}else if($status == '1'){
					echo '<option value="0">ATIVO</option>';
					echo '<option value="1" selected>INATIVO</option>';
				}
				?>
			</select>
		</label>
		<label style="width:100%; text-align:center"><br/>
			<input type="submit" value="Salvar"  style="width:50%" class="btn btn-success btn-sm"/>
		</label>
		<?php } ?>
</form>
