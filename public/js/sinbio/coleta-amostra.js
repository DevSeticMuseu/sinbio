$(document).ready(function () {
    $('.tabela tbody').on('dblclick', 'tr', function () {
        var row = table.row(this).data();
        detalharAmostra(row[0]);
    });
});

function detalharAmostra(idAmostra){
    $.post('/amostra-detalhada/verifica-permissao', {
        sOP: "Cadastrar"
    }, function(data) {
        if (data) {
            $(window.document.location).attr('href',"/amostra-detalhada/index/nId/" + idAmostra);
        }
        else {
            jAlert("Voce não possui permissão de acessar essa área.","Acesso Negado");
        }
    });
}

function setParticipantes(){
    $.post('/participantes-amostra/verifica-permissao', {
        sOP: "Cadastrar"
    }, function(data) {
        if (data) {
            if ($('.trSelected').length > 1) {
                jAlert("Você deve selecionar apenas um item para alterar.","Atenção");
            }
            else if ($('.trSelected').length == 0) {
                jAlert("Você deve selecionar um item para alterar.","Atenção");
            }
            else {
                var nId = $('.trSelected td:first-child div').text();
                $(window.document.location).attr('href',"/participantes-amostra/index/nId/"+nId);
            }
        }
        else {
            jAlert("Voce não possui permissão de acessar essa área.","Acesso Negado");
        }
    });
}

function novoModulo() {
    $.post('/amostra/verifica-permissao', {
        sOP: "Cadastrar"
    } ,function(data) {
        if (data) {
            $(window.document.location).attr('href',"/amostra/cadastrar");
        }
        else {
            jAlert("Voce não possui permissão de acessar essa área.","Acesso Negado");
        }
    });
}

function alterarModulo() {
    $.post('/amostra/verifica-permissao', {
        sOP: "Alterar"
    }, function(data) {
        if (data) {
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
        else {
            jAlert("Voce não possui permissão de acessar essa área.","Acesso Negado");
        }
    });
}

function excluirModulo() {
    if (table.$('tr.selected').length == 0) {
        jAlert("Você deve selecionar ao menos um ítem.","Atenção");
    }
    else {
        var info = [];
        var columnInfo;
        table.$('tr.selected').each(function () {
            columnInfo = table.row(this).data()[2];
            info.push(columnInfo);
        });
        if (confirm('Você tem certeza que deseja excluir ' + table.$('tr.selected').length + ' Expedição(ões)?')) {
            $.post('/amostra/verifica-permissao', {
                sOP: "Excluir"
            } , function(data) {
                if (data) {
                    var selArry = [];
                    var nId;

                    table.$('tr.selected').each(function () {
                        nId = table.row(this).data()[0];
                        selArry.push(nId);
                    });

                    $.post('/amostra/excluir', {
                        fId: selArry, 
                        sOP: "Excluir"
                    }, function (data) {
                        location.reload();
                    });
                }
                else {
                    jAlert("Voce não possui permissão de acessar essa área.","Acesso Negado");
                }
            });
        }
    }
}

