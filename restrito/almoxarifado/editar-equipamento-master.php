<?php
	require_once('../../config.php');
	require_once('../../functions.php');
	$con = new DBConnection();
	verificaLogin(); getData();
?>
<?php 
	if(isset($ac)){
		if($ac=='add'){
			try 
			{
				$stm = $con->prepare("INSERT INTO notas_historico_equipamentos (id_equipamento, data, historico) VALUES (?, ?, ?)");
				$stm->execute(array($id_equipamento, $dataInput, $obsInput));
				echo '<script>ldy("almoxarifado/editar-equipamento-master.php?ac=listar&id_equipamento='.$id_equipamento.'","#listar") </script>';
				echo 'ok';
			}
			catch(PDOException $e)
			{
			  echo 'Erro: '.$e->getMessage();
			}
			exit;
		}
		if($ac=='listar'){
			echo '<div class="box box-warning">
				<table class="table table-bordered table-striped">
				<thead>
				<tr style="font-size: smaller">
					<th>Nº</th>
					<th>Data</th>
					<th>Observações:</th>';
					if($acesso_usuario == 'MASTER'){
						echo '<th style="text-align:center;">Excluir</th>';
					}
				echo '</tr>
				</thead>
				<tbody>';
			$stmc = $con->prepare("SELECT * FROM notas_historico_equipamentos WHERE id_equipamento = ? ");
			$stmc->execute(array($id_equipamento));
			$se1 = 0;
			while($s = $stmc->fetch())
			{
				$se1 += 1;
				echo '<tr id="thisTr'.$s['id'].'">';
				echo '<td width="5%">'.$se1.'</td>';
				echo '<td width="5%">'.implode("/",array_reverse(explode("-",$s['data']))).'</td>';
				echo '<td>'.$s['historico'].'</td>';
				if($acesso_usuario == 'MASTER'){
					echo '
					<td width="5%" align="center">
						<a href="#" onclick=\'$(".modal-body").load("almoxarifado/del/excluir-historico-equipamento.php?&id_item='.$s['id'].'")\' data-toggle="modal" data-target="#myModal2"  class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-trash"></span></a>
					</td>';
				}
			echo '</tr>';
			}
			echo ' </tbody> </table> </div>';
			exit;
		}
		if($ac == 'up') {
			try{
				if($statusInput == 1){
					if($saida == ''){
						echo '<div class="alert alert-danger" role="alert"><strong>Atenção!!! </strong>Favor preencher a data de saida, e tentar novamente.</div>';
					}else{
						$query = $con->query("UPDATE notas_equipamentos SET obs = '$obs', placa = '$placa', patrimonio = '$patrimonio', marca = '$marca', patrimonio2 = '$patrimonio2', valor = '$valor', justificativa = '$seguro', desconto = '$desconto', patrimonio2 = '$patrimonio2', chassi = '$chassi', ano = '$ano', empresa = '$empresa', obra = '$obraInput', categoria = '$categoria', sub_categoria = '$sub_categoria', status = '$statusInput', situacao = '$situacao', entrada = '$entrada', saida = '$saida', user_edit = '$id_usuario_logado', data_edit = now() WHERE id = '$id'");
						echo '<div class="alert alert-success" role="alert">Informações atualizadas com sucesso!</div>';
					}
				}else{
					$query = $con->query("UPDATE notas_equipamentos SET obs = '$obs', placa = '$placa', patrimonio = '$patrimonio', marca = '$marca', patrimonio2 = '$patrimonio2', valor = '$valor', justificativa = '$seguro', desconto = '$desconto', patrimonio2 = '$patrimonio2', chassi = '$chassi', ano = '$ano', empresa = '$empresa', categoria = '$categoria', sub_categoria = '$sub_categoria', status = '$statusInput', situacao = '$situacao', entrada = '$entrada', saida = '$saida', user_edit = '$id_usuario_logado', data_edit = now() WHERE id = '$id'");
					echo '<div class="alert alert-success" role="alert">Informações atualizadas com sucesso!</div>';
				}
			}
			catch( PDOException $e ){
				echo '<div class="alert alert-danger" role="alert">Algo aconteceu de errado!</div>'; 
			}
			exit;
		} 
	exit;
	}
