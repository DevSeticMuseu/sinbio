$().ready(function() {
    $(".grid").flexigrid({
        url : '/expedicao/gera-xml',
        dataType : 'xml',
        colModel : [ {
            display : 'Id',
            name : 'id',
            width : 40,
            sortable : true,
            align : 'center'
        },
        {
            display : 'Protocolo',
            name : 'nm_protocolo',
            width : 250,
            sortable : true,
            align : 'left'
        },
        {
            display : 'Estado',
            name : 'nm_uf',
            width : 100,
            sortable : true,
            align : 'left'
        },
        {
            display : 'Município',
            name : 'nm_municipio',
            width : 100,
            sortable : true,
            align : 'left'
        },
        {
            display : 'Localidade',
            name : 'nm_sitio',
            width : 150,
            sortable : true,
            align : 'left'
        },
        {
            display : 'Nome Projeto/Programa',
            name : 'nm_projeto_programa',
            width : 200,
            sortable : true,
            align : 'left'
        },
        {
            display : 'Projeção',
            name : 'sistema_projecao',
            width : 100,
            sortable : true,
            align : 'left'
        },
        
        {
            display : 'Data Início',
            name : 'data_inicio',
            width : 80,
            sortable : true,
            align : 'left'
        },
        {
            display : 'Data Fim',
            name : 'data_fim',
            width : 80,
            sortable : true,
            align : 'left'
        }],
        buttons : [ {
            name : 'Novo',
            bclass : 'add',
            onpress : novoPrograma
        }, {
            name : 'Alterar',
            bclass : 'edit',
            onpress : alterarPrograma
        }, {
            name : 'Excluir',
            bclass : 'delete',
            onpress : excluirPrograma
        }, {
            separator : true
        } , {
            name : 'Atibuir Participantes para Expedição',
            bclass : 'variavel',
            onpress : setParticipantes
        } , {
            name : 'Atibuir Lider(s) para Expedição',
            bclass : 'variavel',
            onpress : setLider
        }],
        searchitems : [ {
            display : 'Nome Protocolo',
            name : 'nm_protocolo',
            isdefault : true
        } ],
        sortname : "id",
        sortorder : "desc",
        usepager : true,
        useRp : true,
        rp : 15,
        showTableToggleBtn : true,
        width : '100%',
        height : 460
    });
});

function setLider(){
    $.post('/expedicao-lider/verifica-permissao', {
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
                $(window.document.location).attr('href',"/expedicao-lider/index/nId/"+nId);
            }
        }
        else {
            jAlert("Voce não possui permissão de acessar essa área.","Acesso Negado");
        }
    });
}


function setParticipantes(){
    $.post('/participantes-expedicao/verifica-permissao', {
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
                $(window.document.location).attr('href',"/participantes-expedicao/index/nId/"+nId);
            }
        }
        else {
            jAlert("Voce não possui permissão de acessar essa área.","Acesso Negado");
        }
    });
}



function novoPrograma() {
    $.post('/expedicao/verifica-permissao', {
        sOP: "Cadastrar"
    } ,function(data) {
        if (data) {
            $(window.document.location).attr('href',"/expedicao/cadastrar");
        }
        else {
            jAlert("Voce não possui permissão de acessar essa área.","Acesso Negado");
        }
    });
}

function alterarPrograma() {
    $.post('/expedicao/verifica-permissao', {
        sOP: "Alterar"
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
                $(window.document.location).attr('href',"/expedicao/alterar/nId/"+nId);
            }
        }
        else {
            jAlert("Voce não possui permissão de acessar essa área.","Acesso Negado");
        }
    });
}

function excluirPrograma() {
    if ($('.trSelected').length == 0) {
        jAlert("Você deve selecionar ao menos um ítem.","Atenção");
    }
    else {
        if (confirm('Você tem certeza que deseja excluir ' + $('.trSelected').length + ' Expedição(ões)?')) {
            $.post('/expedicao/verifica-permissao', {
                sOP: "Excluir"
            } ,function(data) {
                if (data) {
                    var selArry = [];
                    var nI = 1;
                    $("div.flexigrid .trSelected").each(function(trI,tr){
                        selArry.push($(tr).attr("id").substr(3));
                    });
                    $.post('/expedicao/excluir', {
                        fId: selArry, 
                        sOP: "Excluir"
                    } ,function(data) {
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
