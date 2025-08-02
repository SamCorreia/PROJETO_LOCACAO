<?php
	require_once('../../../config.php');
	require_once('../../../functions.php');
	$con = new DBConnection();
	verificaLogin(); getData();
	if(isset($ac)){
		if($ac=='excluir') { 
			try
			{
				$del_query = $con->query("INSERT INTO log_delete (descricao, data, user) VALUES ('$descricao_del', now(), '$id_usuario_logado')"); 
				
				$query = $con->prepare("DELETE FROM contrato_dados WHERE id = ?"); 
				$query->execute(array($id));
				$query2 = $con->prepare("DELETE FROM contrato_adendo WHERE id_contrato = ?"); 
				$query2->execute(array($id));
			}
			catch(PDOException $e)
			{
				echo 'Erro: ' . $e->getMessage();
			}
			if($query) { 
				echo '<center><p class="text-success">Informações deletada com sucesso!</p>
							<a href="#" class="btn btn-danger btn-sm" style="width:150px" autofocus onclick=\'$(".modal").modal("hide")\'>Fechar</a></center>'; 
				echo '<script>$("#thisTr'.$id.'").hide()</script>';
			}else{ 
				echo '<p class="text-danger">'.mysql_error().'</p>'; 
			}
			exit;
		}
	}
	$count = $con->query("SELECT COUNT(*) FROM contrato_itens WHERE id_contrato = '$id'")->fetchColumn();
	if($count > 0){
		echo '
		<center>
			<div class="alert alert-danger" style="font-size:12px">
				Ainda existem itens lançado dentro deste contrato, exclua e tente novamente!
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
		$select = $con->query("SELECT * FROM contrato_dados WHERE id = '$id'");
		$row = $select->fetch();
		$descricao_del = '(ID:'.$row['id'].' | Empresa: '.$row['empresa'].')';
		echo '
		<center>
			<div class="alert alert-danger" style="font-size:12px">
				Tem certeza que deseja excluir este contrato permanentemente?
			</div>
		</center>';
		echo '
		<div class="ajax">
			<center>
			<a href="javascript:void(0)" class="btn btn-success btn-sm" style="width:150px; margin-right:20px;" onclick=\'ldy("financeiro/del/excluir-contrato.php?ac=excluir&id='.$id.'&descricao_del='.$descricao_del.'",".ajax")\'>Sim</a>
		
			<a href="#" class="btn btn-danger btn-sm" style="width:150px" autofocus onclick=\'$(".modal").modal("hide")\'>Não</a>
			</center>
		</div>';	
	}

?>