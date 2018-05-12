$(document).ready(function(){
    var perfilAcesso = $("#listarPerfilAcesso");
    var cadastroPerfil = $("#cadastroPerfilAcesso");
    var selectCliente = $(".select-cliente-pe");
    var selectGrupoVeiculo = $(".grupos-veiculos");
    var modulosSistema = [];
    var editar = false;

    selectCliente.on('change', function(){
        id = $(this).val();
        $.post(ROOT+'/painel/cadastros/perfil/acesso/grupos/veiculos',{id:id}, function(dados){
            var oldVeiculos = $('#oldVeiculos').val();
            oldVeiculos = oldVeiculos != undefined? JSON.parse(oldVeiculos) : undefined;
            var grupos = dados.grupos;
            var opt = '';
            for(i in grupos){
                opt += '<option value="'+grupos[i].gvcodigo+'">'+grupos[i].gvdescricao+'</option>'
            }
            $('.grupos-veiculos').html(opt);

            var veiculos = dados.veiculos;
            opt = '';
            for(i in veiculos){
                if(oldVeiculos != undefined && oldVeiculos.includes(veiculos[i].vecodigo)){
                    opt += '<option selected value="'+veiculos[i].vecodigo+'">'+veiculos[i].veplaca+' | '+veiculos[i].veprefixo+'</option>'
                }else{
                    opt += '<option value="'+veiculos[i].vecodigo+'">'+veiculos[i].veplaca+' | '+veiculos[i].veprefixo+'</option>'
                }
            }
            $('.pe-veiculos').html(opt);
            modulosSistema = dados.modulosSistema;
        });

        $(".load-perfil-acesso").html('<span class="fa fa-spinner fa-spin fa-3x fa-fw margin-bottom"></span>');

        $.post(ROOT+'/painel/cadastros/perfil/acesso/perfilItens',{'id':selectCliente.val(),'pecodigo': $('#peCodigo').val() },
            function(dados){
                var cat = `<div class='content col-sm-12' >`;
                var menu_categoria = '<span id="categoriasNome">Categorias</span>';
                var m_count = 0;

                //Setar valores necessários para a implementação do editar;
                var permissoes = null;
                if(dados.perfil != undefined && dados.perfil.permissoes != undefined){
                    editar = true
                    permissoes = dados.perfil.permissoes;
                }

                for(m in dados.menu){
                    var pmcodigo = dados.menu[m].pmcodigo;
                    var itens = dados.menu[m].itens;
                    var count = 0;
                    if(itens.length > 0){
                        var i_count = 0;
                        //pegar o item correspondente a este ĺinha
                        count ++;
                        m_count++;
                        var rotei_dont_use = dados.menu[m].pmdescricao === 'Roteirizador' ? true: false;
                        var mapa_dont_use = dados.menu[m].pmdescricao === 'Mapa' ? true: false;
                        var relatorios_dont_use = dados.menu[m].pmdescricao === 'Relatórios' ? true: false;
                        var catId = dados.menu[m].pmcodigo; //id da categoria
                        var actives = m_count == 1? ' active ' : '';
                        var icon = m_count == 1? ' <span class="active fa fa-angle-double-right"></span> ' : '';
                        menu_categoria += `<div class="col-sm-12 linha-categoria">
                                            <a class="`+actives+` menu-categoria" data-id="`+catId+`" href="#" >`+dados.menu[m].pmdescricao+` </a>
                                                `+icon+`
                                         </div>`;
                        var linha = '';
                        var hidden = m_count == 1? '' : 'hidden';
                        //Checks do cabecalho
                        cat += `
                        <div class="col-sm-12 content-permissoes content-`+catId+` `+hidden+`">
                            <div class="col-sm-12 ">
                                <span class="col-sm-3"></span>
                                <div class="cabecalho-permissoes col-sm-9">
                                    <div class="form-title-perimssoes">Visualizar <input `+ (editar? '' : 'checked') +` class="pevisualizar-`+m+` pevisualizar-td" data-cat="`+m+`" type="checkbox" name="" id=""></div>
                                    <div class="form-title-perimssoes">Cadastrar <input `+ (editar? '' : 'checked') +` class="pecadastrar-`+m+` pecadastrar-td" data-cat="`+m+`" type="checkbox" name="" id=""></div>
                                    <div class="form-title-perimssoes">Editar <input `+ (editar? '' : 'checked') +` class="peeditar-`+m+` peeditar-td" data-cat="`+m+`" type="checkbox" name="" id=""></div>
                                    <div class="form-title-perimssoes">Excluir <input class="peexcluir-`+m+` peexcluir-td" data-cat="`+m+`" type="checkbox" name="" id=""></div>
                                    <div class="form-title-perimssoes">Importar <input `+ (editar? '' : 'checked') +` class="peimportar-`+m+` peimportar-td" data-cat="`+m+`" type="checkbox" name="" id=""></div>
                                </div>
                            </div> `;

                        for(i in itens) {
                            i_count++;
                            var updater_layout = '';
                            // var updater_layout = i_count == 1 ? ' updater-layout ' : '';
                            var picodigo = itens[i].picodigo;
                            var old = null;
                            for (var j in permissoes) {
                                if(picodigo == permissoes[j].ppperfilitens){
                                    var old = permissoes[j];
                                }
                            }
                            // console.log(picodigo,permissoes[picodigo]);
                            //Checks dos ítens
                            cat +=  `
                            <div class="permissoes permissoes-`+m+`">
                                <span class="perfil-itens-nome col-sm-3">`+itens[i].pidescricao+`</span>
                                <div class="linha-linha col-sm-9">`;
                                    //valores padrões;
                                    console.log(editar)

                                    var cond_visualizar = editar ? old != null ? old.ppvisualizar ? 'checked' : '' : '' : 'checked';
                                    var cond_cadastrar = editar? old != null ? old.ppcadastrar? 'checked' : '' : '' : 'checked';
                                    var cond_editar = editar? old != null ? old.ppeditar? 'checked' : '' : '' : 'checked';
                                    var cond_excluir = editar? old != null ? old.ppexcluir? 'checked' : '' : '' : '';
                                    var cond_importar = editar? old != null ? old.ppimportar? 'checked' : '' : '' : 'checked';
                                    var l_visualizar = `<input `+ cond_visualizar +` class="perfil-permissoes ppvisualizar ppvisualizar-`+m+` ppvisualizar-`+m+`-`+i+` `+updater_layout+`  " data-cat="`+m+`" data-item="`+i+`" type="checkbox" name="ppvisualizar-`+pmcodigo+`-`+picodigo+`">`;
                                    var l_cadastrar = `<input `+cond_cadastrar+` class="perfil-permissoes ppcadastrar ppcadastrar-`+m+` ppcadastrar-`+m+`-`+i+` `+updater_layout+` " data-cat="`+m+`" data-item="`+i+`" type="checkbox" name="ppcadastrar-`+pmcodigo+`-`+picodigo+`">`;
                                    var l_editar = `<input `+cond_editar+` class="perfil-permissoes ppeditar ppeditar-`+m+` ppeditar-`+m+`-`+i+` `+updater_layout+` " data-cat="`+m+`" data-item="`+i+`" type="checkbox" name="ppeditar-`+pmcodigo+`-`+picodigo+`">`;
                                    var l_excluir = `<input `+cond_excluir+` class="perfil-permissoes ppexcluir ppexcluir-`+m+` ppexcluir-`+m+`-`+i+` `+updater_layout+` " data-cat="`+m+`" data-item="`+i+`" type="checkbox" name="ppexcluir-`+pmcodigo+`-`+picodigo+`">`;
                                    var l_importar = `<input `+cond_importar+` class="perfil-permissoes ppimportar ppimportar-`+m+` ppimportar-`+m+`-`+i+` `+updater_layout+` " data-cat="`+m+`" data-item="`+i+`" type="checkbox" name="ppimportar-`+pmcodigo+`-`+picodigo+`">`;

                                    //Campos null para substituir quando não possui
                                    var pl_visualizar = `<span class="perfil-permissoes ppvisualizar" ><span></span></span>`;
                                    var pl_cadastrar = `<span class="perfil-permissoes ppcadastrar" ><span></span></span>`;
                                    var pl_editar = `<span class="perfil-permissoes ppeditar" ><span></span></span>`
                                    var pl_excluir = `<span class="perfil-permissoes ppexcluir" ><span></span></span>`
                                    var pl_importar = `<span class="perfil-permissoes ppimportar" ><span></span></span>`;

                                    //INTERSESSÕES Campos que o menu não possui
                                    if(rotei_dont_use && itens[i].pidescricao === 'Importar Cargas'|| itens[i].pidescricao === 'Rota Automática' || itens[i].pidescricao === 'Rota Manual' ){
                                        if(itens[i].pidescricao === 'Importar Cargas'){
                                            l_cadastrar = pl_cadastrar;
                                        }
                                        // l_visualizar = pl_visualizar;
                                        // l_editar = pl_editar;
                                        // l_excluir = pl_excluir;
                                        // l_cadastrar = pl_cadastrar;
                                        l_importar = pl_importar;
                                    }else if(rotei_dont_use && itens[i].pidescricao === 'Finalização de Rotas'){
                                        l_cadastrar = pl_cadastrar;
                                        l_importar = pl_importar;
                                    }else if(rotei_dont_use && itens[i].pidescricao === 'Monitoramento'){
                                        // l_cadastrar = pl_cadastrar;
                                        // l_editar = pl_editar;
                                        l_excluir = pl_excluir;
                                        l_importar = pl_importar;
                                    }else if(relatorios_dont_use && itens[i].pidescricao === 'Pontos'){
                                        l_cadastrar = pl_cadastrar;
                                        l_editar = pl_editar;
                                        l_excluir = pl_excluir;
                                        // l_importar = pl_importar;
                                    }else if(mapa_dont_use && itens[i].pidescricao === 'Pontos'){
                                        // l_cadastrar = pl_cadastrar;
                                        // l_editar = pl_editar;
                                        // l_excluir = pl_excluir;
                                        l_importar = pl_importar;
                                    }else if(relatorios_dont_use || mapa_dont_use && itens[i].pidescricao === 'Veículos'){
                                        l_cadastrar = pl_cadastrar;
                                        l_editar = pl_editar;
                                        l_excluir = pl_excluir;
                                        l_importar = pl_importar;
                                    }else if(relatorios_dont_use || mapa_dont_use && itens[i].pidescricao === "Painel Informações") {
                                        l_cadastrar = pl_cadastrar;
                                        l_excluir = pl_excluir;
                                        l_importar = pl_importar;
                                    }else if(relatorios_dont_use || mapa_dont_use && itens[i].pidescricao === "Painel de Controle") {
                                        l_cadastrar = pl_cadastrar;
                                        l_excluir = pl_excluir;
                                        l_importar = pl_importar;
                                    }else if(relatorios_dont_use || mapa_dont_use && itens[i].pidescricao === "Ferramentas") {
                                        l_cadastrar = pl_cadastrar;
                                        l_excluir = pl_excluir;
                                        l_importar = pl_importar;
                                        l_editar = pl_editar;
                                    }

                                    if(itens[i].pidescricao != 'Painel Informações'
                                        && itens[i].pidescricao != 'Painel de Controle'
                                        && itens[i].pidescricao != 'Pontos'
                                        && itens[i].pidescricao != 'Importar Cargas'
                                        && itens[i].pidescricao != 'Rota Automática'
                                        && itens[i].pidescricao != 'Rota Manual'){ //Somente o Cadastro de Pontos possui importações,
                                        l_importar = pl_importar;
                                    }

                                    cat += l_visualizar + l_cadastrar + l_editar + l_excluir + l_importar;

                                cat +=`</div>
                                <hr class="col-sm-12" />
                            </div>`;
                        }
                        cat += `</div>`;
                    }
                }
                cat += `</div>`;
                $(".categorias-nomes").html(menu_categoria);
                $(".block-permissoes").html(cat);
                var permissoes = $('.block-permissoes').height();
                permissoes = permissoes + 50;
                $('.categorias-nomes').css({ "height": +permissoes+"px" });

                cadastroPerfil.each(function(idx, element){
                    var elem = $(element);
                    var categorias = elem.find('.linha-categoria a');
                    var selectCliente = elem.find('.select-cliente-pe');

                    $(categorias).on('click', function(e){
                        e.preventDefault();
                        var id = $(this).data('id');
                        $('.content-permissoes').addClass('hidden');
                        $('.content-'+id).removeClass('hidden');
                    });
                });

                $('.ppvisualizar').click(function(){
                    var m = $(this).data('cat');
                    var i = $(this).data('item');
                    if($(this).is(':checked')){
                        $('.ppcadastrar-'+m+`-`+i).removeAttr('disabled');
                        $('.ppeditar-'+m+`-`+i).removeAttr('disabled');
                        $('.ppexcluir-'+m+`-`+i).removeAttr('disabled');
                        $('.ppimportar-'+m+`-`+i).removeAttr('disabled');
                    }else{
                        $('.ppimportar-'+m+`-`+i).attr('disabled','disabled');
                        $('.ppimportar-'+m+`-`+i).prop('checked', false);
                        $('.ppcadastrar-'+m+`-`+i).attr('disabled','disabled');
                        $('.ppcadastrar-'+m+`-`+i).prop('checked', false);
                        $('.ppeditar-'+m+`-`+i).attr('disabled','disabled');
                        $('.ppeditar-'+m+`-`+i).prop('checked', false);
                        $('.peeditar-'+m).prop('checked', false);
                        $('.ppexcluir-'+m+`-`+i).attr('disabled','disabled');
                        $('.ppexcluir-'+m+`-`+i).prop('checked', false);
                        $('.peexcluir-'+m).prop('checked', false);
                    }
                });

                if(editar){
                    $('.updater-layout').trigger('click');
                    $('.updater-layout').trigger('click');
                }

                $(".load-perfil-acesso").html('');
            }
        );
    });
    selectCliente.trigger('change');

    selectGrupoVeiculo.on('change',function(){
        id = $(this).val();
        if(id.length > 0){
            $.post(ROOT+'/painel/cadastros/perfil/acesso/veiculos/grupo',{ids:id}, function(dados){
                var veiculos = dados.veiculos;
                for(i in veiculos){
                    $(".pe-veiculos").find("option[value='"+veiculos[i].vecodigo+"']").attr('selected', 'selected');
                }
                $(".pe-veiculos").select2({"language": "pt-BR"})
            })
        }
    });

    selectGrupoVeiculo.on('select2:unselecting', function (e) {
        var id = (e.params.args.data.id).toString(10).split(" ");
        $.post(ROOT+'/painel/cadastros/perfil/acesso/veiculos/grupo',{ids:id}, function(dados){
            var veiculos = dados.veiculos;
            for(i in veiculos){
                $(".pe-veiculos").find("option[value='"+veiculos[i].vecodigo+"']").removeAttr('selected');
            }
            $(".pe-veiculos").select2({"language": "pt-BR"});
        })
    });

    perfilAcesso.each(function(idx, element){
    	var elem = $(element);
    	var selectCliente = elem.find('.cliente-usuario');

        var status = elem.find('.bt-filtros-update.btn-primary')
        selectCliente.on('change', function(e){
            e.preventDefault();
            var thad = $(this);
            var id = $(this).val();
            var t = id.indexOf("00")
            if(t == 0) {
                $(selectCliente).children().attr('selected', true)
                $(selectCliente).select2({
                    "language": "pt-BR"
                })
            }
            tableListaPerfil()
        })
    	selectCliente.trigger('change');
    });

    $(document).on('click','.menu-categoria', function(){
        var thad = $(this);
        $('.menu-categoria').each(function(){
            if($(this).hasClass('active')){
                $(this).removeClass('active')
                $(this).siblings('.active').remove();
            }
        });
        $(thad).addClass('active');
        $(thad).parent().append('<span class="active fa fa-angle-double-right"></span>');
    });

    $(document).on('click','.btn-permissoes', function(){
        if($(this).prop('id') == 'btAcessoTotal'){
            $(".btn-permissoes").addClass("btn-default").removeClass("btn-info");
            $(this).addClass("btn-info").removeClass("btn-default");
            $(this).html('Acesso total <span class="fa fa-check">');
            $('#btnApenasVisualizar').html('Apenas visualizar');
            $('#btnMenosExcluir').html('Menos exluir');
        }else if($(this).prop('id') == 'btnApenasVisualizar'){
            $(".btn-permissoes").addClass("btn-default").removeClass("btn-info");
            $(this).addClass("btn-info").removeClass("btn-default");
            $(this).html('Apenas visualizar <span class="fa fa-check">');
            $('#btAcessoTotal').html('Acesso total');
            $('#btnMenosExcluir').html('Menos exluir');
        }else{
            $(".btn-permissoes").addClass("btn-default").removeClass("btn-info");
            $(this).addClass("btn-info").removeClass("btn-default");
            $(this).html('Menos excluir <span class="fa fa-check">');
            $('#btAcessoTotal').html('Acesso total');
            $('#btnApenasVisualizar').html('Apenas visualizar');
        }
    });

    $(document).on('click','#btAcessoTotal', function(){
        $('input.ppcadastrar, input.pecadastrar-td, input.ppexcluir, input.peexcluir-td , input.ppvisualizar, input.pevisualizar-td, input.ppeditar, input.peeditar-td, input.ppimportar, input.peimportar-td').each(function(){
            if($(this).is(':disabled')){
                $(this).removeAttr('disabled');
            }
            $(this).prop('checked', true);
        });
    });

    $(document).on('click','#btnApenasVisualizar', function(){
        $('input.ppcadastrar, input.pecadastrar-td, input.ppexcluir, input.peexcluir-td , input.ppvisualizar, input.pevisualizar-td, input.ppeditar, input.peeditar-td, input.ppimportar, input.peimportar-td').each(function(){
            if($(this).is(':disabled')){
                $(this).removeAttr('disabled');
            }
            if($(this).hasClass('ppvisualizar') || $(this).hasClass('pevisualizar-td')){
                $(this).prop('checked', true);
            }else{
                $(this).prop('checked', false);
            }
        });
    });


    $(document).on('click','#btnMenosExcluir', function(){
        $('input.ppcadastrar, input.pecadastrar-td, input.ppexcluir, input.peexcluir-td , input.ppvisualizar, input.pevisualizar-td, input.ppeditar, input.peeditar-td, input.ppimportar, input.peimportar-td').each(function(){
            if($(this).is(':disabled')){
                $(this).removeAttr('disabled');
            }
            if(!$(this).hasClass('ppexcluir')){
                $(this).prop('checked', true);
            }else{
                $(this).prop('checked', false);
                $('.peexcluir-td').prop('checked', false);
            }
        });
    });

    $(document).on('click','.pevisualizar-td', function(){
        var thad = $(this);
        var cat = $(this).data('cat');
        console.log('asdf')
        $('.ppvisualizar-'+cat).each(function(){
            if($(thad).prop('checked')){$(this).prop('checked',true)
            }else{$(this).prop('checked',false)}
        });
        if($(thad).is(':checked')){
            $('.ppcadastrar-'+cat).removeAttr('disabled');
            $('.ppcadastrar-'+cat).removeAttr('disabled');
            $('.pecadastrar-'+cat).removeAttr('disabled');
            $('.pecadastrar-'+cat).removeAttr('disabled');
            $('.ppeditar-'+cat).removeAttr('disabled');
            $('.peeditar-'+cat).removeAttr('disabled');
            $('.ppexcluir-'+cat).removeAttr('disabled');
            $('.peexcluir-'+cat).removeAttr('disabled');
            $('.ppimportar-'+cat).removeAttr('disabled');
        }else{
            $('.ppcadastrar-'+cat).attr('disabled','disabled');
            $('.ppcadastrar-'+cat).attr('disabled','disabled');
            $('.pecadastrar-'+cat).attr('disabled','disabled');
            $('.pecadastrar-'+cat).attr('disabled','disabled');
            $('.ppeditar-'+cat).attr('disabled','disabled');
            $('.peeditar-'+cat).attr('disabled','disabled');
            $('.peeditar-'+cat).prop('checked', false);
            $('.ppcadastrar-'+cat).prop('checked', false);
            $('.ppeditar-'+cat).prop('checked', false);
            $('.ppexcluir-'+cat).attr('disabled','disabled');
            $('.peexcluir-'+cat).attr('disabled','disabled');
            $('.peexcluir-'+cat).prop('checked', false);
            $('.ppexcluir-'+cat).prop('checked', false);
            $('.ppimportar-'+cat).attr('disabled','disabled');
            $('.ppimportar-'+cat).attr('disabled','disabled');
            $('.ppimportar-'+cat).prop('checked', false);
            $('.ppimportar-'+cat).prop('checked', false);
        }
    });

    $(document).on('click','.ppvisualizar', function(){
        var cat = $(this).data('cat');
        if(!$(this).prop('checked')){
            $('.pevisualizar-'+cat).prop('checked', false);
        }else if($('input:checkbox.ppvisualizar-'+cat).filter(':checked').length == $('input:checkbox.ppvisualizar-'+cat).length){
            $('.pevisualizar-'+cat).prop('checked', true);
        }
    });

    $(document).on('click','.pecadastrar-td', function(){
        var thad = $(this);
        var cat = $(this).data('cat');
        $('.ppcadastrar-'+cat).each(function(){
            if($(thad).prop('checked')){ $(this).prop('checked',true)
            }else{ $(this).prop('checked',false) }
        });
    });
    $(document).on('click','.ppcadastrar', function(){
        var cat = $(this).data('cat');
        if(!$(this).prop('checked')){
            $('.pecadastrar-'+cat).prop('checked', false);
        }else if($('input:checkbox.ppcadastrar-'+cat).filter(':checked').length == $('input:checkbox.ppcadastrar-'+cat).length){
            $('.pecadastrar-'+cat).prop('checked', true);
        }
    })

    $(document).on('click','.peeditar-td', function(){
        var thad = $(this);
        var cat = $(this).data('cat');
        $('.ppeditar-'+cat).each(function(){
            if($(thad).prop('checked') && !$(this).is(':disabled')){
                $(this).prop('checked',true)
            }else{
                $(this).prop('checked',false)
            }
        });
    });
    $(document).on('click','.ppeditar', function(){
        var cat = $(this).data('cat');
        if(!$(this).prop('checked')){
            $('.peeditar-'+cat).prop('checked', false);
        }else if($('input:checkbox.ppeditar-'+cat).filter(':checked').length == $('input:checkbox.ppeditar-'+cat).length){
            $('.peeditar-'+cat).prop('checked', true);
        }
    })

    $(document).on('click','.peexcluir-td', function(){
        var thad = $(this);
        var cat = $(this).data('cat');
        $('.ppexcluir-'+cat).each(function(){
            if($(thad).prop('checked') && !$(this).is(':disabled')){ $(this).prop('checked',true)
            }else{ $(this).prop('checked',false) }
        });
    });
    $(document).on('click','.ppexcluir', function(){
        var cat = $(this).data('cat');
        if(!$(this).prop('checked')){
            $('.peexcluir-'+cat).prop('checked', false);
        }else if($('input:checkbox.ppexcluir-'+cat).filter(':checked').length == $('input:checkbox.ppexcluir-'+cat).length){
            $('.peexcluir-'+cat).prop('checked', true);
        }
    })

    $(document).on('click','.peimportar-td', function(){
        var thad = $(this);
        var cat = $(this).data('cat');
        $('.ppimportar-'+cat).each(function(){
            if($(thad).prop('checked')){ $(this).prop('checked',true)
            }else{ $(this).prop('checked',false) }
        });
    });
    $(document).on('click','.ppimportar', function(){
        var cat = $(this).data('cat');
        if(!$(this).prop('checked')){
            $('.peimportar-'+cat).prop('checked', false);
        }else if($('input:checkbox.ppimportar-'+cat).filter(':checked').length == $('input:checkbox.ppimportar-'+cat).length){
            $('.peimportar-'+cat).prop('checked', true);
        }
    })

    $(document).on('change','input:checkbox',function(){
        var visualisar = false;
        var cadastrar = false;
        var editar = false;
        var excluir = false;
        var importar = false;

        $('input:checkbox.pevisualizar-td').each(function(){
            if($(this).is(':checked') && $('input:checkbox.pevisualizar-td').length == $('input:checkbox.pevisualizar-td').filter(':checked').length){
                 visualisar = true;
            }
        });
        $('input:checkbox.pecadastrar-td').each(function(){
            if($(this).is(':checked') && $('input:checkbox.pecadastrar-td').length == $('input:checkbox.pecadastrar-td').filter(':checked').length){
                cadastrar = true;
            }
        });
        $('input:checkbox.peeditar-td').each(function(){
            if($(this).is(':checked') && $('input:checkbox.peeditar-td').length == $('input:checkbox.peeditar-td').filter(':checked').length){
                editar = true;
            }
        });
        $('input:checkbox.peexcluir-td').each(function(){
            if($(this).is(':checked') && $('input:checkbox.peexcluir-td').length == $('input:checkbox.peexcluir-td').filter(':checked').length){
                excluir = true;
            }
        });
        $('input:checkbox.peimportar-td').each(function(){
            if($(this).is(':checked') && $('input:checkbox.peimportar-td').length == $('input:checkbox.peimportar-td').filter(':checked').length){
                importar = true;
            }
        });

        var c_visualisar = $('input:checkbox.ppevisualizar').filter(':checked').length;
        var c_cadastrar = $('input:checkbox.ppcadastrar').filter(':checked').length;
        var c_editar = $('input:checkbox.ppeditar').filter(':checked').length;
        var c_excluir = $('input:checkbox.ppexcluir').filter(':checked').length;
        var c_importar = $('input:checkbox.ppimportar').filter(':checked').length;

        if(visualisar && cadastrar && editar && excluir && importar){
            // $("#btAcessoTotal").trigger("click");
            $(".btn-permissoes").addClass("btn-default").removeClass("btn-info");
            $("#btAcessoTotal").addClass("btn-info").removeClass("btn-default");
            $("#btAcessoTotal").html('Acesso total <span class="fa fa-check">');
            $('#btnApenasVisualizar').html('Apenas visualizar');
            $('#btnMenosExcluir').html('Menos excluir');
        }else if(!excluir && visualisar && cadastrar && editar && importar && c_excluir == 0){
            // $("#btnMenosExcluir").trigger("click");
            $(".btn-permissoes").addClass("btn-default").removeClass("btn-info");
            $("#btnMenosExcluir").addClass("btn-info").removeClass("btn-default");
            $("#btnMenosExcluir").html('Menos Excluir <span class="fa fa-check">');
            $('#btAcessoTotal').html('Acesso total');
            $('#btnApenasVisualizar').html('Apenas visualisar');
        }else if(visualisar && c_cadastrar == 0 && c_editar == 0 && c_excluir == 0 && c_importar == 0){
            // $("#btnApenasVisualizar").trigger("click");
            $(".btn-permissoes").addClass("btn-default").removeClass("btn-info");
            $("#btnApenasVisualizar").addClass("btn-info").removeClass("btn-default");
            $('#btnApenasVisualizar').html('Apenas visualizar <span class="fa fa-check">');
            $('#btAcessoTotal').html('Acesso total');
            $("#btnMenosExcluir").html('Menos excluir ');
        }else{
            $(".btn-permissoes").addClass("btn-default").removeClass("btn-info");
            $('#btAcessoTotal').addClass("btn-default").removeClass("btn-info").html('Acesso total');
            $('#btnMenosExcluir').addClass("btn-default").removeClass("btn-info").html('Menos excluir');
            $('#btnApenasVisualizar').addClass("btn-default").removeClass("btn-info").html('Apenas visualizar');
        }

    });

    $('#formPerfilAcesso').on('submit', function () {

        $('.salvar-witout-readonly').parent().prepend('<div class="fa-spinner-tmp"><span class="fa fa-spinner fa-spin fa-3x fa-fw margin-bottom"></span>Gravando</div>');
        $('.fa-spinner-tmp').css({'float': 'left', 'margin-right': '10px'});
        $('#perfilAcessoSave').css('display', 'none');

        var algum_selecionado = false;
        if($('.ppvisualizar:checked').length > 0 || $('.ppcadastrar:checked').length > 0 || $('.ppeditar:checked').length > 0 || $('.ppexcluir:checked').length > 0 || $('.ppimportar:checked').length > 0){
            algum_selecionado = true;
        }

        if(algum_selecionado && $('#idPedescricao').val().length >= 3 && ($('.pe-veiculos :selected').length > 0 || !$('#ckVeiculos').is(':checked'))){
            $(this).ajaxSubmit({
                // Exibindo resposta do servidor
                success: function (resposta) {
                    if(resposta == '200'){
                        history.go(-1);
                    }
                    removeLoad();
                },
                // Se acontecer algum erro
                error: function () {
                    removeLoad();
                }
            });
        }else{
            removeLoad();
            if(!algum_selecionado){
                $('#hasErrorSelecionados').removeClass("hidden");
            }
            if($('#idPedescricao').val() == '' &&  $('.pe-veiculos :selected').length <= 0){
                $('#hasErrorPedescricao').removeClass("hidden");
                if($('#ckVeiculos').is(':checked')){
                    $('#hasErrorPeveiculos').removeClass("hidden");
                }
            }else if($('.pe-veiculos :selected').length <= 0 && $('#ckVeiculos').is(':checked')){
                $('#hasErrorPeveiculos').removeClass("hidden");
            }else if(!$('#idPedescricao').val().length >= 3){
                $('#hasErrorPedescricao').removeClass("hidden");
            }
        }
        // Retorna FALSE para que o formulário não seja enviado de forma convencional
        return false;
    });

    function removeLoad(){
        $('#perfilAcessoSave').css('display', 'inline-block');
        $('.fa-spinner-tmp').remove();
    }

    $('#idPedescricao').on('change',function(){
        $('#hasErrorPedescricao').addClass("hidden");
    })

    $('#idPedescricao').on('input',function(){
        $('#hasErrorPedescricao').addClass("hidden");
        if($(this).val().length >= 3){
            $.post(ROOT+'/painel/cadastros/perfil/acesso/check/desc',{'empresa': $('.select-cliente-pe').val() ,'desc':$(this).val()}, function(dados){
                if(dados.status){
                    $('#hasErrorPedescricao').removeClass("hidden");
                }
            })
        }
    })


    $('.pe-veiculos').on('change',function(){
        if($('.pe-veiculos :selected').length > 0){
            $('#hasErrorPeveiculos').addClass("hidden");
        }
    });

    $(document).on('click','#ckVeiculos',function(){
        if($(this).is(':checked')){
            //show imputs;
            $('.grupos-veiculos , .pe-veiculos').prop('disabled',false);
        }else{
            $('.grupos-veiculos , .pe-veiculos').prop('disabled',true);
            //hidde imputs
        }
    });

    function tableListaPerfil(){
        var id = $(".cliente-usuario").val();
        $.post(ROOT+'/painel/cadastros/perfil/acesso/listar',
        {
            id:id,
            status:$('#status_perfil').val()
        },
        function(dados){
            var perfis = dados.perfis;
            var status = $('.bt-filtros-update.btn-primary').attr('data-val')
            var dataSet = [];

            // var ppeditar = $("#ppeditar").data('permissao');
            // var ppexcluir = $("#ppexcluir").data('permissao');

            for(i in perfis){
                // if(perfis[i].pestatus == status || status == 'T') {
                    var hid = perfis[i].pestatus  ? 'hidden' : ''
                    var hidden = perfis[i].pestatus ? '' : 'hidden'
                    var tr = ''
                    var data = []
                    data.push(perfis[i].pedescricao);
                    data.push(perfis[i].empresa.clnome);

                    // if(ppexcluir){
                        tr += '<a href="#" title="Desativar Perfil" data-head="Desativar" class="modalDesativa btn-tb btn btn-danger '+hidden+' desativar-cadastros" data-toggle="modal" data-target="#modalDesativa" data-class="save-perfil-acesso-status" data-url="'+ROOT+'/painel/cadastros/perfil/acesso/desativar/'+perfis[i].pecodigo+'">'
                            tr += '<span class="fa fa-ban"></span>'
                        tr += '</a>'
                        tr += '<a href="#" title="Ativar Perfil" data-url="'+ROOT+'/painel/cadastros/perfil/acesso/ativar/'+perfis[i].pecodigo+'" data-id="'+perfis[i].pecodigo+'" class="btn btn-tb '+hid+' ativar-cadastros  btn-success save-perfil-acesso-status">'
                            tr += '<span class="fa fa-check"></span>'
                        tr += '</a>'
                    // }
                    // if(ppeditar){
                        tr += '<a title="Editar Perfil" class="btn btn-info" href="'+ROOT+'/painel/cadastros/perfil/acesso/editar/'+perfis[i].pecodigo+'">'
                            tr += '<span class="fa fa-pencil"></span>'
                        tr += '</a>'
                    // }
                    data.push(tr)
                    dataSet.push(data)
                // }
            }
            if ($.fn.DataTable.isDataTable('#tablePerfisAcesso')){
                $('#tablePerfisAcesso').DataTable().destroy();
            }
            table =  $('#tablePerfisAcesso').DataTable({
                paging: false,
                retrieve: true,
                language: traducao,
                dom: 'Bfrtip',
                buttons:
                    [{
                        extend: 'pdf',
                        message: 'Data da emissão '+moment().format('L')+'',
                        className: 'btn btn-lg  btn-default exportar',
                        text: 'PDF',
                        pageSize: 'A4',
                        orientation: 'portrait',
                        exportOptions: { columns: [0,1] },
                        customize: function (doc) {
                            console.log(doc);
                            doc.defaultStyle.alignment = 'center';
                            doc.styles.tableHeader.alignment = 'center';
                            doc.content[2].table.widths =
                            Array(doc.content[2].table.body[0].length + 1).join('*').split('');
                       }
                    },{
                       extend: 'excel',
                       footer: false,
                       className: 'btn btn-lg btn-default exportar',
                       exportOptions: { columns: [0,1]
                       }
                   },{
                       extend: 'csv',
                       footer: false,
                       className: 'btn btn-lg btn-default exportar',
                       exportOptions: { columns: [0,1] }
                   },{
                       extend: 'print',
                       text: 'Imprimir',
                       footer: false,
                       className: 'btn btn-lg btn-default exportar',
                       exportOptions: { columns: [0,1] }
                   }],
                data: dataSet,
                initComplete: function () {
                    $('.exportar').removeClass("dt-button buttons-pdf buttons-csv buttons-excel buttons-html5");
                    $('.exportar').parent().addClass('cabecalho-exportacoes');
                    $('.exportar').prepend("<span class='fa fa-save'></span>");
                }
            });
        })
    }


    $(".fl_perfil_st").on("click",function(){
        if($(this).attr('id') == "at_perfil"){
            $("#status_perfil").val("A");
            $(".fl_perfil_st").addClass("btn-default").removeClass("btn-primary");
            $(this).addClass("btn-primary").removeClass("btn-default");
        }else if($(this).attr('id') == "in_perfil"){
            $("#status_perfil").val("I");
            $(".fl_perfil_st").addClass("btn-default").removeClass("btn-primary");
            $(this).addClass("btn-primary").removeClass("btn-default");
        }else{
            $("#status_perfil").val("T");
            $(".fl_perfil_st").addClass("btn-default").removeClass("btn-primary");
            $(this).addClass("btn-primary").removeClass("btn-default");
        }
        tableListaPerfil();
    });


    $(document).on('click','.save-perfil-acesso-status',function(){
        setTimeout(function (){
            tableListaPerfil();
        }, 600);
    });

});
