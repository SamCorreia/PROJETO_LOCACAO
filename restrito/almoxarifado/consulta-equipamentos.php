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
		"bAutoWidth": false,
		"columnDefs": [
		    { "orderable": false, "targets": [ -1,-2 ] }
		]
    });
	});
</script>
	<?php
	if(isset($ac)){
		if(@$relatorio == '1'){
			if(empty($cat) || empty($sub) || empty($to) || empty($si) || empty($ct)){ 
				echo '<span class="text-danger">Selecione todos os campos obrigatorios</span>'; 
				exit; 
			}else{
				$catg = ''; $subg = ''; $sta = ''; $sit = ''; $ctt = '';
				foreach($cat as $cats){ $catg .= $cats.','; } $catg = substr($catg,0,-1); 
				foreach($sub as $subs)  { $subg .= $subs.',';  } $subg = substr($subg,0,-1);
				foreach($to as $tos)  { $sta .= $tos.',';   } $sta = substr($sta,0,-1);
				foreach($si as $sis)  { $sit .= $sis.',';   } $sit = substr($sit,0,-1); 
				foreach($ct as $cts)  { $ctt .= $cts.',';   } $ctt = substr($ctt,0,-1); 
			}
			echo '<div class="container-fluid hidden-xs visible-print" style="border-bottom:1px solid #CCC; padding-bottom:20px; margin:15px;">
				<div class="col-xs-2" style="padding:0px">
					<img src="../style/img/litoralrent-logo.png" class="img-responsive" width="100px" />
				</div>
				<div class="col-xs-10" style="text-align:right; font-size:8px">
					<b><small>LITORAL RENT LOCADORA E CONSTRUÇÕES LTDA.</small></b><br/>
					Av Antônio Emmerick, 723, Jardim Guassu, São Vicente/SP - CEP 11370-001<br/>
					Telefone: (13) 3043-4211 &nbsp;&nbsp;&nbsp; Email: contato@litoralrent.com.br
					<br/>
				</div>
			</div>
			';
			echo '<center>
				<h4 class="hidden-xs visible-print" style="font-family: \'Oswald\', sans-serif; letter-spacing:4px; text-align:center; margin-bottom:20px;">
					<p><small>RELATORIO EQUIPAMENTOS <br/> SIMPLES</small></p>
				</h4>
				</center>';
			echo '<div class="box box-widget">
					<table id="resultadoConsulta" class="box box-widget table  table-striped table-min small" style="font-size:10px">
					<thead>
						<tr>
							<th style="text-align:center"><i class="fa fa-list-alt" aria-hidden="true"></i></th>
							<th class="hidden-print" style="text-align:center">ID:</th>
							<th style="text-align:center">BP:</th>
							<th style="text-align:center">Chassi:</th>
							<th style="text-align:center">Sub-Categoria:</th>
							<th style="text-align:center">Tipo:</th>
							<th class="hidden-print" style="text-align:center">Valor:</th>
							
							<th style="text-align:center">Obs:</th>
							<th style="text-align:center">Status:</th>';
							echo '<th class="hidden-print" style="text-align:center">Editar:</th>';
							if($acesso_usuario == 'MASTER'){
								echo '<th class="hidden-print" style="text-align:center">Excluir:</th>';
							}
							echo '
						</tr>
					</thead> 
				<tbody>';
				
				$stm = $con->query("SELECT * FROM notas_equipamentos WHERE categoria IN($catg) AND sub_categoria IN($subg) AND status IN($sta) AND situacao IN($sit) AND controle IN($ctt)");
				$c = 0; $total_equipamentos = 0;
				while($b = $stm->fetch()){
					$c += 1;
					echo '<tr id="thisTr'.$b['id'].'">';
					echo '<td style="text-align:center">'.$c.'</td>';
					echo '<td class="hidden-print" style="text-align:center">'.$b['id'].'</td>';
					echo '<td>'.$b['patrimonio'].'</td>';
					echo '<td>'.$b['chassi'].'</td>';
					echo '<td>'.$con->query("SELECT descricao FROM notas_cat_sub WHERE id = '".$b['sub_categoria']."' ")->fetchColumn().'</td>';
					echo '<td>'.$con->query("SELECT descricao FROM notas_eq_situacao WHERE id = '".$b['situacao']."' ")->fetchColumn().'</td>';
					echo '<td class="hidden-print">R$ '.number_format($b['valor'],2,",",".").'</td>';
					$total_equipamentos += $b['valor'];
					echo '<td>'.$con->query("SELECT historico FROM notas_historico_equipamentos where id_equipamento = '".$b['id']."' ORDER BY id DESC LIMIT 1")->fetchColumn().'</td>';
					echo '<td>';
					if($b['controle'] == '0'){ 
						echo '<span class="label label-success">DISPONIVEL</label>'; 
					}else{ 
						echo '<span class="label label-warning">LOCADO</label>'; 
					}
					echo '</td>';
					if($acesso_usuario == 'MASTER' || $acesso_usuario == 'EQUIPAMENTOS'){
						echo '<td class="hidden-print" style="text-align:center" width="5px"><a href="#" Onclick=\'$(".modal-body").load("almoxarifado/editar-equipamento-master.php?id='.$b['id'].'")\' data-toggle="modal" data-target="#myModal" class="btn btn-success btn-xs" style="margin:0px; font-weight:bold;"><span class="glyphicon glyphicon-pencil"></span></a></td>';
					}else{
						echo '<td class="hidden-print" style="text-align:center" width="5px"><a href="#" Onclick=\'$(".modal-body").load("almoxarifado/editar-equipamento-usuario.php?id='.$b['id'].'")\' data-toggle="modal" data-target="#myModal" class="btn btn-success btn-xs" style="margin:0px; font-weight:bold;"><span class="glyphicon glyphicon-pencil"></span></a></td>';
					}
					if($acesso_usuario == 'MASTER'){
						echo '<td class="hidden-print" style="text-align:center" width="5px"><a href="#" Onclick=\'$(".modal-body").load("almoxarifado/del/excluir-equipamento.php?id='.$b['id'].'")\' data-toggle="modal" data-target="#myModal2" class="btn btn-danger btn-xs" style="margin:0px; font-weight:bold;"><span class="glyphicon glyphicon-trash"></span></a></td>';
					}
					
					echo '</tr>';
				}
				echo '</tbody> </table> </div>';
				echo '<h3 class="pull-right">Total R$:'.number_format($total_equipamentos,2,",",".").'</h3>';
			exit;
		}
		if(@$relatorio == '2'){
			if(empty($cat) || empty($sub) || empty($to) || empty($si)){ 
				echo '<span class="text-danger">Selecione todos os campos obrigatorios</span>'; 
				exit; 
			}else{
				$catg = ''; $subg = ''; $sta = ''; $sit = '';
				foreach($cat as $cats){ $catg .= $cats.','; } $catg = substr($catg,0,-1); 
				foreach($sub as $subs)  { $subg .= $subs.',';  } $subg = substr($subg,0,-1);
				foreach($to as $tos)  { $sta .= $tos.',';   } $sta = substr($sta,0,-1);
				foreach($si as $sis)  { $sit .= $sis.',';   } $sit = substr($sit,0,-1); 
			}
			echo '<div class="container-fluid hidden-xs visible-print" style="border-bottom:1px solid #CCC; padding-bottom:20px; margin:15px;">
				<div class="col-xs-2" style="padding:0px">
					<img src="../style/img/litoralrent-logo.png" class="img-responsive" width="100px" />
				</div>
				<div class="col-xs-10" style="text-align:right; font-size:8px">
					<b><small>LITORAL RENT LOCADORA E CONSTRUÇÕES LTDA.</small></b><br/>
					Av Antônio Emmerick, 723, Jardim Guassu, São Vicente/SP - CEP 11370-001<br/>
					Telefone: (13) 3043-4211 &nbsp;&nbsp;&nbsp; Email: contato@litoralrent.com.br
					<br/>
				</div>
			</div>
			';
			echo '<center>
				<h4 class="hidden-xs visible-print" style="font-family: \'Oswald\', sans-serif; letter-spacing:4px; text-align:center; margin-bottom:20px;">
					<p><small>RELATORIO EQUIPAMENTOS</small></p>
				</h4>
			</center>';
				$stm = $con->query("SELECT * FROM notas_equipamentos WHERE categoria IN($catg) AND sub_categoria IN($subg) AND status IN($sta) AND situacao IN($sit)");
				$c = 0;
				while($b = $stm->fetch()){ extract($b);
					$c += 1;
					echo '<div class="box box-widget">
					<table class="box box-widget table table-striped table-condensed" style="font-size:12px; border:1px solid #ccc;">';
				echo '
				<thead>
					<tr>
						<th style="text-align:center">'.$con->query("select razao_social from localferramentas_cadastroempresa where id = '$empresa'")->fetchColumn().'</th>
						
						<th style="text-align:center"><small>Categoria: &nbsp;</small> '.$con->query("SELECT descricao FROM notas_cat_e WHERE id = '$categoria'")->fetchColumn().'</th>
						
						<th style="text-align:center"><small>Sub-Categoria: &nbsp; </small>'.$con->query("SELECT descricao FROM notas_cat_sub WHERE id = '$sub_categoria'")->fetchColumn().'
						</th>
						
					</tr>
				</thead><tbody>';
				echo '
					<tr>
						<td width="30%"><small><b>Nº Motor:</b></small> '.$placa.'</td>
						<td width="30%"><small><b>Marca:</b></small> '.$marca.'</td>
						<td width="30%"><small><b>BP:</b></small> '.$patrimonio.'</td>
					</tr>';
				echo '
					<tr>
						<td><small><b>Chassi: </b></small>'.$chassi.'</td>
						<td><small><b>Valor: </b></small> R$ '.number_format($valor,2,",",".").'</td>
						<td><small><b>Tipo: </b></small>'.$con->query("SELECT descricao FROM notas_eq_situacao WHERE id = '$situacao' ")->fetchColumn().'</td>
					</tr>';
				if($status == '0'){ $status_print = 'ATIVO'; }else{ $status_print = 'INATIVO'; }
				echo '
					<tr>
						<td><small><b>Nº Apolise: </b></small>'.$patrimonio2.'</td>
						<td><small><b>Tipo: </b></small>'.$status_print.'</td>
						<td><small><b>Entrada: </b></small>'.implode("/",array_reverse(explode("-",$entrada))).' - <small><b>Saida: </b></small>'.implode("/",array_reverse(explode("-",$saida))).'</td>
					</tr>';
				
				echo '
					<tr>
						<td width="30%"><small><b>Nota:</b></small> '.$desconto.'</td>
						<td width="30%"><small><b>Fornecedor:</b></small> '.$obs.'</td>
						<td width="30%"><small><b>Seguro:</b></small> '.$justificativa.'</td>
					</tr>';
				echo '
					<tr>
						<td><small><b>Chassi / Nº série: </b></small>'.$chassi.'</td>
						<td><small><b>Ano: </b></small>'.$ano.'</td>
						<td style="text-align:center">';
							if($acesso_usuario == 'MASTER' || $acesso_usuario == 'EQUIPAMENTOS'){
								echo '<a href="#" Onclick=\'$(".modal-body").load("almoxarifado/editar-equipamento-master.php?id='.$b['id'].'")\' data-toggle="modal" data-target="#myModal" class="hidden-print pull-right btn btn-warning btn-xs" style="margin:0px; font-weight:bold;"><span class="glyphicon glyphicon-pencil"></span></a>';
							}
						echo '</td>
					</tr>';
					echo '</tbody> </table> </div>';
				}
			exit;
		}
		if(@$relatorio == '3'){
			if(empty($cat) || empty($sub) || empty($si) || empty ($emp)){ 
				echo '<span class="text-danger">Selecione todos os campos obrigatorios</span>'; 
				exit; 
			}else{
				$catg = ''; $subg = ''; $sit = '';
				foreach($cat as $cats){ $catg .= $cats.','; } $catg = substr($catg,0,-1); 
				foreach($sub as $subs)  { $subg .= $subs.',';  } $subg = substr($subg,0,-1);
				foreach($si as $sis)  { $sit .= $sis.',';   } $sit = substr($sit,0,-1); 
				foreach($emp as $emps)  { $empt .= $emps.',';   } $empt = substr($empt,0,-1);
			}
			echo '<div class="container-fluid hidden-xs visible-print" style="border-bottom:1px solid #CCC; padding-bottom:20px; margin:15px;">
				<div class="col-xs-2" style="padding:0px">
					<img src="../style/img/litoralrent-logo.png" class="img-responsive" width="100px" />
				</div>
				<div class="col-xs-10" style="text-align:right; font-size:8px">
					<b><small>LITORAL RENT LOCADORA E CONSTRUÇÕES LTDA.</small></b><br/>
					Av Antônio Emmerick, 723, Jardim Guassu, São Vicente/SP - CEP 11370-001<br/>
					Telefone: (13) 3043-4211 &nbsp;&nbsp;&nbsp; Email: contato@litoralrent.com.br
					<br/>
				</div>
			</div>
			';
			echo '<center>
				<h4 class="hidden-xs visible-print" style="font-family: \'Oswald\', sans-serif; letter-spacing:4px; text-align:center; margin-bottom:20px;">
					<p><small>RELATORIO EQUIPAMENTOS <br/> LOCADOS</small></p>
				</h4>
			</center>';
			echo '<div class="box box-widget">
					<table id="resultadoConsulta" class="box box-widget table  table-striped table-min small" style="font-size:10px">
					<thead>
						<tr>
							<th style="text-align:center"><i class="fa fa-list-alt" aria-hidden="true"></i></th>
							<th style="text-align:center">Empresa:</th>
							<th style="text-align:center">Patrimônio:</th>
							<th  class="hidden-print" style="text-align:center">Chassi:</th>
							<th style="text-align:center">Sub-Categoria:</th>
							<th class="hidden-print" style="text-align:center">Tipo:</th>
							<th class="hidden-print" style="text-align:center">Valor:</th>
							<th style="text-align:center">Status:</th>
							<th style="text-align:center">Data Locação:</th>';
							if($acesso_usuario == 'MASTER'){
								echo '<th class="hidden-print" style="text-align:center">Editar:</th>';
								echo '<th class="hidden-print" style="text-align:center">Excluir:</th>';
							}
							echo '
						</tr>
					</thead> 
				<tbody>';
				
				$stm = $con->query("SELECT * FROM notas_equipamentos WHERE notas_equipamentos.categoria IN($catg) AND notas_equipamentos.sub_categoria IN($subg) AND notas_equipamentos.situacao IN($sit) AND controle = '1'");
				$c = 0; $total_equipamentos = 0;
				while($b = $stm->fetch()){
					$stm34 = $con->query("SELECT contrato_itens.*, (SELECT empresa FROM contrato_dados WHERE contrato_dados.id = contrato_itens.id_contrato) AS empresa_id, contrato_itens.vlr as vlr_contrato, contrato_itens.data_retirada FROM contrato_itens LEFT JOIN contrato_dados ON contrato_itens.id_contrato = contrato_dados.id WHERE contrato_itens.equipamento = '".$b['id']."' AND contrato_itens.tipo = 0 AND contrato_dados.empresa IN($empt) ORDER BY contrato_itens.data_retirada DESC LIMIT 1");
					$rowxx = $stm34->fetch();
					if($rowxx != ''){
					$c += 1;
					echo '<tr id="thisTr'.$b['id'].'">';
					echo '<td style="text-align:center">'.$c.'</td>';
					echo '<td>'.$con->query("SELECT razao_social FROM localferramentas_cadastroempresa WHERE id = '".$rowxx['empresa_id']."' ")->fetchColumn().'</td>';
					echo '<td> '.$b['patrimonio'].'</td>';
					echo '<td class="hidden-print">'.$b['chassi'].'</td>';
					echo '<td>'.$con->query("SELECT descricao FROM notas_cat_sub WHERE id = '".$b['sub_categoria']."' ")->fetchColumn().'</td>';
					echo '<td class="hidden-print">'.$con->query("SELECT descricao FROM notas_eq_situacao WHERE id = '".$b['situacao']."' ")->fetchColumn().'</td>';
					echo '<td class="hidden-print" style="text-align:center">R$ '.number_format($rowxx['vlr'],2,",",".").'</td>';
					$total_equipamentos += $rowxx['vlr'];
					echo '<td style="text-align:center">';
					if($b['controle'] == '0'){ 
						echo '<span class="label label-success">DISPONIVEL</label>'; 
					}else{ 
						echo '<span class="label label-warning">LOCADO</label>';    
					}
					echo '</td>';
					echo '<td style="text-align:center">'.implode("/",array_reverse(explode("-",$rowxx['data_retirada']))).'</td>';
					if($acesso_usuario == 'MASTER' || $acesso_usuario == 'EQUIPAMENTOS'){
						echo '<td class="hidden-print" style="text-align:center" width="5px"><a href="#" Onclick=\'$(".modal-body").load("almoxarifado/editar-equipamento-master.php?id='.$b['id'].'")\' data-toggle="modal" data-target="#myModal" class="btn btn-success btn-xs" style="margin:0px; font-weight:bold;"><span class="glyphicon glyphicon-pencil"></span></a></td>';
					}
					if($acesso_usuario == 'MASTER'){
						echo '<td class="hidden-print" style="text-align:center" width="5px"><a href="#" Onclick=\'$(".modal-body").load("almoxarifado/del/excluir-equipamento.php?id='.$b['id'].'")\' data-toggle="modal" data-target="#myModal2" class="btn btn-danger btn-xs" style="margin:0px; font-weight:bold;"><span class="glyphicon glyphicon-trash"></span></a></td>';
					}
					
					echo '</tr>';
					}
				}
				echo '</tbody> </table> </div>';
				echo '<h3 class="pull-right">Total R$:'.number_format($total_equipamentos,2,",",".").'</h3>';
			exit;
		}
		if(@$relatorio == '4'){
			if(empty($cat) || empty($sub) || empty($to) || empty($si) || empty($ct)){ 
				echo '<span class="text-danger">Selecione todos os campos obrigatorios</span>'; 
				exit; 
			}else{
				$catg = ''; $subg = ''; $sta = ''; $sit = ''; $ctt = '';
				foreach($cat as $cats){ $catg .= $cats.','; } $catg = substr($catg,0,-1); 
				foreach($sub as $subs)  { $subg .= $subs.',';  } $subg = substr($subg,0,-1);
				foreach($to as $tos)  { $sta .= $tos.',';   } $sta = substr($sta,0,-1);
				foreach($si as $sis)  { $sit .= $sis.',';   } $sit = substr($sit,0,-1); 
				foreach($ct as $cts)  { $ctt .= $cts.',';   } $ctt = substr($ctt,0,-1); 
			}
			echo '<div class="container-fluid hidden-xs visible-print" style="border-bottom:1px solid #CCC; padding-bottom:20px; margin:15px;">
				<div class="col-xs-2" style="padding:0px">
					<img src="../style/img/litoralrent-logo.png" class="img-responsive" width="100px" />
				</div>
				<div class="col-xs-10" style="text-align:right; font-size:8px">
					<b><small>LOCAL FERRRAMENTAS LTDA.</small></b><br/>
					Av Antônio Emmerick, 723, Jardim Guassu, São Vicente/SP - CEP 11370-001<br/>
					Telefone: (13) 3043-4211 &nbsp;&nbsp;&nbsp; Email: contato@litoralrent.com.br
					<br/>
				</div>
			</div>
			';
			echo '<center>
				<h4 class="hidden-xs visible-print" style="font-family: \'Oswald\', sans-serif; letter-spacing:4px; text-align:center; margin-bottom:20px;">
					<p><small>MEMORIA DE CALCULO - EQUIPAMENTOS</small></p>
				</h4>
			</center>';
				echo '<div class="box box-widget">
					<table id="resultadoConsulta" class="box box-widget table  table-striped">
					<thead>
						<tr>
							<th style="text-align:center"><i class="fa fa-list-alt" aria-hidden="true"></i></th>
							<th style="text-align:center">Descrição:</th>
							<th style="text-align:center">Total Qtd</th>
							<th style="text-align:right; padding-right:40px">Valor R$</th>
						</tr>
					</thead> 
				<tbody>';
			$stm2 = $con->query("select *, (SELECT SUM(valor) FROM notas_equipamentos WHERE sub_categoria = notas_cat_sub.id AND status IN($sta) AND situacao IN($sit) AND controle IN($ctt)) as valor_equipamentos, (SELECT COUNT(*) FROM notas_equipamentos WHERE sub_categoria = notas_cat_sub.id AND status IN($sta) AND situacao IN($sit) AND controle IN($ctt)) AS total_equipamentos FROM notas_cat_sub WHERE id IN($subg) and oculto = '0' order by descricao asc");
			$se2 = 0;
			$total_equipamentos_g = 0;
			while($c = $stm2->fetch()){
				if($c['total_equipamentos'] <> 0){
					$total_vlr_equip += $c['valor_equipamentos'];
					$se2 += 1;
					echo '<tr>';
					echo '<td style="text-align:center">'.$se2.'</td>';
					echo '<td>'.$c['descricao'].'</td>';
					echo '<td style="text-align:center">'.$c['total_equipamentos'].'</td>';
					echo '<td style="text-align:right; padding-right:40px">R$ '.number_format($c['valor_equipamentos'],2,",",".").'</td>';
					echo '</tr>';
					$total_equipamentos_g += $c['total_equipamentos'];
				}
			}
			echo '<tfoot>';
			echo '<tr class="active"><td colspan="2"><b>Total</b></td><td style="text-align:center"><b>'.$total_equipamentos_g.'</b></td><td style="text-align:right; padding-right:40px"><b>R$ '.number_format($total_vlr_equip,2,",",".").'</b></td></tr>';
			echo '</tfoot>';
			exit;
		}
		if(@$relatorio == '5'){
			if(empty($cat) || empty($sub) || empty($to) || empty($si) || empty($ct)){ 
				echo '<span class="text-danger">Selecione todos os campos obrigatorios</span>'; 
				exit; 
			}else{
				$catg = ''; $subg = ''; $sta = ''; $sit = ''; $ctt = '';
				foreach($cat as $cats){ $catg .= $cats.','; } $catg = substr($catg,0,-1); 
				foreach($sub as $subs)  { $subg .= $subs.',';  } $subg = substr($subg,0,-1);
				foreach($to as $tos)  { $sta .= $tos.',';   } $sta = substr($sta,0,-1);
				foreach($si as $sis)  { $sit .= $sis.',';   } $sit = substr($sit,0,-1); 
				foreach($ct as $cts)  { $ctt .= $cts.',';   } $ctt = substr($ctt,0,-1); 
			}
			echo '<div class="container-fluid hidden-xs visible-print" style="border-bottom:1px solid #CCC; padding-bottom:20px; margin:15px;">
				<div class="col-xs-2" style="padding:0px">
					<img src="../style/img/litoralrent-logo.png" class="img-responsive" width="100px" />
				</div>
				<div class="col-xs-10" style="text-align:right; font-size:8px">
					<b><small>LOCAL FERRAMENTAS LTDA.</small></b><br/>
					Av Antônio Emmerick, 723, Jardim Guassu, São Vicente/SP - CEP 11370-001<br/>
					Telefone: (13) 3043-4211 &nbsp;&nbsp;&nbsp; Email: contato@litoralrent.com.br
					<br/>
				</div>
			</div>
			';
			echo '<center>
				<h4 class="hidden-xs visible-print" style="font-family: \'Oswald\', sans-serif; letter-spacing:4px; text-align:center; margin-bottom:20px;">
					<p><small>MEMORIA DE CALCULO - EQUIPAMENTOS</small></p>
				</h4>
			</center>';
				echo '<div class="box box-widget">
					<table class="box box-widget table table-condensed table-striped">
					<thead>
						<tr>
							<th style="text-align:center"><i class="fa fa-list-alt" aria-hidden="true"></i></th>
							<th style="text-align:center"  colspan="4">Descrição:</th>
							<th style="text-align:center">Total</th>
						</tr>
					</thead> 
				<tbody>';
			$stm2 = $con->query("select *, (SELECT COUNT(*) FROM notas_equipamentos WHERE sub_categoria = notas_cat_sub.id AND status IN($sta) AND situacao IN($sit) AND controle IN($ctt)) AS total_equipamentos FROM notas_cat_sub WHERE id IN($subg) and oculto = '0' order by descricao asc");
			$se2 = 0;
			$total_equipamentos_g = 0;
			while($c = $stm2->fetch()){
				if($c['total_equipamentos'] <> 0){
					$se2 += 1;
					echo '<tr class="active info">';
					echo '<td><b>'.$se2.'</b></td>';
					echo '<td colspan="4"><b>'.$c['descricao'].'</b></td>';
					echo '<td style="text-align:center"><b>'.$c['total_equipamentos'].'</b></td>';
					echo '</tr>';
					echo '<tr class="active small" style="font-size:10px">';
					echo '<th style="text-align:center">N:</th>
							<th style="text-align:center">BP:</th>
							<th style="text-align:center">Chassi:</th>
							<th style="text-align:center">Tipo:</th>
							<th style="text-align:center">Observações:</th>
							<th style="text-align:center">Situação:</th>';
					echo '</tr>';
					$pdo_equip = $con->prepare("SELECT * FROM notas_equipamentos WHERE sub_categoria = ? AND status IN($sta) AND situacao IN($sit) AND controle IN($ctt) order by patrimonio asc");
					$pdo_equip->execute(array($c['id']));
					$se3 = 0;
					while($e = $pdo_equip->fetch()){
						$se3 += 1;
						echo '<tr class="small">';
						echo '<td>'.$se3.'</td>';
						echo '<td>'.$e['patrimonio'].'</td>';
						echo '<td>'.$e['chassi'].'</td>';
						echo '<td>'.$con->query("SELECT descricao FROM notas_eq_situacao WHERE id = ".$e['situacao']." ")->fetchColumn().'</td>';
						echo '<td>'.$con->query("SELECT historico FROM notas_historico_equipamentos where id_equipamento = ".$e['id']." ORDER BY id DESC LIMIT 1")->fetchColumn().'</td>';
						echo '<td>';
						if($e['controle'] == 0){ 
							echo '<span class="label label-success">DISPONIVEL</label>'; 
						}else{ 
							echo '<span class="label label-warning">LOCADO</label>'; 
						}
						echo '</td>';
					}
					
					$total_equipamentos_g += $c['total_equipamentos'];
				}
			}
			echo '<tr class="active"><td colspan="5"><b>Total</b></td><td style="text-align:center"><b>'.$total_equipamentos_g.'</b></td></tr>';
			exit;
		}
		if(@$relatorio == '6'){
			if(empty($cat) || empty($sub) || empty($to) || empty($si)){ 
				echo '<span class="text-danger">Selecione todos os campos obrigatorios</span>'; 
				exit; 
			}else{
				$catg = ''; $subg = ''; $sta = ''; $sit = '';
				foreach($cat as $cats){ $catg .= $cats.','; } $catg = substr($catg,0,-1); 
				foreach($sub as $subs)  { $subg .= $subs.',';  } $subg = substr($subg,0,-1);
				foreach($to as $tos)  { $sta .= $tos.',';   } $sta = substr($sta,0,-1);
				foreach($si as $sis)  { $sit .= $sis.',';   } $sit = substr($sit,0,-1); 
				foreach($emp as $emps)  { $empt .= $emps.',';   } $empt = substr($empt,0,-1); 
			}
			echo '<div class="container-fluid hidden-xs visible-print" style="border-bottom:1px solid #CCC; padding-bottom:20px; margin:15px;">
				<div class="col-xs-2" style="padding:0px">
					<img src="../style/img/litoralrent-logo.png" class="img-responsive" width="100px" />
				</div>
				<div class="col-xs-10" style="text-align:right; font-size:8px">
					<b><small>LOCAL FERRAMENTAS LTDA.</small></b><br/>
					Av Antônio Emmerick, 723, Jardim Guassu, São Vicente/SP - CEP 11370-001<br/>
					Telefone: (13) 3043-4211 &nbsp;&nbsp;&nbsp; Email: contato@litoralrent.com.br
					<br/>
				</div>
			</div>
			';
			echo '<center>
				<h4 class="hidden-xs visible-print" style="font-family: \'Oswald\', sans-serif; letter-spacing:4px; text-align:center; margin-bottom:20px;">
					<p><small>RELATORIO EQUIPAMENTOS <br/> LOCADOS</small></p>
				</h4>
			</center>';
				$stm = $con->query("SELECT * FROM notas_equipamentos WHERE categoria IN($catg) AND sub_categoria IN($subg) AND status IN($sta) AND situacao IN($sit) AND controle = 1");
				$c = 0;
				while($b = $stm->fetch()){ extract($b);
					$stm35 = $con->query("SELECT contrato_itens.*, (SELECT empresa FROM contrato_dados WHERE contrato_dados.id = contrato_itens.id_contrato) AS empresa_id, contrato_itens.vlr as vlr_contrato, contrato_itens.data_retirada FROM contrato_dados RIGHT JOIN contrato_itens ON contrato_itens.id_contrato = contrato_dados.id WHERE contrato_itens.equipamento = ".$b['id']." AND contrato_dados.empresa IN($empt) AND contrato_itens.tipo = 0 ORDER BY contrato_itens.data_retirada DESC LIMIT 1");
					$rowbb = $stm35->fetch();
					if($rowbb != ''){
					$c += 1;
					echo '<div class="box box-widget">
					<table class="box box-widget table table-striped table-condensed" style="font-size:12px; border:1px solid #ccc;">';
				echo '<thead>
					<tr>
						<th>Nº '.$c.'&nbsp;&nbsp; / &nbsp;&nbsp;'.$con->query("select razao_social from localferramentas_cadastroempresa where id = ".$rowbb['empresa_id']."'")->fetchColumn().'</th>
						
						<th style="text-align:center"><small>Categoria: &nbsp;</small> '.$con->query("SELECT descricao FROM notas_cat_e WHERE id = $categoria")->fetchColumn().'</th>
						
						<th style="text-align:center"><small>Sub-Categoria: &nbsp; </small>'.$con->query("SELECT descricao FROM notas_cat_sub WHERE id = $sub_categoria")->fetchColumn().'
						</th>
						
					</tr>
				</thead><tbody>';
				echo '<tr>
						<td width="30%"><small><b>Nº Motor:</b></small> '.$placa.'</td>
						<td width="30%"><small><b>Marca:</b></small> '.$marca.'</td>
						<td width="30%"><small><b>BP:</b></small> '.$patrimonio.'</td>
					</tr>';
				echo '<tr>
						<td><small><b>Chassi: </b></small>'.$chassi.'</td>
						<td><small><b>Valor: </b></small> R$ '.number_format($valor,2,",",".").'</td>
						<td><small><b>Tipo: </b></small>'.$con->query("SELECT descricao FROM notas_eq_situacao WHERE id = $situacao ")->fetchColumn().'</td>
					</tr>';
				if($status == '0'){ $status_print = 'ATIVO'; }else{ $status_print = 'INATIVO'; }
				echo '<tr>
						<td><small><b>Nº Apolise: </b></small>'.$patrimonio2.'</td>
						<td><small><b>Tipo: </b></small>'.$status_print.'</td>
						<td><small><b>Entrada: </b></small>'.implode("/",array_reverse(explode("-",$entrada))).' - <small><b>Saida: </b></small>'.implode("/",array_reverse(explode("-",$saida))).'</td>
					</tr>';
				
				echo '<tr>
						<td width="30%"><small><b>Nota:</b></small> '.$desconto.'</td>
						<td width="30%"><small><b>Fornecedor:</b></small> '.$obs.'</td>
						<td width="30%"><small><b>Seguro:</b></small> '.$justificativa.'</td>
					</tr>';
				echo '<tr>
						<td><small><b>Chassi / Nº série: </b></small>'.$chassi.'</td>
						<td><small><b>Ano: </b></small>'.$ano.'</td>
						<td style="text-align:center">';
							if($acesso_usuario == 'MASTER' || $acesso_usuario == 'EQUIPAMENTOS'){
								echo '<a href="#" Onclick=\'$(".modal-body").load("almoxarifado/editar-equipamento-master.php?id='.$b['id'].'")\' data-toggle="modal" data-target="#myModal" class="hidden-print pull-right btn btn-warning btn-xs" style="margin:0px; font-weight:bold;"><span class="glyphicon glyphicon-pencil"></span></a>';
							}
						echo '</td>
					</tr>';
					echo '</tbody> </table> </div>';
					}
				}
			exit;
		}
	}
	?>

