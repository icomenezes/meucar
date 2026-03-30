
var values = "";
var login;
var senha;
var qtdLogin = 0;
var storage = window.localStorage;

$("#btn_logar").click(function(){

    if(navigator.connection.type == "none"){
        alert("Não há conexão com a internet");
    }else{

        login = $("#login").val();
        senha = $("#senha").val();


        if(login != "" && senha != ""){

            
            $("body").prepend('<div id="div_aguarde" style="z-index: 10; text-align:center; opacity:0.8; background-color:#000;width:100%; height:1000px; position:fixed; font-size: 30px; color:#fff;"><div style="margin-top:200px;">Aguarde...</div></div>');

            $.ajax({

                url: "https://meucar.trsystem.com.br/apps/login-avaliacar/app_avaliacoes/true",
                type: "POST",
                async: false,
                data: ({"login": login, "senha": senha}),
                dataType: "TEXT",
                success: function(resp){

                    console.log(resp);

                    if(resp != "-1"){

                        values = JSON.parse(resp);

                        storage.setItem('id_usuario', values.id); // Pass a key name and its value to add or update that key.
                        storage.setItem('id_empresa', values.id_empresa); // Pass a key name and its value to add or update that key.
                        storage.setItem('nome', values.nome); // Pass a key name and its value to add or update that key.
                        storage.setItem('login', login);
                        storage.setItem('senha', senha);
                        storage.setItem('data_atualizacao', values.data_atualizacao);
                        storage.setItem('atualizacao', 1);
                        storage.setItem('id_perfil', values.id_perfil);
                        storage.setItem('celular_superior', values.celular_superior);
                        storage.setItem('telefone_avaliador', values.celular);

                        location.replace("list.html");
                      
                    }else{
                        if(resp == "-1"){
                            alert("Usuário não encontrado, por favor verifique as suas credenciais e tente novamente!");
                        }else{
                            alert("Houve um erro inesperado, por favor verifique se o dispositivo está conectado a internet e tente novamente!");
                        }
                    }

                }

            });
            
            $("#div_aguarde").remove();

        }else{
            alert("Os campos 'Login' e 'Senha' devem ser preenchidos!");
            $("#div_aguarde").remove();
        }

    }

});

var arrLogin;
$(document).ready(function(){

    $("body").prepend('<div id="div_aguarde" style="z-index: 10; text-align:center; opacity:0.8; background-color:#000;width:100%; height:1000px; position:fixed; font-size: 30px; color:#fff;"><div style="margin-top:200px;">Aguarde...</div></div>');

    if(navigator.connection.type != "none"){

        $.ajax({

            url: "https://meucar.trsystem.com.br/apps/login-avaliacar/app_avaliacoes/true",
            type: "POST",
            async: false,
            data: ({"login": storage.getItem('login'), "senha": storage.getItem('senha')}),
            dataType: "TEXT",
            success: function(resp){

                if(resp != "-1"){

                    values = JSON.parse(resp);

                    if(values.data_atualizacao != storage.getItem('data_atualizacao')){
                        storage.setItem('atualizacao', 1);
                        storage.setItem('data_atualizacao', values.data_atualizacao);
                    }

                    location.replace("list.html");

                }else{

                    if(resp == "-1"){
                        alert("Usuario não encontrado, por favor verifique as suas credenciais e tente novamente!");
                        $("#modal_login").css("display","block");
                        $("#div_aguarde").remove();
                    }else{
                        alert("Houve um erro inesperado, por favor verifique se o dispositivo está conectado a internet e tente novamente!");
                    }

                }

            }

        });

    }

});