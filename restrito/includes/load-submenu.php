<?php
require_once('../../config.php');
$modulo = $_GET['modulo'] ?? '';

$submenus = [
    'financeiro' => [
        ['link' => 'financeiro/cadastro-contrato.php', 'nome' => 'Cadastrar Contrato'],
        ['link' => 'financeiro/consulta-contrato.php', 'nome' => 'Consultar Contratos']
    ],
    'gestor' => [
        ['link' => 'gestor/usuarios.php', 'nome' => 'Gerenciar Usuários']
    ],
    // Adicione os outros módulos...
];

if (array_key_exists($modulo, $submenus)) {
    foreach ($submenus[$modulo] as $item) {
        echo '<li><a href="javascript:ldy(\''.$item['link'].'\', \'.conteudo\')">'.$item['nome'].'</a></li>';
    }
}
?>