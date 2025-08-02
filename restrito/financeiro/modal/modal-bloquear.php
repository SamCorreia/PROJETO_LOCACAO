<?php
	require_once('../../../config.php');
	require_once('../../../functions.php');
	$con = new DBConnection();
	verificaLogin(); getData();
	if(isset($ac)){
		if($ac=='bloquear') { 
			try
			{
				$query = $con->prepare("UPDATE contrato_adendo SET `tipo` = '1' WHERE id = ?"); 
				$query->execute(array($id_adendo));
			}
			catch(PDOException $e)
			{
				echo 'Erro: ' . $e->getMessage();
			}
			if($query) { 
				echo '<center><p class="text-success">Informações atualizada com sucesso! Adendo Bloqueada</p>
				<a href="#" class="btn btn-danger btn-sm" style="width:150px" autofocus onclick=\'$(".modal").modal("hide")\'>Fechar</a></center>'; 

				echo '<script>ldy("financeiro/modal/adendo-itens.php?id_contrato='.$id_contrato.'",".listarAdendo")</script>';
			}else{ 
				echo '<p class="text-danger">'.mysql_error().'</p>'; 
			}
			exit;
		}
		if($ac=='desbloquear') { 
			try
			{
				$query = $con->prepare("UPDATE contrato_adendo SET `tipo` = '0' WHERE id = ?"); 
				$query->execute(array($id_adendo));
			}
			catch(PDOException $e)
			{
				echo 'Erro: ' . $e->getMessage();
			}
			if($query) { 
				echo '<center><p class="text-success">Informações atualizada com sucesso! DESBLOQUEADA</p>
							<a href="#" class="btn btn-danger btn-sm" style="width:150px" autofocus onclick=\'$(".modal").modal("hide")\'>Fechar</a></center>'; 

				echo '<script>ldy("financeiro/modal/adendo-itens.php?id_contrato='.$id_contrato.'",".listarAdendo")</script>';
			}else{ 
				echo '<p class="text-danger">'.mysql_error().'</p>'; 
			}
			exit;
		}
	}
	echo '
		<center>
			<div class="alert alert-danger" style="font-size:12px">
			Tem certeza que deseja alterar a situação deste adendo?
			</div>
		</center>';
	echo '
		<div class="ajax">
			<center>
			<a href="javascript:void(0)" class="btn btn-success btn-sm" style="width:150px; margin-right:20px;" onclick=\'ldy("financeiro/modal/modal-bloquear.php?ac='.$tipo.'&id_adendo='.$id_adendo.'&id_contrato='.$id_contrato.'",".ajax")\'>Sim</a>
			
			<a href="#" class="btn btn-danger btn-sm" style="width:150px" autofocus onclick=\'$(".modal").modal("hide")\'>Não</a>
			
			</center>
		</div>';
?>