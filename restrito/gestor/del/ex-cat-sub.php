<?php
	require_once('../../../config.php');
	require_once('../../../functions.php');
	$con = new DBConnection();
	verificaLogin(); getData();

	if(@$ac=='excluir') { 
		$del_query = $con->query("INSERT INTO log_delete (descricao, data, user) VALUES ('$descricao_del', now(), '$id_usuario_logado')"); 
		
		$query = $con->query("delete from notas_cat_sub where id = '$id'"); 
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
	
	$count = $con->query("SELECT COUNT(*) FROM notas_cat_sub WHERE id = '$id'");
	$count = $count->fetchColumn();
	if($count == 0){
		echo '
			<center>
				<div class="alert alert-danger" style="font-size:12px">
					Sub-Categoria não encontrada!!!
				</div>
			</center>';
		echo '
			<div class="ajax">
				<center>			
					<a href="#" class="btn btn-danger btn-sm" style="width:150px" autofocus onclick=\'$(".modal").modal("hide")\'>Sair</a>
				</center>
			</div>';
		exit;
	}
	$stm = $con->query("SELECT * FROM notas_cat_sub WHERE id = '$id'");
	while($l = $stm->fetch()) {
		$descricao = $l['descricao'];
		$descricao_del = 'SUB-CATEGORIA (ID: '.$l['id'].' | Descrição: '.$l['descricao'].' | Associada: '.$l['associada'].')';
	}
	echo '
		<center>
			<div class="alert alert-danger" style="font-size:12px">
				Tem certeza que deseja excluir esta sub-categoria <strong>'.$descricao.'</strong> permanentemente?
			</div>
		</center>';
	echo '
		<div class="ajax">
			<center>
			<a href="javascript:void(0)" class="btn btn-success btn-sm" style="width:150px; margin-right:20px;" onclick=\'ldy("gestor/del/ex-cat-sub.php?ac=excluir&id='.$id.'&descricao_del='.$descricao_del.'",".ajax")\'>Sim</a>
		
			<a href="#" class="btn btn-danger btn-sm" style="width:150px" autofocus onclick=\'$(".modal").modal("hide")\'>Não</a>
			</center>
		</div>';
?>
