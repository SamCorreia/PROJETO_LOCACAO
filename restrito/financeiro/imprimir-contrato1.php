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
			font-size: 13px;
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
<h5 style="text-align:center"><b>CONTRATO DE LOCAÇÃO DE EQUIPAMENTOS</b></h5>
<br/>
<h5>CLÁUSULA 01 - DAS PARTES</h5>
<p>	
1.1 – <b>LITORAL RENT LOCADORA E CONSTRUÇÕES LTDA</b>, empresa privada, inscrita no CNPJ sob o n° <b>24.094.902/0001-00</b>, com sede à Avenida Antônio Emmerick, n°723 CEP: 11370-001 na Cidade de São Vicente, Estado de São Paulo, adiante denominada <b>LOCADORA</b>.
</p>
<p>
1.2 – <b><?= $empresa_nome ?></b> inscrito no CPF/CNPJ sob o n° <b><?= $cnpj ?></b>  com sede à <b><?= $empresa_endereco ?></b>,  adiante denominado <b>LOCATARIA</b>.
</p>
<h5>Pelo presente instrumento:</h5>
<h5>CLÁUSULA 02 - OBJETIVO</h5>
<p>Objeto deste contrato refere-se à locação de equipamentos, com utilização em obra, mediante as condições contidas nas cláusulas a seguir:</p> 

<h5>CLÁUSULA 03 – ADENDOS CONTRATUAIS</h5>
<p>Os equipamentos constantes no objeto deste contrato serão descriminados em adendos, conforme solicitações feitas e confirmadas por e-mail informando local e responsável no recebimento em decorrer do período de contrato.</p> 

<h5>CLÁUSULA 04 - PRAZO DE CONTRATO</h5>
<p>O período deste contrato é equivalente a (<?= $prazo_contrato ?>) meses, contados a partir da data de assinatura do mesmo, renovando-se automaticamente por prazo indeterminado.</p>

<p>4.1 - Entende-se como datas de Entrega dos equipamentos, o dia em que o mesmo for entregue e aprovado pelo locadora e locatária conforme os anexos. A mesma regra será utilizada para as devoluções dos equipamentos.</p>

<h5>CLÁUSULA 05 – MEDIÇÃO</h5> 
<p>Fica ajustado que o primeiro aluguel será faturado proporcionalmente a partir do dia da entrega até o último dia do mês correspondente e os demais aluguéis serão sempre devidos do primeiro ao último dia de cada mês subsequente.</p> 

<p>5.1 - O prazo de pagamento, deverão ser realizados impreterivelmente com prazo de (<?= $prazo_pagamento ?>) dias após fechamento de medição, através de boleto. </p>

<p>5.2 - Eventuais atrasos de faturamento por parte da Locadora e consequentes postergações das respectivas datas de vencimento não serão jamais entendidos, em hipótese alguma, como renovação contratual e/ou alteração de regra de faturamento acima estabelecida.</p>

<p>5.3 - A Locatária obriga-se pelos pagamentos do aluguel estipulado neste contrato até o final do prazo ajustado, na forma do parágrafo único do artigo n° 1.193 do Código Civil Brasileiro.</p> 

<h5> CLÁUSULA 06 – REAJUSTE </h5>
<p> O valor de locação estipulado neste contrato será reajustado com base na variação do IGP-M (índice Geral de Preços de Mercado), variação está a ser aplicada sempre na menor periodicidade admitida em lei, em qualquer época de vigência deste contrato. Na hipótese de suspensão, extinção ou vedação do uso do IGP-M como índice de atualização de preços, fica desde já eleito o índice que oficialmente vier a substitui-lo. </p>

<h5>CLÁUSULA 07 – EQUIPAMENTOS</h5>
<p>A Locadora, pelo presente, se obriga a manter o equipamento em perfeitas condições de funcionamento, sem quaisquer anus para a Locatária, até o final do presente contrato de locação, prorrogado ou não. </p>

<p>7.1 - No preço mensal da locação, durante a vigência do contrato, encontra-se incluído, exclusivamente para uso no equipamento descrito na cláusula 1, o fornecimento de certos materiais de consumo, até a quantidade máxima definida para cada material relacionado e que passa a fazer parte deste instrumento. Excetua-se, desde logo, de tal fornecimento, qualquer outro material de consumo que lá não esteja relacionado. </p>

<p>7.2 - Fica desde já estabelecido que, caso seja necessário o fornecimento de quaisquer materiais de consumo objeto deste instrumento em número maior que o definido na lista anexa, a diferença de unidades a maior será cobrada da Locatária, com base nos preços à época vigentes na tabela da Locadora, para pagamento contra a entrega da respectiva nota fiscal.</p>
 
