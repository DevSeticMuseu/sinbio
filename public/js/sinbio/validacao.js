$(document).ready(function () {
    
    $('#insere-altera').validate({
        rules: {
            "fCpf": {cpf: true},
            "fCpfDiretor": {cpf: true},
            "ajaxCpf": {cpf: true},
            "fCnpj": {cnpj: true},
            "fNumTelefone": {telefone: true},
            "fNumCelular": {telefone: true},
            "fCep": {cep: true},
            "fEmail": {email: true},
            "fEmailContato": {email: true},
            "fSenha": {
                minlength: 6,
                maxlength: 16
            },
            "fSenhaConf": {
                minlength: 6,
                maxlength: 16,
                equalTo: "#fSenha"
            }
        },
        messages: {
            "fSenhaConf": {
                minlength: "A senha deve possuir ao menos 6 digítos.",
                maxlength: "A senha deve possuir no máximo 16 digítos.",
                equalTo: "As senhas devem ser iguais."},
            "fSenha": {
                minlength: "A senha deve possuir ao menos 6 digítos.",
                maxlength: "A senha deve possuir no máximo 16 digítos."},
            "fLogin": {remote: "Este Login já existe"}
        },
        errorClass: "help-inline",
        errorElement: "span",
        highlight: function (element, errorClass, validClass) {
            $(element).parents('.control-group').addClass('error');
        },
        unhighlight: function (element, errorClass, validClass) {
            $(element).parents('.control-group').removeClass('error');
            $(element).parents('.control-group').addClass('success');
        },
        submitHandler: function (form) {
            form.submit();
        }
    });
});

function checaEmailFone() {
    if ($('#email-input').val()){
        $('.phone-icon').removeClass( 'icon-asterisk' );
        $('#phone-input').removeClass( 'required' );
    } else {
        $('.phone-icon').addClass( 'icon-asterisk' );
        $('#phone-input').addClass( 'required' );
    }
        
    if ($('#phone-input').val()){
        $('.email-icon').removeClass( 'icon-asterisk' );
        $('#email-input').removeClass( 'required' );
    } else {
        $('.email-icon').addClass( 'icon-asterisk' );
        $('#email-input').addClass( 'required' );
    }
}

//máscara que só permite valores monetários (dec = numero de algarismos decimais)
function soValorC(valor, dec) {
    // parte decimal e inteira do n?mero
    intstr = '';
    decstr = '';
    temp_valor = '';

    // remove todos os caracteres que n?o s?o v?lidos
    var validos = "0123456789";
    var numero_ok = '';
    for (i = 0; i < valor.length; i++) {
        if (validos.indexOf(valor.substr(i, 1)) != -1) {
            temp_valor += valor.substr(i, 1);
        }
    }

    // separa parte decimal de parte inteira
    if (temp_valor.length == 0) {
        intstr = "0";
        // concatena zeros
        for (i = 0; i < dec; i++) {
            decstr += "0";
        }
    }
    else if (temp_valor.length == 1) {
        intstr = "0";
        // concatena zeros
        for (i = 0; i < (dec - 1); i++) {
            decstr += "0";
        }
        decstr += temp_valor;
    }
    else if (temp_valor.length <= dec) {
        intstr = "0";
        decstr = temp_valor;
        for (i = temp_valor.length; i < dec; i++) {
            decstr += "0";
        }
    }
    else {
        intstr = temp_valor.substring(0, (temp_valor.length - dec));
        decstr = temp_valor.substring((temp_valor.length - dec), temp_valor.length);
    }

    // remove zeros a esquerda da parte inteira
    temp_valor = intstr;
    intstr = '';
    primeiro_valor = false;
    for (i = 0; i < temp_valor.length; i++) {
        if (primeiro_valor == false) {
            if (temp_valor.substr(i, 1) != "0") {
                primeiro_valor = true;
                intstr += temp_valor.substr(i, 1);
            }
        }
        else {
            intstr += temp_valor.substr(i, 1);
        }
    }
    if (intstr.length == 0) {
        intstr = "0";
    }

    // adiciona "." a cada 3 algarismos (partindo da direita para esquerda)
    temp_valor = intstr;
    intstr = '';
    ponto = 0;
    for (i = temp_valor.length; i > 0; i--) {
        if (((ponto % 3) == 0) &&
                (ponto != 0)) {
            intstr = temp_valor.substr(i - 1, 1) + "." + intstr;
        }
        else {
            intstr = temp_valor.substr(i - 1, 1) + intstr;
        }
        ponto++;
    }

    // concatena v?rgula entre parte inteira e decimal
    temp_valor = intstr + "," + decstr;

    // retorna valor concatenado com v?rgula
    return temp_valor;
}

