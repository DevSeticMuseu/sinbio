function buscarMunicipios(idUf) {
    $.ajax('/instituicao/cadastrar' ,{
        type: "get",
        data: {
            'idUf': idUf,
            'op': 'filtrarCep'
        },
        dataType: "json",
        beforeSend: function(){
            $('#fIdMunicipio').html('<option value="">Aguarde...</option>');
        },
        success: function(json) {
            $('#fIdMunicipio').html('<option value=""></option>');
            for(i in json) {
                $('#fIdMunicipio').append('<option value="' + json[i].id + '">' + json[i].nm_municipio + '</option>');
            }
        }
    });
}

//$(buscarMunicipios(3));

$('#fIdUf').change(function() {
    buscarMunicipios($('#fIdUf').val());
})

//$('#fIdMunicipio').change(function() {
//    $.ajax('/amostra/cadastrar' ,{
//        type: "get",
//        data: {
//            'data': $('#fIdDataExpedicao').val(),
//            'idUf': $('#fIdUf').val(),
//            'idMunicipio': $('#fIdMunicipio').val(),
//            'idLocalidade': $('#fIdLocalidade').val(),
//            'op': 'filtrarCep',
//            'filtro': 'localidade'
//        },
//        dataType: "json",
//        beforeSend: function(){
//            $('#fIdLocalidade').html('<option value="">Aguarde...</option>');
//            $('#fIdExpedicao').html('<option value="">Aguarde...</option>');
//        },
//        success: function(json) {
//            $('#fIdLocalidade').html('<option value=""></option>');
//            for(i in json.localidades) {
//                $('#fIdLocalidade').append('<option value="' + json.localidades[i].id + '">' + json.localidades[i].nm_localidade + '</option>');
//            }
//        }
//    });
//})
