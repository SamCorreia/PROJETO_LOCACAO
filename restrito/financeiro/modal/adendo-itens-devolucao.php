<?php
	require_once('../../../config.php');
	require_once('../../../functions.php');
	$con = new DBConnection();
	verificaLogin(); getData();

	if(isset($ac)){
		if($ac=='add'){
			try 
			{
				//$stm = $con->prepare("INSERT INTO contrato_itens (id_contrato, equipamento, vlr, data_retirada, tipo, adendo) VALUES (?, ?, ?, ?, ?, ?)");
				//$stm->execute(array($id_contrato, $equipamentoInput, $vlrInput, $data_retiradaInput, 1, $id_adendo));
				$stm = $con->query("INSERT INTO contrato_itens (id_contrato, equipamento, vlr, data_retirada, tipo, obs, adendo)
				SELECT '$id_contrato','$equipamentoInput', '$vlrInput', '$data_retiradaInput', '1', '$obsInput', '$id_adendo' FROM DUAL WHERE NOT EXISTS
				(SELECT equipamento FROM contrato_itens WHERE id_contrato = '$id_contrato' AND equipamento = '$equipamentoInput' AND tipo = '1' AND adendo = '$id_adendo')");
				
				$inser = $con->query("UPDATE notas_equipamentos SET controle = '0' WHERE id = '$equipamentoInput'");
				
				echo '<script>ldy("financeiro/modal/listar-adendo-devolucao.php?id_adendo='.$id_adendo.'&id_contrato='.$id_contrato.'","#listar'.$id_adendo.'") </script>';
				
				echo '<script>ldy("../functions/functions-load.php?atu=tipo_contrato2&id_contrato='.$id_contrato.'","#itens_tipo'.$id_adendo.'") </script>';
			}
			catch(PDOException $e)
			{
			  echo 'Erro: '.$e->getMessage();
			}
			
			
			exit;
		}
		exit;
	}

	$stms = $con->prepare("SELECT * FROM contrato_adendo WHERE id_contrato = ? ORDER BY id DESC");
	$stms->execute(array($id_contrato));
	while($x = $stms->fetch())
	{
?>
<script>$('#autoComplete<?= $x['id'] ?>').selectToAutocomplete();</script>
	<?php
		if($x['tipo'] == '0') {
			echo '<div class="panel panel-success" id="thisAdendo'.$x['id'].'" style="margin-top:20px;">';
			echo '<a href="#" onclick=\'$(".modal-body").load("financeiro/modal/modal-bloquear.php?tipo=bloquear&id_adendo='.$x['id'].'&id_contrato='.$id_contrato.'")\' data-toggle="modal" data-target="#myModal3"  style="margin:10px" class="btn btn-success btn-sm"><i class="fas fa-lock"></i> Finalizar</a>';
		}else if($x['tipo'] == '1'){
			echo '<div class="panel panel-danger" id="thisAdendo'.$x['id'].'" style="margin-top:20px; background-color: rgba(255, 0, 0, 0.1)">';
			if($acesso_usuario == 'MASTER'){		
				echo '<a href="#" onclick=\'$(".modal-body").load("financeiro/modal/modal-bloquear.php?tipo=desbloquear&id_adendo='.$x['id'].'&id_contrato='.$id_contrato.'")\' data-toggle="modal" data-target="#myModal3" style="margin:10px"  class="btn btn-danger btn-sm"><i class="fas fa-lock-open"></i> Desbloquear</a>';
			}else{
				echo '<a href="#" style="margin:10px" class="btn btn-danger btn-sm disabled" style="opacity:0.5"><i class="fas fa-lock-open"></i> Desbloquear</a>';
			}
		}	
		
		echo '<a href="#" onclick=\'$(".modal-body").load("financeiro/del/excluir-adendo.php?&id='.$x['id'].'")\' style="margin:10px" data-toggle="modal" data-target="#myModal2"  class="pull-right btn btn-danger btn-sm"><span class="glyphicon glyphicon-trash"></span> Excluir</a>';
		
		echo '<a href="#" onclick=\'$(".modal-body").load("financeiro/modal/editar-adendo.php?&id='.$x['id'].'")\' style="margin:10px"  data-toggle="modal" data-target="#myModal2"  class="pull-right btn btn-info btn-sm"><span class="glyphicon glyphicon-edit"></span> Editar</a>';
		
		echo '<a href="financeiro/imprimir-contrato2.php?id_adendo='.$x['id'].'&id_contrato='. $id_contrato.'" style="margin:10px" target="_blank" class="pull-right btn btn-warning btn-sm"> <span class="glyphicon glyphicon-print" aria-hidden="true"></span> Imprimir</a>';	

	?>
	<div class="panel-heading">
		<small><b><?=$con->query("SELECT nome FROM empresa_obras WHERE id = '".$x['obra']."' ")->fetchColumn()?></b> - Data: <strong><?= implode("/",array_reverse(explode("-",$x['data_adendo'])))?></strong> | Observações: <strong><?= $x['obs'] ?></strong></small>
		<div class="pull-right">
		<?php 
			if($x['tipo'] == '0') {
				echo '<a href="#" onclick=\'$(".modal-body").load("financeiro/modal/modal-bloquear-devolucao.php?tipo=bloquear&id_adendo='.$x['id'].'&id_contrato='.$id_contrato.'")\' data-toggle="modal" data-target="#myModal3"  class="btn btn-success btn-xs"><i class="fas fa-lock"></i> Finalizar</a>';
			}else if($x['tipo'] == '1'){
				if($acesso_usuario == 'MASTER'){		
					echo '<a href="#" onclick=\'$(".modal-body").load("financeiro/modal/modal-bloquear-devolucao.php?tipo=desbloquear&id_adendo='.$x['id'].'&id_contrato='.$id_contrato.'")\' data-toggle="modal" data-target="#myModal3"  class="btn btn-danger btn-xs"><i class="fas fa-lock-open"></i> Desbloquear</a>';
				}else{
					echo '<a href="#" class="btn btn-danger btn-xs disabled" style="opacity:0.5"><i class="fas fa-lock-open"></i> Desbloquear</a>';
				}
			}
		?>
		</div>
	</div>
	<div class="panel-body">
	<?php if($x['tipo'] == '0'){ ?>
		<div class="row-fluid" style="background:#FFF; margin-bottom:20px;">
			<h5>Adicionar Equipamento</h5>
			<div class="box box-widget box-fix-layout" style=" background:#f0fbf9">
				<div class="container-fluid" style="padding:5px">
					<form action="javascript:void(0)" onSubmit="post(this,'financeiro/modal/adendo-itens-devolucao.php?ac=add&id_adendo=<?= $x['id'] ?>&id_contrato=<?= $id_contrato ?>','#listar<?= $x['id'] ?>')">
						<div class="box-body">
							<div class="col-md-2 col-xs-12">
								<div class="form-group">
									<label><small>Data:</small></label>
									<input type="date" name="data_retiradaInput" min="2018-01-01" max="<?= $todayTotal ?>" value="<?= $todayTotal ?>" class="form-control input-sm" required />
								</div>
							</div>
							<div class="col-md-3 col-xs-12">
								<div class="form-group">
									<div id="itens_tipo<?= $x['id'] ?>">
										<label><small>Selecione o Equipamento:</small></label>
										<select id="autoComplete<?= $x['id'] ?>" name="equipamentoInput" class="form-control input-sm selectAuto" required>
											<option value="" selected>Selecione um item</option>
											<?php
											$query2 = $con->query("SELECT * FROM contrato_itens WHERE id_contrato = '$id_contrato' GROUP BY equipamento");
											while($a = $query2->fetch()){
												$stm = $con->query("SELECT * FROM notas_equipamentos WHERE controle = '1' AND id = '".$a['equipamento']."' ORDER BY patrimonio ASC");
												while($b = $stm->fetch()){
													echo '<option value="'.$b['id'].'">'.$b['patrimonio'].' - '.$con->query("SELECT descricao FROM notas_cat_e WHERE id = '".$b['categoria']."' ")->fetchColumn().' '.$con->query("SELECT descricao FROM notas_cat_sub WHERE id = '".$b['sub_categoria']."' ")->fetchColumn().'</option>';
												}
											}
											?>
										</select>
									</div>
								</div>
							</div>
							<div class="col-md-3 col-xs-12">
								<div class="form-group">
									<label><small>Observações:</small></label>
									<input type="text" name="obsInput" class="form-control input-sm">
								</div>
							</div>
							<div class="col-md-2 col-xs-12">
								<div class="form-group">
									<label><small>Valor:</small></label>
									<input type="number" name="vlrInput" step="0.01" min="0" class="form-control input-sm">
								</div>
							</div>
							<div class="col-md-1 col-xs-12">
								<div class="form-group center-input"><br/>
									<input type="submit" class="btn btn-sm btn-success" value="Adicionar" style="width:150px;">
								</div> 
							</div>
						</div>
					</form>
				</div>
			</div>
			<script>ldy("financeiro/modal/listar-adendo-devolucao.php?id_adendo=<?= $x['id'] ?>&id_contrato=<?= $id_contrato ?>","#listar<?= $x['id'] ?>")</script>
			<div id="listar<?= $x['id'] ?>" style="margin-top:20px;"></div>
		</div>
		<?php }else if($x['tipo'] == '1'){ ?>
		<div class="row-fluid" style="background:#FFF; margin-bottom:20px;">
			<script>ldy("financeiro/modal/listar-adendo-devolucao.php?id_adendo=<?= $x['id'] ?>&id_contrato=<?= $id_contrato ?>","#listar<?= $x['id'] ?>")</script>
			<div id="listar<?= $x['id'] ?>" style="margin-top:20px;"></div>
		</div>
		<?php } ?>
	</div>
</div>
<?php	
	}
	?>