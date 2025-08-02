<?php
	require_once('../../../config.php');
	require_once('../../../functions.php');
	$con = new DBConnection();
	verificaLogin(); getData();
	if(isset($ac)){
		if($ac=='cadastrarVenda'){
			$query = $con->prepare("INSERT INTO contrato_venda (id_contrato, item, qtd, vlr, obs, data_venda) VALUES (?,?,?,?,?,?)");
			$query->execute(array($id_contrato, $itemInput, $qtdInput, $vlrInput, $obsInput, $data_vendaInput));
			if($query) {
				echo '<div class="alert alert-success" role="alert">Informações cadastrada com sucesso!</div>';
			}else{ 
				echo '<div class="alert alert-danger" role="alert">'.mysql_error().'</div>';
			}
		}
		exit;
	}
?>

	<div class="divVenda" style="margin-top:10px;"></div>
	<div class="box box-widget box-fix-layout">
		<div class="container-fluid" style="padding:5px">
			<form action="javascript:void(0)" onSubmit="post(this,'financeiro/modal/cadastrar-vendas-prov.php?ac=cadastrarVenda&id_contrato=<?php echo $id_contrato ?>','.divVenda')">
				<div class="box-body">
					<div class="col-md-12 col-xs-12">
						<div class="form-group">
							<label><small>Data:</small></label>
							<input type="date" name="data_vendaInput" min="2018-01-01" max="<?= $todayTotal ?>" value="<?= $todayTotal ?>" class="form-control input-sm" required />
						</div>
					</div>
					<div class="col-md-12 col-xs-12">
						<div class="form-group">
							<label><small>Item:</small></label>
							<select id="combobox" name="itemInput" class="form-control input-sm selectAuto" required>
								<option value="" disabled selected>Selecione um item</option>
								<?php 
									$obras_consulta = $con->query("SELECT * FROM notas_itens WHERE categoria = '2'");
									while($l = $obras_consulta->fetch()) {
										echo '<option value="'.$l['id'].'">'.$l['descricao'].' <sup>('.$l['valor30'].')</sup></option>'; 
									}
								?>	
							</select>
						</div>
					</div>
					<div class="col-md-12 col-xs-12" style="padding:10px 0px">
						<div class="col-md-6 col-xs-12">
							<div class="form-group">
								<label><small>Quantidade:</small></label>
								<input type="text" name="qtdInput" class="form-control input-sm" />
							</div>
						</div>
						<div class="col-md-6 col-xs-12">
							<div class="form-group">
								<label><small>Valor (UN)</small></label>
								<input type="text" name="vlrInput" placeholder="R$" class="form-control input-sm" />
							</div>
						</div>
					</div>
					<div class="col-md-12 col-xs-12">
						<div class="form-group">
							<label><small>Observações:</small></label>
							<input type="text" name="obsInput" class="form-control input-sm" />
						</div>
					</div>
					<div class="col-md-12 col-xs-12">
						<div class="form-group" style="text-align:center"><br/>
							<input type="submit" class="btn btn-success" value="Cadastrar" style="width:150px;">
						</div> 
					</div>
				</div>
			</form>
		</div>
	</div>