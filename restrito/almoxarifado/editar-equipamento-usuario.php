<?php
	require_once('../../config.php');
	require_once('../../functions.php');
	$con = new DBConnection();
	verificaLogin(); getData();
?>
<script>
$(document).ready(function () {
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
while($l = $sql->fetch()){ extract($l); }

?>
	<div class="ajax"></div>
	<div class="container-fluid" style="padding:0px">
		<form action="javascript:void(0)" enctype="multipart/form-data" class="formulario-info">
			<div class="panel">
				<div class="panel-body" style="width:100%">	
					<div class="col-xs-12 col-sm-4">
						<div class="col-xs-6">
							<label style="width:100%">Nº Motor: <br/> <input type="text" name="placa" value="<?php echo $placa; ?>" class="form-control input-sm" disabled /></label>
						</div>
						<div class="col-xs-6">
							<label style="width:100%">BP: <br/><input type="text" name="patrimonio" value="<?php echo $patrimonio; ?>" onfocus="$(this).mask('aaa-9999')" class="form-control input-sm up" disabled /></label>
						</div>
	
						<div class="col-xs-6">
							<label style="width:100%">Marca: </br><input type="text" name="marca" value="<?php echo $marca; ?>" class="form-control input-sm up" disabled /></label>
						</div>
						<div class="col-xs-6">
							<label style="width:100%">Nº Apolise: <br><input type="text" name="patrimonio2" value="<?php echo $patrimonio2; ?>" class="form-control input-sm" disabled /></label>
						</div>
						<div class="col-xs-6">
							<label style="width:100%">Valor: <br><input type="number" step="0.1" name="valor" value="<?php echo $valor; ?>" class="form-control input-sm" disabled /></label>
						</div>
						<div class="col-xs-6">
							<label style="width:100%">Seguro: <br>
								<select name="seguro" class="form-control input-sm" disabled>
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
							<label style="width:100%">Nota: <br><input type="number" name="desconto" value="<?php echo $desconto; ?>" step="0.01" class="form-control input-sm" disabled /></label>
						</div>
						<div class="col-xs-6">
							<label style="width:100%">Ano: <br><input type="text" name="ano" value="<?php echo $ano; ?>" class="form-control input-sm" disabled /></label>
						</div>
						<div class="col-xs-6">
							<label style="width:100%">Chassi / Nº série: <br><input style="width:100%;"type="text" name="chassi" value="<?php echo $chassi; ?>" class="form-control input-sm up" disabled /></label>
						</div>
					</div>
			
					<div class="col-xs-12 col-sm-4">
						<div class="col-xs-12">
							<label style="width:100%">Empresa: <br>
								<select name="empresa" class="form-control input-sm" disabled>
									<option value="">SEM EMPRESA</option>
									<?php 
										$empresasql = $con->query("select * from localferramentas_cadastroempresa WHERE id = '$empresa'");
										while($l = $empresasql->fetch()) {
											echo '<option value="'.$l['id'].'" selected>'.$l['razao_social'].'</option>'; 
										}
									?>			
								</select>
							</label>
						</div>
						<div class="col-xs-12">
							<label for "" style="width:100%"> Categoria: 
								<select name="categoria" style="width:100%" class="form-control input-sm" disabled>
									<option value="0">SELECIONE UMA CATEGORIA</option>
									<?php 
										$stms = $con->query("select * from notas_cat_e where id = '$categoria'");
										while($l = $stms->fetch()){
											echo '<option value="'.$l['id'].'" selected>'.$l['descricao'].'</option>'; 
										}
									?>		
								</select>
							</label>
						</div>
						<div class="col-xs-12">
							<label id="itens23" style="width:100%">Sub-Categoria:
								<label style="width:100%">
									<select name="sub_categoria" style="width:100%" class="form-control input-sm" disabled>
										<?php 
											$stms = $con->query("select * from notas_cat_sub where id = '$sub_categoria'");
											while($l = $stms->fetch()){
												echo '<option value="'.$l['id'].'" selected>'.$l['descricao'].'</option>'; 
											}
										?>		

									</select>
								</label>
							</label>
						</div>
						<div class="col-xs-12">
							<label style="width:100%">Fornecedor: <br><input type="text" name="obs" value="<?php echo $obs; ?>" class="form-control input-sm" disabled /></label>
						</div>
					</div>
		
					<div class="col-xs-12 col-sm-4">
					<div class="col-xs-12">
							<label style="width:100%">Obra:<br/>
								<select name="cidade" onChange="$('#itens-obra').load('almoxarifado/cadastro-equipamentos.php?atu=ac&obra_2=' + $(this).val() + '');" class="input-sm form-control" style="width:100%" disabled>
									<?php 
										$obras_consulta = $con->query("select * from notas_obras_cidade WHERE id IN($cidade_usuario) AND id <> 0 order by nome asc");
										while($l = $obras_consulta->fetch()) {
											echo '<option value="'.$l['id'].'">'.$l['nome'].'</option>'; 
										}
									?>	
								</select>
							</label>
						</div>
						<div class="col-xs-12">
							<label style="width:100%" id="itens-obra">
								<label style="width:100%">Contrato:<br/>
									<select name="obraInput" class="form-control input-sm" style="width:100%" disabled>
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
								<select name="situacao" class="form-control input-sm" disabled>
									<option value="" selected disabled>00 - SEM TIPO </option>
									<?php 
										$situacaosql = $con->query("select * from notas_eq_situacao where id = '$situacao'");
										while($l = $situacaosql->fetch()) {
											echo '<option value="'.$l['id'].'" selected>'.$l['descricao'].'</option>';
										}
									?>			
								</select>
							</label>
						</div>
						<div class="col-xs-6">
							<label style="width:100%">Status: 
								<select class="form-control input-sm" name="statusInput" disabled>
									<?php 
									if($status == '0') {
										echo '<option value="0" selected>ATIVO</option>';
										echo '<option value="1">INATIVO</option>';
									}else if($status == '1'){
										echo '<option value="0">ATIVO</option>';
										echo '<option value="1" selected>INATIVO</option>';
									}
									?>
								</select>
							</label>	
						</div>
						<div class="col-xs-6">
							<label>Entrada: <br><input type="date" name="entrada" value="<?php echo $entrada ?>" class="form-control input-sm" disabled /></label>
						</div>
						<div class="col-xs-6">
							<label>Saída: <br><input type="date" name="saida" value="<?php echo $saida ?>" class="form-control input-sm" disabled /></label><br/>
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