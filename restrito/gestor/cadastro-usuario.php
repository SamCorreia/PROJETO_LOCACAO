<?php
	require_once('../../config.php');
	require_once('../../functions.php');
	error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
	$con = new DBConnection();
	verificaLogin(); getData();
?>
<script>
$(function () {
	$('.sel').multiselect({
      buttonClass: 'btn btn-sm',
	  numberDisplayed: 1,
	  maxHeight: 200,
	  includeSelectAllOption: true,
	  selectAllText: "Selecionar todos",
	  enableFiltering: true,
	  enableCaseInsensitiveFiltering: true,
	  selectAllValue: 'multiselect-all'
	}); 
});
</script>
<?php 
if(@$atu=='ac'){
	echo '<label style="width:100%">Contrato:<br/>
			<select name="ob[]" class="sel" style="width:100%" multiple="multiple" required>';
				$obras = $con->query("SELECT * FROM notas_obras WHERE cidade IN($obra_2) AND id <> 0 ORDER BY descricao ASC");
				while($a = $obras->fetch()) {
					echo '<option value="'.$a['id'].'" selected>'.$a['descricao'].'</option>';
				}
	echo '</select></label>';
	exit;
}
if(@$ac=='ins') {
		foreach($ob as $obs) { @$obu .= $obs.','; } $obu = substr($obu,0,-1);
		foreach($ci as $cis) { @$cid .= $cis.','; } $cid = substr($cid,0,-1);
		$senha_crip = md5($senhaCadastro);
		$stm = $con->prepare("SELECT COUNT(*) FROM usuarios WHERE login = ?");
		$stm->execute(array($loginCadastro));
		$count = $stm->fetchColumn();
		if($count != 0){
			echo '
			<div class="alert alert-danger">
				Usuario já cadastrado no sistema. Tente novamente!
			</div>';
		}else{
			$sql = $con->query("INSERT INTO usuarios (nome,login,senha,nivel_acesso, acesso_login, obra,cidade,data_cadastro) VALUES ('$nome','$loginCadastro','$senha_crip', '8' , 'USUARIO', '$obu' , '$cid', now() )");	
			if($sql){		 
				echo '<div class="alert alert-success">Usuario criado com sucesso! Atualize a pagina</div>';	
			}else{
				echo '<div class="alert alert-danger">ERROR!!! USUARIO NÃO CADASTRADO, TENTE NOVAMENTE!</div>';	
			}
		}
		exit;
	} 
?>

<div class="retornoUsuario"></div>

<div class="panel panel-default">
<div class="panel-heading"><small>CADASTRO DE USUARIOS</small></h5></div>
<form action="javascript:void(0)" onSubmit="post(this,'gestor/cadastro-usuario.php?ac=ins','.retornoUsuario'); this.reset()" enctype="multipart/form-data" >
  <div class="panel-body">
	<div class="col-md-6">
		<div class="col-xs-12">
			<label style="width:100%">Nome:<input type="text" name="nome" value="" class="form-control input-sm" size="20" required/></label><br/>
		</div>
		<div class="col-xs-12">
			<label style="width:100%">Login:<input type="text" name="loginCadastro" value="" class="form-control input-sm" size="10" required></label><br/>
		</div>
		<div class="col-xs-12">
			<label style="width:100%">Senha:<input type="password" name="senhaCadastro" value="" class="form-control input-sm" size="20" required></label><br/>
		</div>
		<div class="col-xs-12">
			<label style="width:100%">OBRA:<br/>
				<select name="ci[]" onChange="$('#itens').load('gestor/cadastro-usuario.php?atu=ac&obra_2=' + $(this).val() + '');" class="sel" style="width:100%" multiple="multiple" required>
						<?php 
							$obras_consulta = $con->query("select * from notas_obras_cidade WHERE id <> 0 order by nome asc");
							while($l = $obras_consulta->fetch()) {
								echo '<option value="'.$l['id'].'" selected>'.$l['nome'].'</option>'; 
							}
						?>	
				</select>
			</label>
		</div>
		<div class="col-xs-12" id="itens">
			<label style="width:100%">CONTRATO:<br/>
				<select name="ob[]" class="sel" style="width:100%" multiple="multiple" required>
					<?php 
							$obras_consulta = $con->query("select * from notas_obras WHERE id <> 0 order by descricao asc");
							while($l = $obras_consulta->fetch()) {
								echo '<option value="'.$l['id'].'" selected>'.$l['descricao'].'</option>'; 
							}
						?>	
				</select>
			</label>
		</div>
		<div class="col-xs-12" style="text-align:center">
			<input type="submit" class="btn btn-success btn-sm"  style="width:50%" value="Salvar"/>
		</div>
	</div>

</div>

</form>
