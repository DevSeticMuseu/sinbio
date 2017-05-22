$(document).ready(function () {
    $('.tabela tbody').on('dblclick', 'tr', function () {
        var row = table.row(this).data();
        detalharAmostra(row[0]);
    });
});

function detalharAmostra(idAmostra){
    $(window.document.location).attr('href',"/amostra-detalhada/index/nId/" + idAmostra);
}

function novoModulo() {
    $(window.document.location).attr('href',"/amostra/cadastrar");
}

function alterarModulo() {
    if (table.$('tr.selected').length > 1) {
        jAlert("Você deve selecionar apenas um item para alterar.","Atenção");
    }
    else if (table.$('tr.selected').length == 0) {
        jAlert("Você deve selecionar um item para alterar.","Atenção");
    }
    else {
        var nId = table.row(table.$('tr.selected')).data()[0];
        $(window.document.location).attr('href',"/amostra/alterar/nId/"+nId);
    }
}

function excluirModulo() {
    if (table.$('tr.selected').length == 0) {
        jAlert("Você deve selecionar ao menos um ítem.","Atenção");
    }
    else {
        if (confirm('Você tem certeza que deseja excluir ' + table.$('tr.selected').length + ' Amostra(s)?')) {
            var selArry = [];
            var nId;

            table.$('tr.selected').each(function () {
                nId = table.row(this).data()[0];
                selArry.push(nId);
            });

            $.post('/amostra/excluir', {
                fId: selArry, 
                sOP: "Excluir"
            }, function () {
                location.reload();
            });
        }
    }
}

//function setParticipantes(){
//    if (data) {
//        if ($('.trSelected').length > 1) {
//            jAlert("Você deve selecionar apenas um item para alterar.","Atenção");
//        }
//        else if ($('.trSelected').length == 0) {
//            jAlert("Você deve selecionar um item para alterar.","Atenção");
//        }
//        else {
//            var nId = $('.trSelected td:first-child div').text();
//            $(window.document.location).attr('href',"/participantes-amostra/index/nId/"+nId);
//        }
//    }
//}