<p>7.3 - A Locatária fica ciente, ainda, de que eventuais danos causados em componentes fornecidos por conta deste instrumento, por culpa dela, Locatária, e que resultem em troca do componente, o fornecido em substituição, neste caso, também, será dela cobrado. </p>

<p>7.4 - A Locadora está ciente de que a Locatária tem pleno direito de adquirir componentes e materiais de outras fontes, ficando claro, contudo, que esta aquisição á exclusivamente por conta e responsabilidade da Locatária, sem o direito de reembolso de valores, não podendo estes valores serem adicionados ao valor da locação. </p>

<p>7.5 - Tendo em vista que o equipamento é de propriedade da Locadora, a qual está obrigada pela manutenção técnica, fica ciente desde logo, a Locatária de que, caso diagnósticos sucessivos do técnico da Locadora identifiquem os componentes ou materiais, fornecidos por terceiros, como causa de eventuais falhas no sistema e/ou danos no próprio equipamento, notificada a Locatária, por escrito, sobre o fato, a Locadora passará a ter direito de cobrar os custos adicionais decorrentes dos atendimentos técnicos posteriores. </p>

<p>7.7 - Em caso de roubo (mão armada) a Locatária deverá apresentar um Boletim de Ocorrência com os equipamentos e series devidamente descritos. Sendo assim pagará para Locadora uma franquia, referente a 40% dos valores dos equipamentos, conforme o valor de mercado atualizado. E também os valores referentes a locação mensal dos respectivos equipamentos, até que os mesmos sejam restituídos pela seguradora, no prazo máximo de 60 dias. </p>

<p>7.8 - A Locadora oferece plena garantia do perfeito funcionamento do equipamento, quando da respectiva instalação, obedecidas as especificações técnicas, podendo o equipamento, objeto do presente contrato, ser previamente revisado, dentro dos mais rigorosos padrões técnicos e de controle de qualidade. </p>

<p>7.9 - A Locadora entregará o equipamento no local indicado pela Locatária, em perfeitas condições de servir ao uso a que se destina, do que receberá um comprovante da Locatária. </p>

<p>7.10 - À de responsabilidade da Locadora, por si ou por terceiros por ela credenciados, em ambas as hipóteses sem qualquer ônus para a Locatária, os serviços técnicos e manutenção e reparo do equipamento, substituindo, também por sua conta, todas as peças que se fizerem necessárias em decorrência do uso normal. Esses sérvios serão prestados exclusivamente no Território Nacional e durante o horário normal de expediente comercial da Locadora. Se necessário que estes serviços sejam prestados fora desse horário normal, a pedido da Locatária, as despesas de atendimento extraordinários serão cobradas. Nas localidades de difícil acesso, onde não haja condições de atendimento `in loco` pela Locadora ou por terceiros credenciados, a assistência será prestada em local previamente acordado entre as partes, correndo os gastos referentes ao transporte do equipamento por conta da Locatária. </p>

<p>7.11 - A Locadora aplicará no equipamento, quando necessária a substituição de partes e peças originais, adequadas, novas ou, quando não, que mantenham as especificações técnicas do fabricante, para o que fica, desde logo, autorizada pela Locatária. </p>

<h5> CLÁUSULA 08 – RESPONSABILIDADE DA LOCATÁRIA </h5>

<p>a) usar o equipamento corretamente e não sublocar, ceder nem transferir a locação, total ou parcial;</p>
<p>b) não introduzir modificações de qualquer natureza no equipamento; </p>
<p>c) defender e fazer valer todos os direitos de propriedade e de posse da Locadora sobre o equipamento, inclusive impedindo sua penhora, sequestro, arresto, arrecadação, etc., por terceiros, notificando-os sobre os direitos de propriedade e de posse da Locadora sobre o equipamento; </p>
<p>d) comunicar imediatamente à Locadora qualquer intervenção ou violação por terceiros de qualquer dos seus direitos em relação ao equipamento; </p>
<p>e) permitir o acesso de pessoal autorizado da Locadora para realização da manutenção ou reparos do equipamento e, ainda, para o seu desligamento ou remoção, nas hipóteses cabíveis; </p>
<p>f) responsabilizar-se por qualquer dano, prejuízo ou inutilização do equipamento, ressalvadas as hipóteses de casos roubos ou de força maior, bem como pelo descumprimento de qualquer de suas obrigações previstas neste contrato ou em lei; </p>
<p>g) não permitir que terceiros não autorizados ou credenciados pela Locadora intervenham nas partes e nos componentes internos do equipamento. </p>

