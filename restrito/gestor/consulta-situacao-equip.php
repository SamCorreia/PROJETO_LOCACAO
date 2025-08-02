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
$(document).ready(function() {
});
</script>


	<div class="retorno"></div>
	<div style="clear: both; margin-bottom:5px;">
		<h3 style="font-family: 'Oswald', sans-serif;letter-spacing:5px;"> 
			<p style="position:relative; top:10px; left:10px;">CONSULTA <small> SITUAÇÃO</small>
			<div class="buttons-top-page pull-right">
				<!--<a href="#" style="padding:3px 15px;" title="Cadastrar Novo" class="btn btn-success btn-sm" onclick='$(".modal-body").load("gestor/cadastro-categoria-equip.php")' data-toggle="modal" data-target="#myModal"><i class="fa fa-plus-circle" aria-hidden="true"></i> Cadastrar</a>-->			
				<a href="#" style="padding:3px 15px; margin:0px 10px;" title="Atualizar Pagina" class="btn btn-warning btn-sm" onclick="ldy('gestor/consulta-situacao-equip.php','.conteudo')"><i class="fa fa-refresh" aria-hidden="true"></i> Atualizar</a>
			</div>
			</p>
		</h3>
	</div>
	<div style="clear: both;">
		<hr></hr>
	</div>
	<div class="col-md-12">
		<table class="table table-striped table-condensed small">
			<thead>
			<tr>
				<th> <span class="glyphicon glyphicon-eject" aria-hidden="true"></span> </th>
				<th>Descrição</th>
				<th><center> <span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> </center></th>
				<th><center> <span class="glyphicon glyphicon-cog" aria-hidden="true"></span> </center></th>
			</tr>
			</thead>
		<tbody>
        <?php
		$se = 0;
		$stm = $con->query("select * FROM notas_eq_situacao WHERE id <> '0'");
		while($b = $stm->fetch()){ extract($b);
			$se += 1;
			echo '<tr id="cupom'.$id.'">';
				echo '<td width="3%">'.$se.'</td>';
				echo '<td>'.$descricao.'</td>';
				echo '<td><center>';
				if($status == '0'){
					echo '<span class="btn btn-xs small btn-success" style="font-size:8px">ATIVO</span>';
				}else{
					echo '<span class="btn btn-xs small btn-danger" style="font-size:8px">INATIVO</span>';
				}
				echo '</center>
				</td>';
				echo '<td width="40px" style="text-align:center"><a href="#" Onclick=\'$(".modal-body").load("gestor/editar-situacao-eqp.php?id='.$id.'")\' data-toggle="modal" data-target="#myModal" class="btn btn-primary btn-xs" style="margin:0px; font-weight:bold; font-size:12px"><i class="fas fa-edit"></i></a></td>';
			echo '</tr>';
			
		}
        ?>
		</tbody>
		</table>
	</div>
	
	<div class="modal" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="width:auto;">
		<div class="modal-dialog">
			<div class="modal-content"> 
				<div class="modal-header box box-info" style="margin:0px;">
					<button type="button" class="close" onclick="$('.modal').modal('hide'); $('.modal-body').empty()" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="myModalLabel">Painel Informações</h4>
				</div>
				<div class="modal-body">
					Aguarde um momento &nbsp;&nbsp; <img src="../style/img/loading.gif" alt="Carregando" width="20px"/>
				</div>
			</div>
		</div>
	</div>
