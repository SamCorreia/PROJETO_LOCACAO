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
    $('#resultadoConsulta').DataTable({
		"paging": false,
		"lengthChange": false,
		"searching": false,
		"ordering": true,
		"info": false,
		"bAutoWidth": false, 
		"columnDefs": [
		    { "orderable": false, "targets": [ -1,-2 ] }
		]
    });
	});
</script>
	<?php
		if(@$ac=='consulta'){ 
			foreach($st as $stb) { @$stu .= $stb.','; } $stu = substr($stu,0,-1);
			foreach($at as $atb) {
				@$acesso_find .= "FIND_IN_SET(".$atb.", nivel_acesso) OR ";
			}
			foreach($ob as $obc) { 
				@$select_find .= "FIND_IN_SET(".$obc.", cidade) OR ";
			}
		echo '<div class="box box-widget">
				<table id="resultadoConsulta" class="box box-widget table table-bordered table-striped table-min small" style="font-size:10px">
				<thead>
					<tr>
						<th width="2%">ID</th>
						<th width="70%">Nome</th>
						<th width="10%" style="text-align:center">Obra</th>
						<th width="10%" style="text-align:center">Login</th>
						<th width="10%" style="text-align:center">Acesso</th>
						<th width="3%"><center>SS</center></th>
						<th width="3%"><center><span class="glyphicon glyphicon-eye-open"></span></center></th>
						<th width="3%"><center><span class="glyphicon glyphicon-cog"></span></center></th>
						<th width="3%"><center><span class="glyphicon glyphicon-flag"></span></center></th>
					</tr>
				</thead> 
			<tbody>';
			$stm = $con->query("select * from usuarios where id <> '0' AND (nome like '%$busca%' OR login like '%$busca%') AND status IN($stu) AND (".$select_find." FIND_IN_SET(0, cidade)) AND (".$acesso_find." FIND_IN_SET(0, nivel_acesso)) order by nome asc");
			while($b = $stm->fetch()){ extract($b);
				echo '<tr id="usuario'.$id.'">';
				echo '<td>'.$id.'</td>';
								echo '<td>'.$nome = strtoupper ($nome).'</td>';
								$cidade = explode(",",$cidade);
								$cidade_nomes = null;
								foreach($cidade as $cic) {
									$sth = $con->query("SELECT * FROM notas_obras_cidade WHERE id = $cic");
									$nome_array = $sth->fetch(PDO::FETCH_ASSOC);
									$cidade_nomes .= "<b>".$nome_array['nome']."</b><br/>";
								}
								echo '<td width="20%" style="text-align:center">
											<div onmouseover=$("#teste'.$id.'").addClass("open").removeClass("closed"); onmouseout=$("#teste'.$id.'").addClass("closed").removeClass("open");>
												OBRAS <i class="fa fa-eye" aria-hidden="true"></i>
												<div id="teste'.$id.'" class="closed">'.$cidade_nomes.'</div>
											</div>
										</td>';
								echo '<td style="text-align:center">'.strtoupper ($login).'</td>';
								echo '<td style="text-align:center">'.strtoupper ($acesso_login).'</td>';
								if($editarss == 0){
									echo '<td><span class="label label-danger">NÃO</span></td>';
								}else{
									echo '<td><span class="label label-success">SIM</span></td>';
								}

								if($status == 0){
									echo '<td><span class="label label-success">ATIVO</span></td>';
								}else{
									echo '<td><span class="label label-danger">INATIVO</span></td>';
								}
								echo '<td width="40px"><a href="#" Onclick=\'$(".modal-body").load("gestor/editar-usuario.php?id='.$id.'")\' data-toggle="modal" data-target="#myModal" class="btn btn-success btn-xs" style="margin:0px; font-weight:bold;"><span class="glyphicon glyphicon-pencil"></span></a></td>';
								
								echo '<td width="40px"><a href="#" onclick=\'$(".modal-body").load("gestor/del/ex-user.php?&id='.$id.'")\' data-toggle="modal" data-target="#myModal2"  class="btn btn-danger btn-xs" style="margin:0px; font-weight:bold;"><span class="glyphicon glyphicon-trash"></span></a></td>';
				
				echo '</tr>';
			}
			echo '</tbody> </table> </div>';
			exit;
		} 
		?>
	
	<div style="clear: both; margin-bottom:5px;">
		<h3 style="font-family: 'Oswald', sans-serif;letter-spacing:5px;"> 
			<p style="position:relative; top:10px; left:10px;">CONSULTA <small> USUÁRIOS CADASTRADOS</small>
			<div class="buttons-top-page pull-right">
				<a href="#" style="padding:3px 15px;" title="Cadastrar Novo" class="btn btn-success btn-sm" onclick='$(".modal-body").load("gestor/cadastro-usuario.php")' data-toggle="modal" data-target="#myModal"><i class="fa fa-plus-circle" aria-hidden="true"></i> Cadastrar</a>				
				<a href="#" style="padding:3px 15px; margin:0px 10px;" title="Atualizar Pagina" class="btn btn-warning btn-sm" onclick="ldy('gestor/consulta-usuarios.php','.conteudo')"><i class="fa fa-refresh" aria-hidden="true"></i> Atualizar</a>
			</div>
			</p>
		</h3>
	</div>
	<div style="clear: both;">
		<hr></hr>
	</div>
	<form action="javascript:void(0)" id="form1" class="hidden-print">
		<div class="well well-sm" style="padding:10px 10px 5px 10px;">
			<label for=""><small>Nome:</small><br/>
				<input type="text" name="busca" placeholder="Digite algo para buscar" size="50" class="form-control input-sm">
			</label>
			<label for=""><small>Status:</small> <br/>
				<select name="st[]" class="sel" multiple="multiple">
					<option value="0" selected>ATIVO</option>
					<option value="1" selected>INATIVO</option>
				</select>
			</label>
			<label for=""><small>Obra:</small> <br/>
				<select name="ob[]" class="sel" multiple="multiple">
					<?php
						$obras = $con->query("SELECT * FROM notas_obras WHERE id IN(0,$obra_usuario)");
						while($a = $obras->fetch()) {
							echo '<option value="'.$a['id'].'" selected>'.$a['descricao'].'</option>';
						}
					?>		
				</select>
			</label>
			<label for="">
					<small>Acesso:</small> <br/>
					<select name="at[]" class="sel" multiple="multiple">
						<?php
							$acesso = $con->query("select * from acesso_usuario where tipo = '0' order by controle asc");
							while($ace = $acesso->fetch()) {
								echo '<option value="'.$ace['controle'].'" selected>'.$ace['descricao'].'</option>';
							}
						?>		
					</select>
				</label>
			<label><br/>
				<input type="submit" value="Pesquisar" style="width:150px; margin-left:10px;" onClick="post('#form1','gestor/consulta-usuarios.php?ac=consulta','.retorno')" class="btn btn-success btn-sm">
			</label>
		</div>
	</form>
	<div class="retorno"></div>
	
	<div class="modal" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="width:auto;">
		<div class="modal-dialog"  style="width:80%;">
			<div class="modal-content"> 
				<div class="modal-header box box-info" style="margin:0px;">
					<button type="button" class="close" onclick="$('.modal').modal('hide'); $('.modal-body').empty()" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="myModalLabel">Painel Equipe</h4>
				</div>
				<div class="modal-body">
					Aguarde um momento &nbsp;&nbsp; <img src="../style/img/loading.gif" alt="Carregando" width="20px"/>
				</div>
			</div>
		</div>
	</div>
	
	<div class="modal" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="width:auto;">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" onclick="$('.modal').modal('hide')" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="myModalLabel">Excluir Usuario</h4>
				</div>
				<div class="modal-body">
					Aguarde um momento &nbsp;&nbsp; <img src="../../imagens/loading.gif" alt="Carregando" width="20px"/>
				</div>
			</div>
		</div>
	</div>