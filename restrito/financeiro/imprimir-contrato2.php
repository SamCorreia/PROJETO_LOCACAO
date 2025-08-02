<?php
	require_once('../../config.php');
	require_once('../../functions.php');
	$con = new DBConnection();
	verificaLogin(); 
	getData();
	setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
	date_default_timezone_set('America/Sao_Paulo');
	$stm = $con->query("SELECT *, (select razao_social from localferramentas_cadastroempresa where id = contrato_dados.empresa) as empresa_nome, (select cnpj from localferramentas_cadastroempresa where id = contrato_dados.empresa) as cnpj, (select endereco from localferramentas_cadastroempresa where id = contrato_dados.empresa) as empresa_endereco FROM contrato_dados WHERE id = '$id_contrato'");
	while($b = $stm->fetch()){ extract ($b); }
	$ano_contrato = explode("-",$data_contrato);
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8"/>
	<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
	<title> Litoral Rent</title>
	<link rel="icon" href="../../style/img/logo.ico" type="image/x-icon"/>
	<link rel="shortcut icon" href="../../style/img/imagens/logo.ico" type="image/x-icon"/>
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport"/>
	<link rel="stylesheet" href="../../style/css/bootstrap.min.css"/>
	<link href='https://fonts.googleapis.com/css?family=Roboto+Condensed' rel='stylesheet' type='text/css'/>
	<link href='https://fonts.googleapis.com/css?family=Oswald:300' rel='stylesheet' type='text/css'/>
	<style>
		* {
			font-size: 12px;
		}
		table {
			width: 100%;
			margin: 0 0 10px 0;
		}
		table tr td, th {
			border-collapse:  collapse;
			border: 1px solid #000 !important;
			padding: 5px;
			font-size: 11px;
		}
		@page {
			size: A4;
		}
	</style>
</head>
<script>
window.onload = function() { 
	window.print(); 
}
</script>
<body>
<div class="container-fluid" style="border-bottom:1px solid #CCC; padding-bottom:20px; margin:15px;">
	<div class="col-xs-2" style="padding:0px">
		<img src="../../style/img/litoralrent-logo.png" class="img-responsive" width="100px" />
	</div>
	<div class="col-xs-10" style="text-align:right; font-size:8px">
		<b><small>LITORAL RENT LOCADORA E CONSTRUÇÕES LTDA.</small></b><br/>
		Av Antônio Emmerick, 723, Jardim Guassu, São Vicente/SP - CEP 11370-001<br/>
		Telefone: (13) 3043-4211 &nbsp;&nbsp;&nbsp; Email: contato@litoralrent.com.br
		<br/>
	</div>
</div>
<div class="container-fluid">
<h5 style="text-align:center"><b>CONTRATO <?= $ano_contrato[0] ?> - Nº <?= $id_contrato ?> </b></h5>
<h5 style="text-align:center"><b>ADENDO - LOCAÇÃO DE EQUIPAMENTOS</b></h5>
<br/>
<h5>CLÁUSULA 01 - DAS PARTES</h5>
<p>	
1.1 – <b>LITORAL RENT LOCADORA E CONSTRUÇÕES LTDA</b>, empresa privada, inscrita no CNPJ sob o n° <b>24.094.902/0001-00</b>, com sede à Avenida Antônio Emmerick, n°723 CEP: 11370-001 na Cidade de São Vicente, Estado de São Paulo, adiante denominada <b>LOCADORA</b>.
</p>
<p>
1.2 – <b><?= $empresa_nome ?></b> inscrito no CPF/CNPJ sob o n° <b><?= $cnpj ?></b>  com sede à <b><?= $empresa_endereco ?></b>,  adiante denominado <b>LOCATARIA</b>.
</p>
<h5>Pelo presente instrumento:</h5>
<h5>CONFORME CLÁUSULA 03 – ADENDOS CONTRATUAIS</h5>
<p>
Os equipamentos constantes no objeto deste contrato serão descriminados em adendos, conforme solicitações feitas e confirmadas por e-mail informando local e responsável no recebimento em decorrer do período de contrato. 
</p>
<h5 align="center">
SEGUE RELAÇÃO DE EQUIPAMENTOS SOLICITADOS:
</h5>

