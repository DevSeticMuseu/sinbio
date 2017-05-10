$('document').ready(function() {
    $("#fIdHoraColeta").mask("99:99:99",{
        placeholder:"hh:mm:ss"
    });
});

$('#fIdUf').change(function() {
    $.ajax('/amostra/cadastrar' ,{
        type: "get",
        data: {
            'data': $('#fIdDataExpedicao').val(),
            'idProtocolo': $('#fIdProtocolo').val(),
            'idUf': $('#fIdUf').val(),
            'idMunicipio': $('#fIdMunicipio').val(),
            'idLocalidade': $('#fIdLocalidade').val(),
            'op': 'filtrarCep',
            'filtro': 'municipio'
        },
        dataType: "json",
        beforeSend: function(){
            $('#fIdMunicipio').html('<option value="null">Aguarde...</option>');
            $('#fIdExpedicao').html('<option value="null">Aguarde...</option>');
        },
        success: function(json) {
            $('#fIdMunicipio').html('<option value="null"></option>');
            for(i in json.municipios) {
                $('#fIdMunicipio').append('<option value="' + json.municipios[i].id + '">' + json.municipios[i].nm_municipio + '</option>');
            }
            $('#fIdExpedicao').empty();
            $('#fIdExpedicao').html('<option value="null"></option>');
            for(i in json.expedicoes) {
                $('#fIdExpedicao').append('<option value="' + json.expedicoes[i].id + '">' + json.expedicoes[i].data_inicio + " - " + json.expedicoes[i].nm_localidade + '</option>');
            }
        }
    });
})

$('#fIdMunicipio').change(function() {
    $.ajax('/amostra/cadastrar' ,{
        type: "get",
        data: {
            'data': $('#fIdDataExpedicao').val(),
            'idProtocolo': $('#fIdProtocolo').val(),
            'idUf': $('#fIdUf').val(),
            'idMunicipio': $('#fIdMunicipio').val(),
            'idLocalidade': $('#fIdLocalidade').val(),
            'op': 'filtrarCep',
            'filtro': 'localidade'
        },
        dataType: "json",
        beforeSend: function(){
            $('#fIdLocalidade').html('<option value="null">Aguarde...</option>');
            $('#fIdExpedicao').html('<option value="null">Aguarde...</option>');
        },
        success: function(json) {
            $('#fIdLocalidade').html('<option value="null"></option>');
            for(i in json.localidades) {
                $('#fIdLocalidade').append('<option value="' + json.localidades[i].id + '">' + json.localidades[i].nm_localidade + '</option>');
            }
            $('#fIdExpedicao').empty();
            $('#fIdExpedicao').html('<option value="null"></option>');
            for(i in json.expedicoes) {
                $('#fIdExpedicao').append('<option value="' + json.expedicoes[i].id + '">' + json.expedicoes[i].data_inicio + " - " + json.expedicoes[i].nm_localidade + '</option>');
            }
        }
    });
})

$('#fIdLocalidade').change(function() {
    $.ajax('/amostra/cadastrar' ,{
        type: "get",
        data: {
            'data': $('#fIdDataExpedicao').val(),
            'idProtocolo': $('#fIdProtocolo').val(),
            'idUf': $('#fIdUf').val(),
            'idMunicipio': $('#fIdMunicipio').val(),
            'idLocalidade': $('#fIdLocalidade').val(),
            'op': 'filtrarCep',
            'filtro': 'sitio'
        },
        dataType: "json",
        beforeSend: function(){
            $('#fIdExpedicao').html('<option value="null">Aguarde...</option>');
            $('#fIdSitio').html('<option value="null">Aguarde...</option>');
        },
        success: function(json) {
            $('#fIdSitio').empty();
            $('#fIdSitio').html('<option value="null"></option>');
            for(i in json.sitios) {
                $('#fIdSitio').append('<option value="' + json.sitios[i].id + '">' + json.sitios[i].nm_sitio + '</option>');
            }
            $('#fIdExpedicao').empty();
            $('#fIdExpedicao').html('<option value="null"></option>');
            for(i in json.expedicoes) {
                $('#fIdExpedicao').append('<option value="' + json.expedicoes[i].id + '">' + json.expedicoes[i].data_inicio + " - " + json.expedicoes[i].nm_localidade + '</option>');
            }
        }
    });
})

