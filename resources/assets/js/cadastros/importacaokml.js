var bt_done = null;
var dropzone = null;

Dropzone.autoDiscover = false;
$(document).ready(function() {
    var imptPonto = $("#divImportarPonto")
    imptPonto.each(function(idx, element) {
        iniciaDropZone();
    })
});

function iniciaDropZone(){
    var table;
    var myDropzone = new Dropzone("#dropZone", {
        url: ROOT+"/painel/cadastros/pontos/importar",
        headers: { 'X-CSRF-TOKEN': $("#token").attr('value')},
        acceptedFiles: ".kml, .txt",
        addRemoveLinks : true,
        timeout : 200000,
        maxFiles : 1,
        init: function() {
            dropzone = this;
            this.on("addedfile", function() {
                if (this.files[1]!=null){
                    this.removeFile(this.files[0]);
                }
                extensao = (this.files[0].name.substring(this.files[0].name.lastIndexOf("."))).toLowerCase();
                if(!((extensao === '.kml') || (extensao === '.txt'))){
                    alert("Formato do arquivo é invalido");
                    this.removeFile(this.files[0]);
                }
            });
            this.on("sending", function(file, xhr, formData){
                    var clientes = [];
                    $('#empresa_importacao :selected').each(function(i, selected){
                        clientes[i] = $(selected).val();
                    });
                    formData.append("tipo_ponto", $("#tipo_ponto_importacao").val());
                    formData.append("raio_ponto", $("#raio_ponto_importacao").val());
                    formData.append("empresa_importacao", clientes);
            });
        },
        accept: function(file, done) {
            var clientes = [];
            $('#empresa_importacao :selected').each(function(i, selected){
                clientes[i] = $(selected).val();
            });

            if($("#raio_ponto_importacao").val() > 1000 ){
                alert("O raio dos pontos deve ser menor ou igual a 1000 Metros");
                this.removeFile(this.files[0]);
            }else if (!($("#tipo_ponto_importacao").val() != 0)) {
                alert("Selecione o tipo de ponto para importação");
                this.removeFile(this.files[0]);
            }else if (! (clientes.length > 0)) {
                alert("Selecione pelo menos uma empresa!");
                this.removeFile(this.files[0]);
            }else if($('#tableConflitosImportacao tbody').find('a').hasClass('bt_ignorar_conflito')){
                alert("Resolva todos os conflitos primeiro!");
                this.removeFile(this.files[0]);
            }else{
                bt_done = done;
                $("#gravarImportacao").removeClass("disabled");
                $ajusteBtnSalvarImportacao();
                $("#gravarImportacao").on("click",function(){
                    $('.erros-importacao').html('');
                    bt_done();
                });
            }
        },
        error:function (dados, error) {
            alert(error.mensagem);
            $ajusteBtnSalvarImportacao();
            $("#gravarImportacao").html('<span class="glyphicon glyphicon-ok"></span>Gravar')
            $("#gravarImportacao").addClass("btn disabled salvar btn-success btn-lg").removeClass("btn-warning");
            $("#gravarImportacao").children().removeClass("fa fa-spinner fa-spin fa-3x fa-fw margin-bottom");
            $("#gravarImportacao").children().addClass("glyphicon glyphicon-ok");

            dropzone.removeAllFiles();
            $('input').prop('readonly', false);
        },

        success: function(file, dados){
            var conflitos = dados.pontos_conflitantes;
            var erros = dados.erros;
            var dataSet = [];
            if(conflitos){
                for (var conflito in conflitos) {
                    var tipo = ''
                    if(conflitos[conflito].potipo == 'C'){
                        tipo = 'Ponto de Coleta';
                    }else if(conflitos[conflito].potipo == 'E'){
                        tipo = 'Ponto de Entrega';
                    }else if(conflitos[conflito].potipo == 'P'){
                        tipo = 'Restaurante/Posto Combustível'
                    }else if(conflitos[conflito].potipo == 'R'){
                        tipo = 'Área de Risco';
                    }
                    var local = [];
                        if(conflitos[conflito].podescricao == undefined){
                        conflitos[conflito].podescricao = "";
                    }
                    local.push(conflitos[conflito].podescricao);

                    if(conflitos[conflito].potipo == undefined){
                        conflitos[conflito].potipo = "";
                    }
                    local.push(tipo);

                    if(conflitos[conflito].poraio == undefined){
                        conflitos[conflito].poraio = "";
                    }
                    local.push(conflitos[conflito].poraio);

                    if(conflitos[conflito].pocodigocliente == undefined){
                        conflitos[conflito].pocodigocliente = "";
                    }
                    local.push(conflitos[conflito].pocodigocliente);

                    if(conflitos[conflito].polatitude == undefined){
                        conflitos[conflito].polatitude = "";
                    }
                    local.push(conflitos[conflito].polatitude);

                    if(conflitos[conflito].polongitude == undefined){
                        conflitos[conflito].polongitude = "";
                    }
                    local.push(conflitos[conflito].polongitude);

                    if(conflitos[conflito].pocodigoexterno == undefined){
                        conflitos[conflito].pocodigoexterno = "";
                    }
                    local.push(conflitos[conflito].pocodigoexterno);

                    var td = "";
                    //Adiciona botoes de ações na tabela;
                    td = td+"<a title='Ignorar e Remover Ponto' class='btn btn-danger "
                        +" bt_ignorar_conflito' value="+conflitos[conflito].pocodigo+"'>"
                        +"<span class='glyphicon glyphicon-trash'></span></a></td>";

                    td = td+"<a title='Sobreescrever Ponto' class='btn btn-success "
                        +" bt_salvar_conflito ' value='"+conflitos[conflito].pocodigo+"'>"
                        +"<span class='glyphicon glyphicon-ok'></span></a></td>";

                    local.push(td);
                    dataSet.push(local);
                }

                $('#tableConflitosImportacao').DataTable().destroy();

                table = $('#tableConflitosImportacao').DataTable({
                    paging: false,
                    retrieve: true,
                    language: traducao,
                    dom: 'Bfrtip',
                    buttons:[],
                    data: dataSet,
                    columnDefs: [{ //esconder coluna cliente
                        "targets": [3],
                        "visible": false,
                        "searchable": false
                        }]
                });

                $('#descartarConflitos').on('click', function(e){
                    table.clear().draw();
                    $("#descartarConflitos").addClass("disabled");
                });

                $('.bt_ignorar_conflito').on('click', function(e){
                    var thad = $(this);
                    table.row($(thad).parents('tr')).remove().draw();
                });

                $('.bt_salvar_conflito').on('click', function(e){
                    var thad = $(this);
                    var row = table.row($(thad).parents('tr')).data();
                    var tipo = [];
                    tipo['Ponto de Coleta'] = 'C';
                    tipo['Ponto de Entrega'] = 'E';
                    tipo['Restaurante/Posto Combustível'] = 'P';
                    tipo['Área de Risco'] = 'R';
                    $.ajax({
                        type: 'POST',
                        url: ROOT+'/painel/cadastros/pontos/salvar_conflito',
                        data: { 'podescricao'    :row[0],
                                'potipo'         :tipo[row[1]],
                                'poraio'         :row[2],
                                'pocodigocliente':row[3],
                                'polatitude'     :row[4],
                                'polongitude'    :row[5],
                                'pocodigoexterno':row[6]
                            },
                        success: function(response){
                            table.row($(thad).parents('tr')).remove().draw();
                        }
                    });
                });

            }else{
                alert("Arquivo importado com sucesso e sem conflitos!");
            }
            if(erros.length > 0){
                $('.erros-importacao').append('<h3> Erros de Importação <h4><hr />');
                for (var i in erros) {
                    console.log(erros[i]);
                    $('.erros-importacao').append('<h5>'+erros[i]+'<h5>')
                    $('.erros-importacao').css('padding', '30px');
                }
            }

             //remover classe salvar do botão salvar
             $(document).ready(function(){
                 if(conflitos.length > 0){
                     $("#descartarConflitos").removeClass("disabled");
                 }

                $("#gravarImportacao").addClass("btn disabled salvar btn-success btn-lg").removeClass("btn-warning");
                $("#gravarImportacao").html('<span class="glyphicon glyphicon-ok"></span>Gravar')
                $("#gravarImportacao").children().removeClass("fa fa-spinner fa-spin fa-3x fa-fw margin-bottom");
                $("#gravarImportacao").children().addClass("glyphicon glyphicon-ok");
                $ajusteBtnSalvarImportacao();
                 dropzone.removeAllFiles();
                 $('input').prop('readonly', false);
            });
        }
    });
}