<h5>CLÁUSULA 09 – PAGAMENTOS</h5>
<p>A Locatária obriga-se a pagar pontualmente os aluguéis e as faturas de fornecimento de materiais de consumo, em banco indicado pela Locadora e do qual será a Locatária devidamente avisada, ou em outros locais, ou ainda a cobradores da Locadora, quando esta assim o admitir por prévio aviso á Locatária. As faturas não pagas até o vencimento serão acrescidas da variação do IGP-M, aplicada pelos dias de atraso, cominada, também, multa de dois por cento (2%) e juros de mora de um por cento (1%) ao mês ou fração, sem prejuízo das demais sanções aplicáveis, dentre as quais o desligamento temporário do equipamento, a suspensão da Assistência Técnica ou a rescisão deste contrato.</p> 

<p>9.1 - Sem prejuízo dos acréscimos moratórios estabelecidos no item acima, à Locatária, se não cumprir as obrigações deste contrato, será cominada a multa equivalente a trás (3) vezes o valor do aluguel mínimo mensal vigente à época, mais custas, despesas e honorários advocatícios, em caso de cobrança judicial, ficando ainda a Locadora com o direito de considerar rescindido o presente contrato. </p>

<p>9.2 - A recusa da devolução do equipamento ou o dano nele produzido obriga a Locatária, ainda ao ressarcimento pelos danos e lucros cessantes, estes pelo período em que o equipamento deixar de ser utilizado pela Locadora. </p>

<h5>CLÁUSULA 10 – CONSIDERAÇÕES FINAIS </h5>
<p>As partes ajustam que, na infração de qualquer das cláusulas contratuais por parte da Locatária, a Locadora poderá, além de rescindir este contrato, como previsto acima, exigir e obter imediata devolução do equipamento, cabendo-lhe inclusive, na via judicial, a reintegração `initio litis`, válido para os fins do inciso II e III do artigo 927 do Código de Processo Civil, o documento enviado pela Locadora solicitando a devolução do equipamento. </p>

<p>10.1 - Poderá ainda a Locadora, facultativamente, considerar rescindida a locação e retirar o equipamento locado nas hipóteses de falência ou insolvência da Locatária. </p>

<p>10.2 - A infração, por qualquer das partes, das obrigações assumidas no presente contrato dará à outra o direito de rescindi-lo, independentemente de intimação judicial ou extrajudicial, bastando, para isso, aviso por escrito, com prazo de trinta (30) dias contados da inadimplência.</p> 

<p>Fica eleito o Foro da cidade de São Vicente, para dirimir quaisquer questões oriundas deste contrato, renunciando as partes a qualquer outro, por mais privilegiado que seja. </p>

<p>E por estarem de pleno e comum acordo com todas as cláusulas, firmam o presente instrumento, por si e eventuais sucessores, em duas (2) vias de igual teor, para um só efeito, com vigência a partir da data de sua assinatura, na presença de duas testemunhas que a tudo assistiram. </p>


<br/>
<p>
São Vicente, <?= mb_convert_encoding(strftime( '%d de %B de %Y', strtotime($data_contrato)), "UTF-8"); ?>
</p>
<br/><br/>
<div class="container-fluid" style="font-size:8px">
	<div class="col-xs-6">
		______________________________________<br/>
		LITORAL RENT LOC. E CONST. LTDA<br/>
		CNPJ: 24.094.902/0001-00<br/>
		REP. - <?= $con->query("SELECT nome FROM usuarios WHERE id = '".$id_usuario_logado."' ")->fetchColumn(); ?><br/>
		RG: <?= $con->query("SELECT rg FROM usuarios WHERE id = '".$id_usuario_logado."' ")->fetchColumn(); ?><br/>
	</div>
	<div class="col-xs-6">
		________________________________________<br/>
		<?= $empresa_nome ?><br/>
		CNPJ/CPF: <?= $cnpj ?><br/>
		REP: ______________________________<br/>
		RG:&nbsp;&nbsp;&nbsp;______________________________<br/>
	</div>
</div>
<br/>
<p>Testemunhas:</p>
<br/>
<div class="container-fluid" style="font-size:8px">
	<div class="col-xs-6">
		______________________________________<br/>
		Nome:<br/>
		Rg: <br/>
	</div>
	<div class="col-xs-6">
		______________________________________<br/>
		Nome:<br/>
		Rg: <br/>
	</div>
</div>
</p>
</div>
</body>
</html>