$('#fIdDataExpedicao').change(function() {
    $.ajax('/amostra/cadastrar' ,{
        type: "get",
        data: {
            'data': $('#fIdDataExpedicao').val(),
            'idProtocolo': $('#fIdProtocolo').val(),
            'idUf': $('#fIdUf').val(),
            'idMunicipio': $('#fIdMunicipio').val(),
            'idLocalidade': $('#fIdLocalidade').val(),
            'op': 'filtrarExp'
        },
        dataType: "json",
        beforeSend: function(){
            $('#fIdExpedicao').html('<option value="null">Aguarde...</option>');
        },
        success: function(json) {
            $('#fIdExpedicao').empty();
            $('#fIdExpedicao').html('<option value="null"></option>');
            for(i in json.expedicoes) {
                $('#fIdExpedicao').append('<option value="' + json.expedicoes[i].id + '">' + json.expedicoes[i].data_inicio + " - " + json.expedicoes[i].nm_localidade + '</option>');
            }
        }
    });
})

$('#fIdProtocolo').change(function() {
    $.ajax('/amostra/cadastrar' ,{
        type: "get",
        data: {
            'data': $('#fIdDataExpedicao').val(),
            'idProtocolo': $('#fIdProtocolo').val(),
            'idUf': $('#fIdUf').val(),
            'idMunicipio': $('#fIdMunicipio').val(),
            'idLocalidade': $('#fIdLocalidade').val(),
            'op': 'filtrarExp'
        },
        dataType: "json",
        beforeSend: function(){
            $('#fIdExpedicao').html('<option value="null">Aguarde...</option>');
        },
        success: function(json) {
            $('#fIdExpedicao').empty();
            $('#fIdExpedicao').html('<option value="null"></option>');
            for(i in json.expedicoes) {
                $('#fIdExpedicao').append('<option value="' + json.expedicoes[i].id + '">' + json.expedicoes[i].data_inicio + " - " + json.expedicoes[i].nm_localidade + '</option>');
            }
        }
    });
})

function addItemsHiddenVariaveis() {
    var array = [];
    if($('#fIdVariaveis').val() != null) {
        array = $('#fIdVariaveis').val();
        $('#hVariaveis').val(array);
    }
    return array;
}

var selected = addItemsHiddenVariaveis();
$('#fIdVariaveisCategoria').change(function() {
    $.ajax('/amostra/cadastrar' ,{
        type: "get",
        data: {
            'idCategoriaVariavel': $('#fIdVariaveisCategoria').val(),
            'op': 'filtrarVariaveis'
        },
        dataType: "json",
        beforeSend: function(){
            $('#fIdVariaveis').html('<option value="">Aguarde...</option>');
        },
        success: function(json) {
            $('#fIdVariaveis').empty();
            $('#fIdVariaveis').html('<option value=""></option>');
            for(i in json) {
                var id = json[i].id;
                var retorno = selected.indexOf(id.toString());
                if(retorno == "-1") {
                    $('#fIdVariaveis').append('<option value="' + json[i].id + '">' + json[i].nm_variavel + '</option>');
                } else {
                    $('#fIdVariaveis').append('<option value="' + json[i].id + '" selected="selected">' + json[i].nm_variavel + '</option>');
                }
            }
        }
    });
})

$('#fIdVariaveis').change(function(e) {
    try {
        if(selected.indexOf(e.added.id) == -1)
            selected.push(e.added.id);
    } catch(err) {}
    try {
        var index = selected.indexOf(e.removed.id);
        selected.splice(index, 1);
    } catch(err) {}
    
    $('#hVariaveis').val(selected);
})