<table class="table table-condensed table-min" style="font-size:10px">
	<thead>
		<tr>
			<th>Item:</th>
			<th>Patrimônio:</th>
			<th>Motor:</th>
			<th>Chassi:</th>
			<th>Sub-categoria:</th>
			<th>Tipo:</th>
			<th>Valor:</th>
		</tr>	
	</thead>
	<tbody>
		<?php
			$stm = $con->prepare("SELECT contrato_itens.*, notas_equipamentos.patrimonio, notas_equipamentos.sub_categoria, notas_equipamentos.placa, notas_equipamentos.chassi, notas_equipamentos.id as id_equip, (SELECT tipo FROM contrato_adendo WHERE id = contrato_itens.adendo) as tipo_adendo FROM contrato_itens left JOIN notas_equipamentos ON contrato_itens.equipamento = notas_equipamentos.id WHERE contrato_itens.id_contrato = ? AND contrato_itens.adendo = ? ORDER BY id desc");
			$stm->execute(array($id_contrato, $id_adendo));
			$se = 0; $total_vlr = 0;
			while($s = $stm->fetch())
			{ 
				$se += 1;
				echo '<tr id="thisTr'.$s['id'].'">';
				echo '<td>'.$se.'</td>';
				echo '<td>'.$s['patrimonio'].'</td>';
				echo '<td>'.$s['placa'].'</td>';
				echo '<td>'.$s['chassi'].'</td>';
				echo '<td>'.$con->query("SELECT descricao FROM notas_cat_sub WHERE id = '".$s['sub_categoria']."' ")->fetchColumn().'</td>';
				if($s['tipo'] == '0'){
					echo '<td  width="5%" align="center" style="font-size:12px; font-weight:bold">LOCAÇÃO</td>';
				}else if($s['tipo'] == '1'){
					echo '<td  width="5%" align="center" style="font-size:12px; font-weight:bold">DEVOLUÇÃO</td>';
				}
				echo '<td align="center">R$ '.number_format($s['vlr'],2,",",".").'</td>';
				$total_vlr += $s['vlr'];
				
				echo '</tr>';
			}
		?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="6" align="right">Valor Total</td>
			<td align="center">R$ <?=number_format($total_vlr,2,",",".")?></td>
		</tr>
	</tfoot>
</table>
<br/>
<p>
<?php
$obra_adendo = $con->query("SELECT obra FROM contrato_adendo WHERE id = '$id_adendo'")->fetchColumn();
$data_adendo = $con->query("SELECT data_adendo FROM contrato_adendo WHERE id = '$id_adendo'")->fetchColumn();
?>
<?= $con->query("SELECT nome FROM empresa_obras WHERE id = '$obra_adendo'")->fetchColumn(); ?> - <?= $con->query("SELECT endereco FROM empresa_obras WHERE id = '$obra_adendo'")->fetchColumn(); ?>
<br/>
São Vicente, <?= mb_convert_encoding(strftime( '%d de %B de %Y', strtotime($data_adendo)), "UTF-8"); ?>
</p>
<br/><br/>
Atenciosamente,
<p>
<!--<img src="../../style/img/assinatura-valdinei-1.png" alt="Assinatura" />
<br/>-->
<img style="position:relative; top:40px" src="../../style/img/assinatura-valdinei.png" alt="Assinatura" width="250px"/>
<br/>
_____________________________________________________________________<br/>
Nome: Benedito Valdinei da Silva<br/>
Sócio - Administrador<br/>
CPF: 194.409.218-89<br/>
<!--<br/>
<br/>
<br/>
____________________________________________________________________<br/>
Nome: AMANDA CRISTINA DE O. BARRETO<br/>
Nº do RG: 34.509.039-1 SSP/SP<br/>-->
<br/>
<br/>
<br/>
_____________________________________________________________________<br/>
Recebido Por:<br/>
N° do RG:<br/>
</p>
</div>
</body>
</html>

