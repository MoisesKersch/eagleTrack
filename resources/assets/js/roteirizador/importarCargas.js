var bt_done = null;
var dropzone = null;

Dropzone.autoDiscover = false;
$(document).ready(function() {
    var imptPonto = $("#importarCargas")
    imptPonto.each(function(idx, element) {
        iniciaDropZone();
    })

    $(document).on("click", ".confirma-importacao", confirmaImportacao)
    function confirmaImportacao(e){
        e.preventDefault()
        $(this).attr('href', '#');
        codigo = $(this).data('carga')
        $.post(ROOT+'/painel/roteirizador/importar/cargas/confirmar', {codigo:codigo}, function(dados){
            var status = dados.codigo;
            if(status == '500') {
                alert('Ponto ainda não cadastrado!');
                $(".confirma-importacao").hide();
                $(".add-carga").show();
            }else if(status == '200') {
                $('.confirma-importacao').parents('tr').remove();
            }
        })
    }
});

function iniciaDropZone(){
    var table;
    var myDropzone = new Dropzone("#dropZone", {
        url: ROOT+"/painel/roteirizador/importar/cargas",
        headers: { 'X-CSRF-TOKEN': $("#token").attr('value')},
        acceptedFiles: ".txt",
        addRemoveLinks : true,
        timeout : 1000000,
        maxFiles : 1,
        init: function() {
            dropzone = this;
            this.on("addedfile", function() {
                if (this.files[1]!=null){
                    this.removeFile(this.files[0]);
                }
                extensao = (this.files[0].name.substring(this.files[0].name.lastIndexOf("."))).toLowerCase();
                if(!extensao === '.kml'){
                    alert("Formato do arquivo é invalido");
                    this.removeFile(this.files[0]);
                }
            });
            this.on("sending", function(file, xhr, formData){
                    formData.append("ircliente", $("#selectEmpresa").val());
            });
        },
        accept: function(file, done) {
            var empresa = $("#selectEmpresa")
            if($(empresa).val() < 1) {
                alert('Selecione uma empresa')
                $(empresa).focus()
                this.removeFile(this.files[0]);
            }else{
                bt_done = done;
                $("#gravarImportacao").removeClass("disabled");
                $("#gravarImportacao").on("click",function(){
                    bt_done();
                });
            }
        },
        error:function (dados, error) {

        },

        success: function(file, dados){
            var naoSalvos = dados.naoSalvo;
            var naoEncontrados = dados.naoEncontrados;
            var dataSet = [];
            var codigo = dados.codigo;
            if(naoSalvos && naoSalvos.length > 0) {
                //Esconder imput de importação
                $('.to-hidden-importacao-cargas').addClass('hidden');
                $('.to-hidden-inverse-importacao-cargas').removeClass('hidden');

                var nsdados = '<div class="alert alert-warning">';
                    nsdados += '<button type="button" data-dismiss="alert" class="close"><span aria-hidden="true">×</span><span class="sr-only">Fechar</span></button>'
                    nsdados += '<h4>Os seguintes documentos já existem do bando de dados:</h4>'
                for(i in naoSalvos){
                    nsdados += '<span>Documento: '+naoSalvos[i].documento+'</span><br />'
                }
                $('.nao-salvos').html(nsdados);
            }
            if(codigo == '415' || codigo == '417') {
                alert(dados.mensagem);
                dropzone.removeAllFiles();
                $ajusteBtnSalvarImportacao();
                $("#gravarImportacao").addClass("btn disabled salvar btn-primary btn-lg").removeClass("btn-warning");
                $("#gravarImportacao").html('<span class="glyphicon glyphicon-ok"></span>Gravar')
                return;
            }
            if(naoEncontrados && naoEncontrados.length > 0) {
                for(i in naoEncontrados) {
                    var local = [];
                    var carga = JSON.stringify(naoEncontrados[i]);
                    var paran = "cd="+naoEncontrados[i][0]+"&cl="+naoEncontrados[i][9]+"&de="+naoEncontrados[i][2]
                    local.push(naoEncontrados[i][0]);
                    local.push(naoEncontrados[i][2]);

                    var td = "";
                    //Adiciona botoes de ações na tabela;
                    td = td+"<a title='Ignorar e Remover Ponto' class='btn btn-danger ignorar-codigo-externo'>"
                        +"<span class='fa fa-minus'></span></a>";

                    td = td+"<a title='Criar um novo ponto' data-codigo='"+carga+"' href='"+ROOT+"/painel/cadastros/pontos/cadastrar?"+paran+"' target='_blank' class='btn btn-success add-carga'>"
                        +"<span class='fa fa-plus'></span></a>";

                    local.push(td);
                    dataSet.push(local);
                }

                if($.fn.DataTable.isDataTable('#tableCondigosNaoEncontrados')) {
                    $("#tableCondigosNaoEncontrados").DataTable().destroy()
                }

                table = $('#tableCondigosNaoEncontrados').DataTable({
                    paging: false,
                    retrieve: true,
                    language: traducao,
                    dom: 'Bfrtip',
                    buttons:[],
                    data: dataSet,
                    columnDefs: [{ //esconder coluna cliente
                        // "targets": [3],
                        "visible": false,
                        "searchable": false
                    },{
                        "targets": [2],
                        "orderable": false
                        }]
                });

                $(".ignorar-codigo-externo").click(function(){
                    table.row($(this).parents('tr')).remove().draw();
                    // $(this).parents('tr').remove();
                    //check se ainda tem, senao habilitar campos;
                    if(table.page.info().recordsTotal == 0){
                        $('.to-hidden-importacao-cargas').removeClass('hidden');
                        $('.to-hidden-inverse-importacao-cargas').addClass('hidden');
                    }
                })
                $(".add-carga").click(function(){
                    // $(this).parents('tr').remove();
                    table.row($(this).parents('tr')).remove().draw();
                    var carga = $(this).data('codigo');
                    var bt = $('<a title="Confirmar importação" data-carga="'+carga+'" href="#" class="btn btn-info confirma-importacao"><span class="fa fa-check"></span></a>');
                    $(this).parent().append(bt)
                    $(this).hide()
                    if(table.page.info().recordsTotal == 0){
                        $('.to-hidden-importacao-cargas').removeClass('hidden');
                        $('.to-hidden-inverse-importacao-cargas').addClass('hidden');
                    }
                })
            }else{
                alert('Cargas importadas com sucesso');
            }
            dropzone.removeAllFiles();
            $ajusteBtnSalvarImportacao();
            $("#gravarImportacao").addClass("btn disabled salvar btn-primary btn-lg").removeClass("btn-warning");
            $("#gravarImportacao").html('<span class="glyphicon glyphicon-ok"></span>Gravar')
        }

    });
}
