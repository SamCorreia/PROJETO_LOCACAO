<?php
	require_once('../../../config.php');
	require_once('../../../functions.php');
	$con = new DBConnection();
	verificaLogin(); getData();
	if(isset($ac)){
		if($ac=='excluir') { 
			$query22 = $con->query("DELETE FROM contrato_adendo WHERE id = '$id'"); 
			if($query22) { 
				echo '<center><p class="text-success">Informações deletada com sucesso!</p>
							<a href="#" class="btn btn-danger btn-sm" style="width:150px" autofocus onclick=\'$(".modal").modal("hide")\'>Fechar</a></center>'; 
				echo '<script>$("#thisAdendo'.$id.'").hide()</script>';
			}else{ 
				echo '<p class="text-danger">'.mysql_error().'</p>'; 
			}
			echo 'testando';
			exit;
		}
	}
	$count = $con->query("SELECT COUNT(*) FROM contrato_itens WHERE adendo = '$id'")->fetchColumn();
	if($count > 0){
		echo '
		<center>
			<div class="alert alert-danger" style="font-size:12px">
				Ainda existem itens lançado dentro deste adendo, exclua e tente novamente!
			</div>
		</center>';
		echo '
		<div class="ajax">
			<center>
				<a href="#" class="btn btn-danger btn-sm" style="width:150px" autofocus onclick=\'$(".modal").modal("hide")\'>Fechar</a>
			</center>
		</div>';
		exit;
	}else{
		echo '
		<center>
			<div class="alert alert-danger" style="font-size:12px">
				Tem certeza que deseja excluir este adendo?
			</div>
		</center>';
		echo '
		<div class="ajax">
			<center>
			<a href="javascript:void(0)" class="btn btn-success btn-sm" style="width:150px; margin-right:20px;" onclick=\'ldy("financeiro/del/excluir-adendo.php?ac=excluir&id='.$id.'",".ajax")\'>Sim</a>
		
			<a href="#" class="btn btn-danger btn-sm" style="width:150px" autofocus onclick=\'$(".modal").modal("hide")\'>Não</a>
			</center>
		</div>';	
	}

?>