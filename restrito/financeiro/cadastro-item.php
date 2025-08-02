<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__.'/debug.log');
	require_once('../../config.php');
	require_once('../../functions.php');
	$con = new DBConnection();
	verificaLogin(); getData();

	if(isset($ac)){
			$query = $con->prepare("INSERT INTO notas_itens (descricao, categoria, valor30, valor15, valor07, valor03, status, data_edit) VALUES (?,?,?,?,?,?,?,?)");
			$query->execute(array($descricaoInput, $categoriaInput, $valor30Input, $valor15Input, $valor07Input, $valor03Input, $statusInput, $todayTotal));
			if($query){
					echo '
					<div class="alert alert-success">
						<h4>Item Cadastrado com Sucesso!</h4>
					</div>';
			}
			
			echo "<script> $('html, body').animate({ scrollTop: $('#alert1').offset().top }, 'slow'); </script>";
		exit;
	}
	
	?>
<section class="content-header" id="alert1">
	<h1>Cadastro Item <small> </small></h1>
</section>
<section class="content">
	<div class="resultadoCadastro"></div>
	<div class="box box-primary" style="padding-top:10px">
		<form action="javascript:void(0)" onSubmit="post(this,'financeiro/cadastro-item.php?ac=ins','.resultadoCadastro')">
			<div class="box-body">
				<div class="form-group">
					<label>Descrição:</label>
					<input type="text" name="descricaoInput" class="form-control input-sm" required>
				</div>
				<div class="form-group">
					<label>Categoria:</label>
					<select name="categoriaInput" class="form-control input-sm" required>
						<?php
							$acesso = $con->query("select * from notas_categorias where status = '0'");
							while($ace = $acesso->fetch()) {
								echo '<option value="'.$ace['id'].'">'.$ace['descricao'].'</option>';
							}
						?>		
					</select>
				</div>
				<div class="form-group">
					<label>Valor 30:</label>
					<input type="number" step="0.01" name="valor30Input" class="form-control input-sm" required>
				</div>
				<div class="form-group">
					<label>Valor 15:</label>
					<input type="number" step="0.01" name="valor15Input" class="form-control input-sm" required>
				</div>
				<div class="form-group">
					<label>Valor 07:</label>
					<input type="number" step="0.01" name="valor07Input" class="form-control input-sm" required>
				</div>
				<div class="form-group">
					<label>Valor 03:</label>
					<input type="number" step="0.01" name="valor03Input" class="form-control input-sm" required>
				</div>
				<div class="form-group">
					<label style="width:100%">Status:
						<select name="statusInput" class="form-control input-sm">
							<option value="0" selected>ATIVO</option>
							<option value="1">INATIVO</option>
						</select>
					</label>
				</div>
				<div class="box-footer" style="text-align:center">
					<input type="submit" style="width:50%" class="btn btn-success btn-sm submit-empresa" value="Salvar">
				</div>
			</div>
		</form>
	</div>
</section>