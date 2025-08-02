<?php
	require_once('../../../config.php');
	require_once('../../../functions.php');
	$con = new DBConnection();
	verificaLogin(); getData();
	if(isset($ac)){
		if($ac=='excluir') { 
			try
			{
				$query = $con->prepare("DELETE FROM empresa_obras WHERE id = ?"); 
				$query->execute(array($id_item));
			}
			catch(PDOException $e)
			{
				echo '<center><p class="text-danger">Esta informação não pode ser excluida, pois está vinculada a algum adendo!</p>';
				echo '<a href="#" class="btn btn-danger btn-sm" style="width:150px" autofocus onclick=\'$(".modal").modal("hide")\'>Fechar</a></center>'; 
				exit;
			}
			if($query) { 
				echo '<center><p class="text-success">Informações deletada com sucesso!</p>';
			}else{ 
				echo '<p class="text-danger">'.mysql_error().'</p>'; 
			}
			echo '<a href="#" class="btn btn-danger btn-sm" style="width:150px" autofocus onclick=\'$(".modal").modal("hide")\'>Fechar</a></center>'; 
			
			
			exit;
		}
	}
	echo '
		<center>
			<div class="alert alert-danger" style="font-size:12px">
				Tem certeza que deseja excluir esta obra permanentemente?
			</div>
		</center>';
	echo '
		<div class="ajax">
			<center>
			<a href="javascript:void(0)" class="btn btn-success btn-sm" style="width:150px; margin-right:20px;" onclick=\'ldy("financeiro/del/excluir-empresa-obra.php?ac=excluir&id_item='.$id_item.'",".ajax")\'>Sim</a>
		
			<a href="#" class="btn btn-danger btn-sm" style="width:150px" autofocus onclick=\'$(".modal").modal("hide")\'>Não</a>
			</center>
		</div>';
?>