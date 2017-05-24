function novoModulo() {
    $(window.document.location).attr('href', "/colecao/cadastrar");
}

function alterarModulo() {
    if ($('tr.selected').length > 1) {
        jAlert("Você deve selecionar apenas um item para alterar.", "Atenção");
    }
    else if ($('tr.selected').length == 0) {
        jAlert("Você deve selecionar um item para alterar.", "Atenção");
    }
    else {
        var nId = table.row(table.$('tr.selected')).data()[0];
        $(window.document.location).attr('href', "/colecao/alterar/nId/" + nId);
    }
}

function excluirModulo() {
    if ($('tr.selected').length == 0) {
        jAlert("Você deve selecionar ao menos um ítem.", "Atenção");
    }
    else {

        var info = [];
        var columnInfo;
        table.$('tr.selected').each(function () {
            columnInfo = table.row(this).data()[2];
            info.push(columnInfo);
        });

        if (confirm('Você tem certeza que deseja excluir ' + info + ' ?')) {
            var selArry = [];
            var nId;

            table.$('tr.selected').each(function () {
                nId = table.row(this).data()[0];
                selArry.push(nId);
            });

            $.post('/colecao/excluir', {fId: selArry, sOP: "Excluir"}, function (data) {
                location.reload();
            });
        }
    }
}

