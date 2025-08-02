<?php
	require_once('../../config.php');
	require_once('../../functions.php');
	$con = new DBConnection();
	verificaLogin(); getData();
	$stm = $con->query("SELECT *, (select razao_social from localferramentas_cadastroempresa where id = contrato_dados.empresa) as empresa_nome, (select cnpj from localferramentas_cadastroempresa where id = contrato_dados.empresa) as cnpj FROM contrato_dados WHERE id = '$id_contrato'");
	while($b = $stm->fetch()){ 
		extract ($b); 
	}
?>
<script>
$(document).ready(function () {
	$('#autoComplete').selectToAutocomplete();
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
	$('#resultadoConsulta').DataTable({
		"paging": false,
		"lengthChange": false,
		"searching": true,
		"ordering": true,
		"info": false,
		"bAutoWidth": true
    });
});
</script>
<?php
	if(isset($ac)){
		if($ac=='listar'){
			echo '<div class="box box-warning">
				<table id="resultadoConsulta" class="table table-bordered table-striped">
				<thead>
				<tr style="font-size: smaller">
					<th style="text-align:center; width:10%;">Item:</th>
					<th style="text-align:center; width:10%;">Data:</th>
					<th style="width:50%">Equipamento:</th>
					<th style="text-align:center; width:10%;">Tipo:</th>
					<th style="text-align:center; width:10%;">Vlr:</th>';
				echo '</tr>
				</thead>
				<tbody>';
			$stm = $con->prepare("SELECT * FROM contrato_itens WHERE id_contrato = ? ORDER BY contrato_itens.data_retirada desc");
			$stm->execute(array($id_contrato));
			$se2 = 0; $total_vlr = 0;
			while($s = $stm->fetch())
			{
				$se2 += 1;
				echo '<tr id="thisTr'.$s['id'].'">';
				$stc = $con->query("SELECT * FROM notas_equipamentos WHERE id = '".$s['equipamento']."'");
				while($e = $stc->fetch()){
					$categoria = $con->query("SELECT descricao FROM notas_cat_e WHERE id = '".$e['categoria']."' ")->fetchColumn();
					$sub_categoria = $con->query("SELECT descricao FROM notas_cat_sub WHERE id = '".$e['sub_categoria']."' ")->fetchColumn();
					$patrimonio = $e['patrimonio'];
				}
				echo '<td>'.$se2.'</td>';
				echo '<td data-sort="'.$s['data_retirada'].'">'.implode("/",array_reverse(explode("-",$s['data_retirada']))).'</td>';
				echo '<td>'.$patrimonio.' - '.$categoria.' '.$sub_categoria.'</td>';
				if($s['tipo'] == '0'){
					echo '<td align="center" style="font-size:11px;"><span class="label label-success">LOCADO</span></td>';
				}else if($s['tipo'] == '1'){
					echo '<td align="center" style="font-size:11px;"><span class="label label-danger">DEVOLVIDO</span></td>';
				}
				echo '<td data-sort="'.$s['vlr'].'" align="center">R$ '.number_format($s['vlr'],2,",",".").'</td>';
				$total_vlr += $s['vlr'];
				echo '</tr>';
			}
			echo ' </tbody>';
			echo '<tfoot>';
			echo '<tr> <td colspan="4" align="right"> <strong>Total</strong> </td> <td style="text-align:center"> <b>R$ '.number_format($total_vlr,2,",",".").'</b></td> </tr>';
			echo '</tfoot>';
			echo '</table> </div>';
			exit;
		}
		exit;
	}
?>
<section class="container-fluid conteudo-contrato" style="clear:both; padding-left:0px; margin-left:0px">
	<div class="resultadoCadastro"></div>
	<table class="table table-condensed small tabelaTeste" style="margin:0px; margin-top:10px;">
	<tr>
		<td style="border:none; padding:0px;">
			<ul class="nav nav-tabs hoverFix" role="tablist">
				<li class="active"><a href="#home" style="color:#333; padding:15px;" role="tab" data-toggle="tab" onclick="$('.resultado').load('financeiro/editar-contrato-devolucao.php?id_contrato=<?=$id_contrato ?>')">Informações do Contrato</a></li>
				<li><a href="#profile" style="color:#333; padding:15px;" role="tab" data-toggle="tab" onclick="$('#profile').load('financeiro/modal/cadastrar-adendo-devolucao.php?id_contrato=<?=$id_contrato ?>')">Cadastrar Adendos</a></li>
				<li class="pull-right">
					<div>
						<a href="financeiro/imprimir-contrato1.php?id_contrato=<?= $id_contrato ?>" target="_blank" style="letter-spacing:5px; margin-top:5px; margin-right:10px;" class="hidden-xs hidden-print pull-right btn btn-warning btn-sm"> <span class="glyphicon glyphicon-print" aria-hidden="true"></span>&nbsp;Imprimir</a>
					</div>
				</li>
			</ul>
		</td>
	</tr>
	</table>
	<div class="tab-content">
	<div class="tab-pane active" id="home">
		<section class="container-fluid" style="padding:0px;">
			<div class="box box-primary" style="padding-top:10px">
				<table class="table table-striped table-bordered">
					<tr class="small">
						<th>Empresa:</th>
						<th>Data:</th>
					</tr>
					<tr>
						<td><?= $cnpj.' - '.$empresa_nome ?></td>
						<td><?= implode("/",array_reverse(explode("-",$data_contrato))) ?></td>
					</tr>
				</table>
			</div>
		</section>
		<div class="box box-widget box-fix-layout" style=" background:#f0fbf9">
			
		</div>
		<script>ldy("financeiro/editar-contrato-devolucao.php?ac=listar&id_contrato=<?php echo $id_contrato ?>","#listar")</script>
		<div id="listar" style="margin-top:20px;"></div>
	</div>
	<div class="tab-pane" id="profile"></div>
</section>

<div class="modal" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="width:auto;">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" onclick="$('.modal').modal('hide')" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="myModalLabel">Excluir Item</h4>
			</div>
			<div class="modal-body">
				Aguarde um momento &nbsp;&nbsp; <img src="../style/img/loading.gif" alt="Carregando" width="20px"/>
			</div>
		</div>
	</div>
</div>