<section class="content">
	<div class="buttons-top-page hidden-print">
		
		<?php if($acesso_usuario == 'MASTER' || $acesso_usuario == 'EQUIPAMENTOS'){ ?>
		<a href="#" style="padding:3px 15px;" title="Cadastrar Novo" class="btn btn-success btn-sm" onclick='$(".modal-body").load("almoxarifado/cadastro-equipamentos.php")' data-toggle="modal" data-target="#myModal"><i class="fa fa-plus-circle" aria-hidden="true"></i> Cadastrar</a>	
		<?php } ?>
		<a href="#" style="padding:3px 15px; margin:0px 10px;" title="Atualizar Pagina" class="btn btn-warning btn-sm" onclick="ldy('almoxarifado/consulta-equipamentos.php','.conteudo')"><i class="fa fa-refresh" aria-hidden="true"></i> Atualizar</a>
		
		<a href="javascript:window.print()" style="padding:3px 15px; margin:0px 10px;"  class="hidden-xs hidden-print pull-right btn btn-warning btn-sm"> <span class="glyphicon glyphicon-print" aria-hidden="true"></span>&nbsp;Imprimir</a>
	</div>
	<div class="hidden-print" style="clear: both;">
		<hr></hr>
	</div>
	<form action="javascript:void(0)" id="form1" class="hidden-print">
		<div class="well well-sm" style="padding:10px 10px 5px 10px;">
			<div class="container-fluid">
				<div class="col-xs-12 col-md-3" style="padding:2px">
					<label style="width:100%"><small>Empresa:</small>
						<select name="emp[]" class="sel" multiple="multiple" required> 
							<?php 
								$stms = $con->query("select * from localferramentas_cadastroempresa order by razao_social asc");
								while($l = $stms->fetch()){
									echo '<option value="'.$l['id'].'" selected>'.$l['razao_social'].'</option>'; 
								}
							?>		
						</select>
					</label>
				</div>
				<div class="col-xs-12 col-md-2" style="padding:2px">
					<label for="" style="width:100%"><small>Relatorio:</small> <br/>
						<select name="relatorio" class="form-control input-sm" style="width:100%">
							<option value="1">SIMPLES</option>
							<option value="2">DETALHADO</option>
							<option value="3">LOCADOS</option>
							<option value="6">LOCADOS (DETALHADO)</option>
							<option value="4">MEMORIA DE CALCULO</option>
							<option value="5">RELATORIO CATEGORIA</option>
						</select>
					</label>
				</div>
				<div class="col-xs-12 col-md-2" style="padding:2px">
					<label style="width:100%"><small>Categoria:</small>
						<select name="cat[]" onChange="$('#itens_categoria').load('../functions/functions-load.php?atu=categoria&control=2&categoria=' + $(this).val() + '');" class="sel" multiple="multiple" required> 
							<?php 
								$stms = $con->query("select * from notas_cat_e where oculto = '0' order by descricao asc");
								while($l = $stms->fetch()){
									echo '<option value="'.$l['id'].'" selected>'.$l['descricao'].'</option>'; 
								}
							?>		
						</select>
					</label>
				</div>
				<div class="col-xs-12 col-md-2" style="padding:2px;">
					<div id="itens_categoria">
						<label style="width:100%"><small>Sub-Categoria:</small><br/>
							<select name="sub[]"  class="sel" multiple="multiple" required>
								<?php 
								$stms = $con->query("select * from notas_cat_sub order by descricao asc");
								while($l = $stms->fetch()){
									echo '<option value="'.$l['id'].'" selected>'.$l['descricao'].'</option>'; 
								}
								?>		
							</select>
						</label>
					</div>
				</div>
				<div class="col-xs-12 col-md-2" style="padding:2px;">
					<label style="width:100%"><small>Tipo: </small>
						<select name="si[]" class="sel" multiple="multiple">
							<?php
								$stms = $con->query("select * from notas_eq_situacao where status = '0' order by descricao asc");
								while($l = $stms->fetch()){
									echo '<option value="'.$l['id'].'" selected>'.$l['descricao'].'</option>';
								}
							?>
						</select>
					</label>
				</div>
				<div class="col-xs-12 col-md-2" style="padding:2px;">
						<label style="width:100%"><small>Situação: </small>
							<select name="to[]" class="sel" multiple="multiple">
								<option value="0" selected>ATIVO</option>
								<option value="1" selected>INATIVO</option>
								<option value="2" selected>ROUBADO</option>
							</select>
						</label>
				</div>
				<div class="col-xs-12 col-md-2" style="padding:2px;">
						<label style="width:100%"><small>Status: </small>
							<select name="ct[]" class="sel" multiple="multiple">
								<option value="0" selected>DISPONIVEL</option>
								<option value="1" selected>LOCADO</option>
							</select>
						</label>
				</div>
				
				<div class="col-xs-12 col-md-2" style="padding:2px;">
					<label><br/>
						<input type="submit" value="Pesquisar" style="width:150px; margin-left:10px;" onClick="post('#form1','almoxarifado/consulta-equipamentos.php?ac=consulta','.retorno')" class="btn btn-success btn-sm">
					</label>
				</div>
			</div>
		</div>
	</form>
	<div class="retorno"></div>
</section>
	<div class="modal" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog"  style="width:90%;">
			<div class="modal-content"> 
				<div class="modal-header box box-info" style="margin:0px;">
					<button type="button" class="close" style="opacity:1" onclick="$('.modal').modal('hide'); $('.modal-body').empty()" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="myModalLabel">Painel Administrativo</h4>
				</div>
				<div class="modal-body">
					Aguarde um momento &nbsp;&nbsp; <img src="../style/img/loading.gif" alt="Carregando" width="20px"/>
				</div>
			</div>
		</div>
	</div>
	<div class="modal" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog"  style="width:90%;">
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