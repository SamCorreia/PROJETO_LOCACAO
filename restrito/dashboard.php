<?php
	require_once('../config.php');
	require_once('../functions.php');
	$con = new DBConnection();
	verificaLogin(); getData();
?>
<div class="row">
	<div class="col-xs-6">
		<div id='calendar-container'>
			<div id='calendar'></div>
		</div>
	</div>
	<div class="col-xs-6">
		<section class="content-header" id="alert1" style="margin:0px; padding:5px; border-bottom:1px solid #ccc; margin-bottom:20px;">
			<h3 style="font-family: 'Oswald', sans-serif; letter-spacing:1px;">Equipamentos Disponiveis<small> </small></h3>
		</section>
			<?php
				echo '<div class="box box-widget">
					<table id="resultadoConsulta" class="box box-widget table table-condensed table-color">
					<thead>
						<tr>
							<th style="text-align:center"><i class="fa fa-list-alt" aria-hidden="true"></i></th>
							<th style="text-align:center">Descrição:</th>
							<th style="text-align:center">Total</th>
						</tr>
					</thead> 
				<tbody>';
			$stm2 = $con->query("select *, (SELECT COUNT(*) FROM notas_equipamentos WHERE sub_categoria = notas_cat_sub.id AND status IN(0) AND situacao IN(2,1) AND controle IN(0)) AS total_equipamentos FROM notas_cat_sub WHERE oculto = '0' order by descricao asc");
			$se2 = 0;
			$total_equipamentos_g = 0;
			while($c = $stm2->fetch()){
				if($c['total_equipamentos'] <> 0){
					$se2 += 1;
					echo '<tr>';
					echo '<td style="text-align:center">'.$se2.'</td>';
					echo '<td>'.$c['descricao'].'</td>';
					echo '<td style="text-align:center">'.$c['total_equipamentos'].'</td>';
					echo '</tr>';
					$total_equipamentos_g += $c['total_equipamentos'];
				}
			}
			echo '<tfoot>';
			echo '<tr class="active"><td colspan="2"><b>Total</b></td><td style="text-align:center"><b>'.$total_equipamentos_g.'</b></td></tr>';
			echo '</tfoot></div>';
		?>
	</div>
</div>