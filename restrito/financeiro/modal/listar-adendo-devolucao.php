<?php
	require_once('../../../config.php');
	require_once('../../../functions.php');
	$con = new DBConnection();
	verificaLogin(); getData();
?>
<script>
$('#tabela<?= $id_adendo ?>').DataTable({
		"paging": false,
		"lengthChange": false,
		"searching": false,
		"ordering": true,
		"info": false,
		"bAutoWidth": true
});
</script>
<?php
		echo '<div class="box box-warning">
				<table id="tabela'.$id_adendo.'" class="table table-condensed">
				<thead>
				<tr style="font-size: smaller">
					<th style="text-align:center; width:10%;">Item</th>
					<th style="text-align:center; width:10%;">Data:</th>
					<th style="width:30%">Equipamento:</th>
					<th style="width:20%">Obs:</th>
					<th style="text-align:center; width:10%;">Tipo:</th>
					<th style="text-align:center; width:10%;">Vlr:</th>
					<th style="text-align:center; width:10%;">Excluir:</th>
					</tr>
				</thead>
				<tbody>';
			$stm = $con->prepare("SELECT contrato_itens.*, notas_equipamentos.patrimonio, notas_equipamentos.sub_categoria, notas_equipamentos.id as id_equip, (SELECT tipo FROM contrato_adendo WHERE id = contrato_itens.adendo) as tipo_adendo  FROM contrato_itens left JOIN notas_equipamentos ON contrato_itens.equipamento = notas_equipamentos.id WHERE contrato_itens.id_contrato = ? AND contrato_itens.adendo = ? ORDER BY id desc");
			$stm->execute(array($id_contrato, $id_adendo));
			$se = 0; $total_vlr = 0;
			while($s = $stm->fetch())
			{
				$se += 1;
				$total_item = 0;
				echo '<tr id="thisTr'.$s['id'].'">';
				echo '<td>'.$se.'</td>';
				echo '<td data-sort="'.$s['data_retirada'].'">'.implode("/",array_reverse(explode("-",$s['data_retirada']))).'</td>';
				echo '<td>'.$s['patrimonio'].' - '.$con->query("SELECT descricao FROM notas_cat_sub WHERE id = '".$s['sub_categoria']."' ")->fetchColumn().'</td>';
				echo '<td class="text-warning" style="font-weight:bold; font-size:10px">'.$s['obs'].'</span></td>';
				if($s['tipo'] == '0'){
					echo '<td align="center" style="font-size:11px;"><span class="label label-success">LOCADO</span></td>';
				}else if($s['tipo'] == '1'){
					echo '<td align="center" style="font-size:11px;"><span class="label label-danger">DEVOLVIDO</span></td>';
				}
				echo '<td data-sort="'.$s['vlr'].'" align="center">R$ '.number_format($s['vlr'],2,",",".").'</td>';
				$total_vlr += $s['vlr'];
					echo '<td align="center">';
						if($s['tipo_adendo'] == '0' && $s['tipo'] == '1') {
							echo '<a href="#" onclick=\'$(".modal-body").load("financeiro/del/excluir-contrato-item-devolucao.php?&id='.$s['id'].'&equipamento='.$s['equipamento'].'&id_contrato='.$id_contrato.'")\' data-toggle="modal" data-target="#myModal2"  class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-trash"></span></a>';
						}else{
							echo '<a href="#" class="btn btn-danger btn-xs disabled"><span class="glyphicon glyphicon-trash"></span></a>';
						}
					echo '</td>';
				echo '</tr>';
			}
			echo ' </tbody>';
			echo '<tfoot>';
			echo '<tr> <td colspan="4" align="right"> <strong>Total</strong> </td> <td style="text-align:center" colspan="2"> <b>R$ '.number_format($total_vlr,2,",",".").'</b></td> </tr>';
			echo '</tfoot>';
			echo '</table> </div>';
			exit;
?>