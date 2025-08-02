// Função ldy para carregamento AJAX
function ldy(url, target) {
    $('#loading').show();
    $(target).load(url, function() {
        $('#loading').hide();
    });
}

// Funções clickFunction (todas as que seu sistema usa)
function clickFunction1() {
    $(".list-block").not(".list-finac").slideUp();
    $(".list-finac").slideToggle('slow');
}

function clickFunction2() {
    $(".list-block").not(".list-comp").slideUp();
    $(".list-comp").slideToggle('slow');
}

function clickFunction3() {
    $(".list-block").not(".list-consulta").slideUp();
    $(".list-consulta").slideToggle('slow');
}

function clickFunction4() {
    $(".list-block").not(".list-gestor").slideUp();
    $(".list-gestor").slideToggle('slow');
}

// Função ldy para carregamento AJAX
function ldy(url, target) {
    $('#loading').show();
    $(target).load(url, function() {
        $('#loading').hide();
    });
}

// Desativa comportamentos de extensões que podem interferir
if (navigator.serviceWorker) {
    navigator.serviceWorker.getRegistrations().then(registrations => {
        registrations.forEach(registration => registration.unregister());
    });
}