?>
<link rel="stylesheet" href="../style/css/combobox.css"/>
<script>
$(document).ready(function () {
	$("#combobox").combobox();
	$('.sel').multiselect({
		buttonClass: 'btn btn-sm', 
		numberDisplayed: 1,
		maxHeight: 500,
		includeSelectAllOption: true,
		selectAllText: "Selecionar todos",
		enableFiltering: true,
		enableCaseInsensitiveFiltering: true,
		selectAllValue: 'multiselect-all',
		buttonWidth: '100%'
	}); 
});
</script>
<?php
$sql = $con->query("SELECT * FROM notas_equipamentos WHERE id = '$id'"); 
while($l = $sql->fetch()){ $obraReal = $l['obra']; extract($l); }

if(@$atu == 'categoria'){
	echo '<label style="width:100%"><small>Sub-Categoria</small><select name="sub_categoria" style="width:100%" class="form-control input-sm combobox">';
	$stms = $con->query("select * from notas_cat_sub where associada in($categoria) order by descricao asc");
	while($l = $stms->fetch()){
		echo '<option value="'.$l['id'].'">'.$l['descricao'].'</option>';
	}
	echo '</select></label>';
}
		
?>

	<div class="ajax"></div>
	
	<div class="container-fluid" style="padding:0px">
		<form action="javascript:void(0)" onSubmit="post(this,'almoxarifado/editar-equipamento-master.php?ac=up&id=<?php echo $id ?>','.ajax')" enctype="multipart/form-data" class="formulario-info">
			<div class="panel">
				<!--onfocus="$(this).mask('aaa-9999')"-->
				<div class="panel-body" style="width:100%">	
					<div class="col-xs-12 col-sm-4">
						<div class="col-xs-6">
							<label style="width:100%">Nº Motor: <br/> <input type="text" name="placa" value="<?php echo $placa; ?>" class="form-control input-sm"></label>
						</div>
						<div class="col-xs-6">
							<label style="width:100%">BP: <br/><input type="text" name="patrimonio" value="<?php echo $patrimonio; ?>" onfocus="$(this).mask('aaa-*999')" class="form-control input-sm up"  required></label>
						</div>
	
						<div class="col-xs-6">
							<label style="width:100%">Marca: </br><input type="text" name="marca" value="<?php echo $marca; ?>" class="form-control input-sm up" required></label>
						</div>
						<div class="col-xs-6">
							<label style="width:100%">Nº Apolise: <br><input type="text" name="patrimonio2" value="<?php echo $patrimonio2; ?>" class="form-control input-sm"></label>
						</div>
						<div class="col-xs-6">
							<label style="width:100%">Valor: <br><input type="number" step="0.1" name="valor" value="<?php echo $valor; ?>" class="form-control input-sm"  required></label>
						</div>
						<!--<div class="col-xs-6">
							<label style="width:100%">Dia Pagamento: <br><input type="text" name="dia_pagamento" value="<?php echo $dia_pagamento; ?>" class="form-control input-sm"  required></label>
						</div>-->
						<div class="col-xs-6">
							<label style="width:100%">Seguro: <br>
								<select name="seguro" class="form-control input-sm" required>
									<?php 
									if($justificativa == 'SIM'){
										echo '<option value="" disabled>Selecione uma opção</option>';
										echo '<option value="SIM" selected>SIM</option>';
										echo '<option value="NAO">NÃO</option>';
									}else if($justificativa == 'NAO'){
										echo '<option value="" disabled>Selecione uma opção</option>';
										echo '<option value="SIM">SIM</option>';
										echo '<option value="NAO" selected>NÃO</option>';
									}else{
										echo '<option value="" disabled selected>Selecione uma opção</option>';
										echo '<option value="SIM">SIM</option>';
										echo '<option value="NAO">NÃO</option>';
									}
									?>
								</select>
							</label>
						</div>
						
						<div class="col-xs-6">
							<label style="width:100%">Nota: <br><input type="number" name="desconto" value="<?php echo $desconto; ?>" step="0.01" class="form-control input-sm"  required /></label>
						</div>
						<div class="col-xs-6">
							<label style="width:100%">Ano: <br><input type="text" name="ano" value="<?php echo $ano; ?>" class="form-control input-sm"  required></label>
						</div>
						<div class="col-xs-6">
							<label style="width:100%">Chassi / Nº série: <br><input style="width:100%;"type="text" name="chassi" value="<?php echo $chassi; ?>" class="form-control input-sm up"  required></label>
						</div>
					</div>
			
					<div class="col-xs-12 col-sm-4">
						<div class="col-xs-12">
							<label style="width:100%">Empresa: <br>
								<select id="combobox" name="empresa" class="form-control input-sm" required>
									<option value="">SEM EMPRESA</option>
									<?php 
										$empresasql = $con->query("select * from localferramentas_cadastroempresa WHERE tipo_empresa IN(1,2) order by razao_social asc");
										while($l = $empresasql->fetch()) {
											if($empresa==$l['id']) { 
												echo '<option value="'.$l['id'].'" selected>'.$l['razao_social'].'</option>'; 
											} else { 
												echo '<option value="'.$l['id'].'">'.$l['razao_social'].'</option>'; 
											}
										}
									?>			
								</select>
							</label>
						</div>
						<div class="col-xs-12">
							<label for "" style="width:100%"> Categoria: 
								<select name="categoria" onChange="$('#itens23').load('../functions/functions-load.php?atu=categoria&control=1&categoria=' + $(this).val() + '');" style="width:100%" class="form-control input-sm">
									<option value="0">SELECIONE UMA CATEGORIA</option>
									<?php 
										$stms = $con->query("select * from notas_cat_e where oculto = '0' order by descricao asc");
										while($l = $stms->fetch()){
											if($categoria==$l['id']) {
												echo '<option value="'.$l['id'].'" selected>'.$l['descricao'].'</option>'; 
											} else { 
												echo '<option value="'.$l['id'].'">'.$l['descricao'].'</option>'; 
											}
										}
									?>		
								</select>
							</label>
						</div>
						<div class="col-xs-12">
							<label id="itens23" style="width:100%">Sub-Categoria:
								<label style="width:100%">
									<select name="sub_categoria" style="width:100%" class="form-control input-sm">
										<?php 
											$stms = $con->query("select * from notas_cat_sub where associada in($categoria) order by descricao asc");
											while($l = $stms->fetch()){
												if($sub_categoria==$l['id']) { 
													echo '<option value="'.$l['id'].'" selected>'.$l['descricao'].'</option>'; 
												} else { 
													echo '<option value="'.$l['id'].'">'.$l['descricao'].'</option>'; 
												}
											}
										?>		

									</select>
								</label>
							</label>
						</div>
						<div class="col-xs-12">
							<label style="width:100%">Fornecedor: <br><input type="text" name="obs" value="<?php echo $obs; ?>" class="form-control input-sm" /></label>
						</div>
					</div>
		
					<div class="col-xs-12 col-sm-4">
					<div class="col-xs-12">
							<label style="width:100%">Obra:<br/>
								<select name="cidade" onChange="$('#itens-obra').load('almoxarifado/cadastro-equipamentos.php?atu=ac&obra_2=' + $(this).val() + '');" class="input-sm form-control" style="width:100%"  required>
									<?php 
									
										$cidadeSelect = $con->query("SELECT cidade FROM notas_obras WHERE id = '$obra'")->fetch(PDO::FETCH_ASSOC);
										$obras_consulta = $con->query("select * from notas_obras_cidade WHERE id IN($cidade_usuario) AND id <> 0 order by nome asc");
										while($l = $obras_consulta->fetch()) {
											if($cidadeSelect['cidade'] == $l['id']){
												echo '<option value="'.$l['id'].'" selected>'.$l['nome'].'</option>'; 
											}else{
												echo '<option value="'.$l['id'].'">'.$l['nome'].'</option>'; 
											}
										}
									?>	
								</select>
							</label>
						</div>
						<div class="col-xs-12">
							<label style="width:100%" id="itens-obra">
								<label style="width:100%">Contrato:<br/>
									<select name="obraInput" class="form-control input-sm" style="width:100%" required>
										<?php 
											$obras_consulta = $con->query("select * from notas_obras WHERE id IN($obra_usuario) AND cidade IN($cidade_usuario) AND id <> 0 order by descricao asc");
											while($l = $obras_consulta->fetch()) {
												if($obra == $l['id']){
													echo '<option value="'.$l['id'].'" selected>'.$l['descricao'].'</option>'; 
												}else{
													echo '<option value="'.$l['id'].'">'.$l['descricao'].'</option>';
												} 
											}
										?>	
									</select>
								</label>
							</label>
						</div>
						<div class="col-xs-6">
							<label style="width:100%">Tipo: <br>
								<select name="situacao" class="form-control input-sm" required>
									<option value="" selected disabled>00 - SEM TIPO </option>
									<?php 
										$situacaosql = $con->query("select * from notas_eq_situacao where status = '0' AND id <> '0' order by descricao asc");
										while($l = $situacaosql->fetch()) {
											if($situacao==$l['id']) { 
												echo '<option value="'.$l['id'].'" selected>'.$l['descricao'].'</option>'; 
											} else { 
												echo '<option value="'.$l['id'].'">'.$l['descricao'].'</option>'; 
											}
										}
									?>			
								</select>
							</label>
						</div>
						<div class="col-xs-6">
							<label style="width:100%">Status: 
								<select class="form-control input-sm" name="statusInput">
									<?php 
									if($status == '0') {
										echo '<option value="0" selected>ATIVO</option>';
										echo '<option value="1">INATIVO</option>';
										echo '<option value="2">ROUBADO</option>';
									}else if($status == '1'){
										echo '<option value="0">ATIVO</option>';
										echo '<option value="1" selected>INATIVO</option>';
										echo '<option value="2">ROUBADO</option>';
									}else if($status == '2'){
										echo '<option value="0">ATIVO</option>';
										echo '<option value="1">INATIVO</option>';
										echo '<option value="2" selected>ROUBADO</option>';
									}
									?>
								</select>
							</label>	
						</div>
						<div class="col-xs-6">
							<label>Entrada: <br><input type="date" name="entrada" value="<?php echo $entrada ?>" class="form-control input-sm" ></label>
						</div>
						<div class="col-xs-6">
							<label>Saída: <br><input type="date" name="saida" value="<?php echo $saida ?>" class="form-control input-sm"   ></label><br/>
						</div>
						<div class="col-xs-12" style="text-align:center">
							<label style="width:50%">
								<input type="submit" style="width:100%; height:30px; margin-top:20px" value="Atualizar" class="btn btn-info btn-sm">
							</label>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
