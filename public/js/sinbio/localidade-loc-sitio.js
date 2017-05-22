function novoModulo() {
    $(window.document.location).attr('href', "/sitio/cadastrar");
}

function alterarModulo() {
    if (table.$('tr.selected').length > 1) {
        jAlert("Você deve selecionar apenas um item para alterar.", "Atenção");
    }
    else if (table.$('tr.selected').length == 0) {
        jAlert("Você deve selecionar um item para alterar.", "Atenção");
    }
    else {
        var nId = table.row(table.$('tr.selected')).data()[0];
        $(window.document.location).attr('href', "/sitio/alterar/nId/" + nId);
    }
}

function excluirModulo() {
    if ($('tr.selected').length == 0) {
        jAlert("Você deve selecionar ao menos um ítem.", "Atenção");
    }
    else {
        if (confirm('Você tem certeza que deseja excluir ' + table.$('tr.selected').length + ' Sítio(s)?')) {
            var selArry = [];
            var nId;

            table.$('tr.selected').each(function () {
                nId = table.row(this).data()[0];
                selArry.push(nId);
            });

            $.post('/sitio/excluir', {
                fId: selArry, 
                sOP: "Excluir"
            }, function () {
                location.reload();
            });
        }
    }
}