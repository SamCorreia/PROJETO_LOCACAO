<div class="sidebar-menu">
    <ul class="menu">
        <?php foreach($modulos_permitidos as $dir => $nome): ?>
            <li class="menu-item">
                <a href="javascript:void(0)" onclick="carregarModulo('<?= $dir ?>')">
                    <i class="fas fa-folder"></i> <?= $nome ?>
                </a>
                <ul class="submenu" id="submenu-<?= $dir ?>">
                    <!-- Subitens serão carregados via AJAX -->
                </ul>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

<script>
function carregarModulo(modulo) {
    fetch(`includes/load-submenu.php?modulo=${modulo}`)
        .then(response => response.text())
        .then(html => {
            document.getElementById(`submenu-${modulo}`).innerHTML = html;
        });
}
</script>