<hr/>
<div class="panel panel-info">
	<div class="panel-heading"><h5><small><b>Historico Equipamento:</b></small></h5></div>
	<div class="panel-body">
	<section class="content">
	<div class="box box-widget box-fix-layout" style=" background:#f0fbf9">
		<div class="container-fluid" style="padding:5px">
		<form action="javascript:void(0)" onSubmit="post(this,'almoxarifado/editar-equipamento-master.php?ac=add&id_equipamento=<?php echo $id ?>','#listar')">
			<div class="box-body">
				<div class="col-md-3 col-xs-12">
					<div class="form-group">
						<label><small>Data:</small></label>
						<input type="date" name="dataInput" class="form-control input-sm" required>
					</div>
				</div>
				<div class="col-md-6 col-xs-12">
					<div class="form-group">
						<label><small>Observações:</small></label>
						<input type="text" name="obsInput" class="form-control input-sm" autocomplete="off" required />
					</div>
				</div>
				<div class="col-md-3 col-xs-12">
					<div class="form-group center-input"><br/>
						<input type="submit" class="btn btn-success" value="Adicionar" style="width:150px;">
					</div> 
				</div>
			</div>
		</form>
		</div>
	</div>
	<script>ldy("almoxarifado/editar-equipamento-master.php?ac=listar&id_equipamento=<?php echo $id ?>","#listar")</script>
	<div id="listar" style="margin-top:20px;"></div>
</section>
	</div>
</div>