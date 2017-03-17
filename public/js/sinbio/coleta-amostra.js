$().ready(function() {
    $(".grid").flexigrid({
        url : '/amostra/gera-xml',
        dataType : 'xml',
        colModel : [ {
            display : 'Id',
            name : 'id',
            width : 40,
            sortable : true,
            align : 'center'
        },{
            display : 'C&oacute;digo Amostra',
            name : 'id_amostra_coleta',
            width : 140,
            sortable : true,
            align : 'left'
        },{
            display : 'Coletores',
            name : 'citacao',
            width : 140,
            sortable : true,
            align : 'left'
        },
        {
            display : 'Protocolo',
            name : 'nm_protocolo',
            width : 140,
            sortable : true,
            align : 'left'
        },
        {
            display : 'Nome Metodos',
            name : 'nm_metodo',
            width : 120,
            sortable : true,
            align : 'left'
        },{
            display : 'Expedicao',
            name : 'coleta_expedicao_id',
            width : 80,
            sortable : true,
            align : 'left'
        },{
            display : 'Atrativos',
            name : 'nm_atrativos',
            width : 40,
            sortable : true,
            align : 'left'
        },{
            display : 'Conservacao',
            name : 'conservacao_material',
            width : 100,
            sortable : true,
            align : 'left'
        },{
            display : 'Destinacao',
            name : 'nm_destinacao',
            width : 220,
            sortable : true,
            align : 'left'
        },{
            display : 'Data Coleta',
            name : 'data_coleta',
            width : 80,
            sortable : true,
            align : 'left'
        }, {
            display : 'Hora coleta',
            name : 'hora_coleta',
            width : 90,
            sortable : true,
            align : 'left'
        }, {
            display : 'Latitude',
            name : 'latitude',
            width : 70,
            sortable : true,
            align : 'left'
        }, {
            display : 'Direção Latitude',
            name : 'direcao_latitude',
            width : 40,
            sortable : true,
            align : 'left'
        }, {
            display : 'Longitude',
            name : 'longitude',
            width : 70,
            sortable : true,
            align : 'left'
        }, {
            display : 'Direção Longitude',
            name : 'direcao_longitude',
            width : 40,
            sortable : true,
            align : 'left'
        },{
            display : 'Projecao',
            name : 'sistema_projecao',
            width : 100,
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
        }, {
            name : 'Atibuir Coletores Participantes da Amostra',
            bclass : 'variavel',
            onpress : setParticipantes
        } ],
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


function novoPrograma() {
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

function alterarPrograma() {
    $.post('/amostra/verifica-permissao', {
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
                $(window.document.location).attr('href',"/amostra/alterar/nId/"+nId);
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
            $.post('/amostra/verifica-permissao', {
                sOP: "Excluir"
            } ,function(data) {
                if (data) {
                    var selArry = [];
                    var nI = 1;
                    $("div.flexigrid .trSelected").each(function(trI,tr){
                        selArry.push($(tr).attr("id").substr(3));
                    });
                    $.post('/amostra/excluir', {
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
