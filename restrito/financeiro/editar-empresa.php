<?php
	require_once('../../config.php');
	require_once('../../functions.php');
	$con = new DBConnection();
	verificaLogin(); getData();
	if(isset($ac)){
		if($ac=='add'){
			try 
			{
				$stm = $con->prepare("INSERT INTO empresa_obras (id_empresa, nome, endereco) VALUES (?, ?, ?)");
				$stm->execute(array($id_empresa, $nomeInput, $enderecoInput));
				echo '<script>ldy("financeiro/editar-empresa.php?ac=listar&id_empresa='.$id_empresa.'","#listar") </script>';
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
					<th>Nome</th>
					<th>Endereço</th>';
					if($acesso_usuario == 'MASTER'){
						echo '<th style="text-align:center;">Excluir</th>';
					}
				echo '</tr>
				</thead>
				<tbody>';
			$stmc = $con->prepare("SELECT * FROM empresa_obras WHERE id_empresa = ? ");
			$stmc->execute(array($id_empresa));
			$se1 = 0;
			while($s = $stmc->fetch())
			{
				$se1 += 1;
				echo '<tr id="thisTr'.$s['id'].'">';
				echo '<td width="5%">'.$se1.'</td>';
				echo '<td>'.$s['nome'].'</td>';
				echo '<td>'.$s['endereco'].'</td>';
				if($acesso_usuario == 'MASTER'){
					echo '
					<td width="5%" align="center">
						<a href="#" onclick=\'$(".modal-body").load("financeiro/del/excluir-empresa-obra.php?&id_item='.$s['id'].'")\' data-toggle="modal" data-target="#myModal2"  class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-trash"></span></a>
					</td>';
				}
			echo '</tr>';
			}
			echo ' </tbody> </table> </div>';
			exit;
		}
		if($ac == 'update'){
			$stm = $con->prepare("SELECT COUNT(*) FROM localferramentas_cadastroempresa WHERE cnpj = ? AND id <> ?");
			$stm->execute(array($cnpj, $id));
			$count = $stm->fetchColumn();
			if($count != 0){
				echo '
				<div class="alert alert-danger">
					<h4>Empresa com este CNPJ já esta cadastrada!</h4>
					<p>O CNPJ: '.$cnpj.' ja está cadastrada no sistema, favor consultar as empresas ja cadastradas e tentar editar novamente!!!</p>
				</div>';
			}else{
				$query = $con->query("UPDATE `localferramentas_cadastroempresa` SET `tipo_empresa`='$tipoInput', `cnpj`='$cnpj', `razao_social`='$razao_social', `telefone`='$telefone', `celular`='$celular', `contato`='$contato', `email`='$email', `endereco`='$endereco', `seguimento`='$seguimento', `data_retorno`='$data_retorno', `obs`='$obs', `visita`='$visita' WHERE id = '$id'");
				if($query) {
					echo '<div class="alert alert-success" role="alert">Informações atualizadas com sucesso!</div>';
				}else{ 
					echo '<div class="alert alert-danger" role="alert">'.mysql_error().'</div>';
				}
			}
			exit;
		}
	}
?>
<?php
$stm = $con->query("SELECT * FROM localferramentas_cadastroempresa WHERE id = '$id'");
while($b = $stm->fetch()){ extract ($b); }
?>

<section class="content">
	<div class="resultadoEditar"></div>
	<div class="box box-primary" style="padding-top:10px">
		<form action="javascript:void(0)" onSubmit="post(this,'financeiro/editar-empresa.php?ac=update&id=<?=$id ?>','.resultadoEditar')">
			<div class="box-body">
				<div class="container">
				<div class="col-xs-12 col-md-4">
					<div class="form-group">
						<label>Tipo Empresa:</label>
						<select name="tipoInput" class="form-control input-sm" required>
							<option value="0" <?= ($tipo_empresa == '0' ? 'selected' : '') ?>>CLIENTE</option>
							<option value="1" <?= ($tipo_empresa == '1' ? 'selected' : '') ?>>FORNECEDOR</option>
							<option value="2" <?= ($tipo_empresa == '2' ? 'selected' : '') ?>>CLIENTE / FORNECEDOR</option>
						</select>
					</div>
				</div>
				<div class="col-xs-12 col-md-4">
					<div class="form-group">
						<label>CNPJ:</label>
						<input type="text" name="cnpj" onfocus="$(this).mask('99.999.999/9999-99')" value="<?= $cnpj; ?>" class="juridica form-control input-sm"  required>
					</div>
				</div>
				<div class="col-xs-12 col-md-4">
					<div class="form-group">
						<label>Razão Social:</label>
						<input type="text" name="razao_social" value="<?=$razao_social ?>" placeholder="Nome da Empresa" size="80" class="todosInput form-control input-sm" required >
					</div>
				</div>
				<div class="col-xs-12 col-md-4">
					<div class="form-group">
						<label>Telefone:</label>
						<input type="text" name="telefone" value="<?=$telefone ?>" onfocus="$(this).mask('(99) 9999-9999')" placeholder="(__) ____-____" size="80" class="todosInput form-control input-sm" >
					</div>
				</div>
				<div class="col-xs-12 col-md-4">
					<div class="form-group">
						<label>Celular:</label>
						<input type="text" name="celular" value="<?=$celular ?>" onfocus="$(this).mask('(99) 99999999?9')" placeholder="(__) _________" size="80" class="todosInput form-control input-sm" >
					</div>
				</div>
				<div class="col-xs-12 col-md-4">
					<div class="form-group">
						<label>Representante:</label>
						<input type="text" name="contato" value="<?=$contato ?>" placeholder="Responsável Legal" size="80" class="todosInput form-control input-sm" >
					</div>
				</div>
				<div class="col-xs-12 col-md-4">
					<div class="form-group">
						<label>E-mail</label>
						<input type="email" name="email" value="<?=$email ?>" placeholder="E-mail para contato" size="80" class="todosInput form-control input-sm" >
					</div>
				</div>
				<div class="col-xs-12 col-md-4">
					<div class="form-group">
						<label>Endereço:</label>
						<input type="text" name="endereco" value="<?=$endereco ?>" placeholder="Endereço completo" size="80" class="todosInput form-control input-sm" >
					</div>
				</div>
				<div class="col-xs-12 col-md-4">
					<div class="form-group">
						<label>Seguimento:</label>
						<input type="text" name="seguimento" value="<?=$seguimento ?>" placeholder="Seguimento" size="80" class="todosInput form-control input-sm" >
					</div>
				</div>
				<div class="col-xs-12 col-md-4">
					<div class="form-group">
						<label>Data Retorno:</label>
						<input type="date" name="data_retorno" value="<?=$data_retorno ?>" class="todosInput form-control input-sm" >
					</div>
				</div>
				<div class="col-xs-12 col-md-4">
					<div class="form-group">
						<label>Visita:</label>
						<input type="text" name="visita" value="<?=$visita ?>" class="todosInput form-control input-sm" >
					</div>
				</div>
				<div class="col-xs-12 col-md-12">
					<div class="form-group">
						<label>Observações:</label>
						<textarea name="obs" class="todosInput form-control input-sm" style="resize:none"><?=$obs ?></textarea>
					</div>
				</div>
				<div class="col-xs-12 col-md-12">
					<div class="box-footer" style="text-align:center">
						<input type="submit" style="width:50%" class="btn btn-success btn-sm submit-empresa" value="Salvar">
					</div>
				</div>
				</div>
			</div>
		</form>
	</div>
</section>
<hr/>
<div class="panel panel-info">
	<div class="panel-heading"><h5><small><b>Obras da Empresa:</b></small></h5></div>
	<div class="panel-body">
	<section class="content">
	<div class="box box-widget box-fix-layout" style=" background:#f0fbf9">
		<div class="container-fluid" style="padding:5px">
		<form action="javascript:void(0)" onSubmit="post(this,'financeiro/editar-empresa.php?ac=add&id_empresa=<?php echo $id ?>','#listar')">
			<div class="box-body">
				<div class="col-md-3 col-xs-12">
					<div class="form-group">
						<label><small>Nome da Obra:</small></label>
						<input type="text" name="nomeInput" class="form-control input-sm" required>
					</div>
				</div>
				<div class="col-md-6 col-xs-12">
					<div class="form-group">
						<label><small>Endereço:</small></label>
						<input type="text" name="enderecoInput" class="form-control input-sm" autocomplete="off" required />
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
	<script>ldy("financeiro/editar-empresa.php?ac=listar&id_empresa=<?php echo $id ?>","#listar")</script>
	<div id="listar" style="margin-top:20px;"></div>
</section>
	</div>
</div>