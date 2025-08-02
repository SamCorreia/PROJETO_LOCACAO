<?php
	require_once('../../../config.php');
	require_once('../../../functions.php');
	$con = new DBConnection();
	verificaLogin(); getData();

	if(@$ac=='excluir') { 
		$del_query = $con->query("INSERT INTO log_delete (descricao, data, user) VALUES ('$descricao_del', now(), '$id_usuario_logado')"); 
		
		$query = $con->query("delete from usuarios where id = '$id'"); 
		if($query) { 
			echo '<center><p class="text-success">Informações deletada com sucesso!!!</p>
						<a href="#" class="btn btn-danger btn-sm" style="width:150px" autofocus onclick=\'$(".modal").modal("hide")\'>Fechar</a>
			</center>'; 
			echo '<script>$("#usuario'.$id.'").hide()</script>';
		}else{ 
			echo '<p class="text-danger">'.mysql_error().'</p>'; 
		}
		exit;
	}
	$count = $con->query("SELECT COUNT(*) FROM usuarios WHERE id = '$id'");
	$count = $count->fetchColumn();
	if($count == 0){
		echo '
		<center>
			<div class="alert alert-danger" style="font-size:12px">
			Este usuario não existe
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
		$stm = $con->query("SELECT * FROM usuarios WHERE id = '$id'");
		while($l = $stm->fetch()) {
			$nome = $l['nome'];
			$descricao_del = '(ID: '.$l['id'].' | Nome: '.$l['nome'].' | Login: '.$l['login'].')';
		}
		echo '
		<center>
			<div class="alert alert-danger" style="font-size:12px">
			Tem certeza que deseja excluir o usuario <strong>'.$nome.'</strong> permanentemente?
			</div>
		</center>';
		echo '
		<div class="ajax">
			<center>
			<a href="javascript:void(0)" class="btn btn-success btn-sm" style="width:150px; margin-right:20px;" onclick=\'ldy("gestor/del/ex-user.php?ac=excluir&id='.$id.'&descricao_del='.$descricao_del.'",".ajax")\'>Sim</a>
		
			<a href="#" class="btn btn-danger btn-sm" style="width:150px" autofocus onclick=\'$(".modal").modal("hide")\'>Não</a>
			</center>
		</div>';
	}
?>
		
