<?php
	require_once('../../../config.php');
	require_once('../../../functions.php');
	$con = new DBConnection();
	verificaLogin(); getData();
	if(isset($ac)){
		if($ac=='aprovar') { 
			try
			{
				$query = $con->prepare("UPDATE orcamento_dados SET `status` = '1' WHERE id = ?"); 
				$query->execute(array($id));
			}
			catch(PDOException $e)
			{
				echo 'Erro: ' . $e->getMessage();
			}
			if($query) { 
				echo '<center><p class="text-success">Informações atualizada com sucesso!</p>
							<a href="#" class="btn btn-danger btn-sm" style="width:150px" autofocus onclick=\'$(".modal").modal("hide")\'>Fechar</a></center>'; 
				//echo '<script>$("#thisTd'.$id.'").hide()</script>';
				echo "<script>ldy('financeiro/consulta-orcamento.php?ac=atualizar&id_recup=".$id."', '#thisTd".$id."')</script>";
			}else{ 
				echo '<p class="text-danger">'.mysql_error().'</p>'; 
			}
			exit;
		}
		if($ac=='reprovar') { 
			try
			{
				$query = $con->prepare("UPDATE orcamento_dados SET `status` = '2' WHERE id = ?"); 
				$query->execute(array($id));
			}
			catch(PDOException $e)
			{
				echo 'Erro: ' . $e->getMessage();
			}
			if($query) { 
				echo '<center><p class="text-success">Informações atualizada com sucesso!</p>
							<a href="#" class="btn btn-danger btn-sm" style="width:150px" autofocus onclick=\'$(".modal").modal("hide")\'>Fechar</a></center>'; 
				//echo '<script>$("#thisTd'.$id.'").hide()</script>';
				echo "<script>ldy('financeiro/consulta-orcamento.php?ac=atualizar&id_recup=".$id."', '#thisTd".$id."')</script>";
			}else{ 
				echo '<p class="text-danger">'.mysql_error().'</p>'; 
			}
			exit;
		}
	}
	echo '
		<center>
			<div class="alert alert-danger" style="font-size:12px">
			Este orçamento foi aprovado ou reprovado?
			</div>
		</center>';
	echo '
		<div class="ajax">
			<center>
			<a href="javascript:void(0)" class="btn btn-success btn-sm" style="width:150px; margin-right:20px;" onclick=\'ldy("financeiro/del/modal-aprovar.php?ac=aprovar&id='.$id.'",".ajax")\'>Aprovado</a>
			
			<a href="javascript:void(0)" class="btn btn-danger btn-sm" style="width:150px; margin-right:20px;" onclick=\'ldy("financeiro/del/modal-aprovar.php?ac=reprovar&id='.$id.'",".ajax")\'>Reprovado</a>
			
			</center>
		</div>';
?>