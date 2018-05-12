<div class="modal-header">
    <h3>Cadastro de chip</h3>
</div>
<div class="modal-body">
    <div id="formCadastro">
        <div class="divMenuModalCadastro">
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-primary">Novo</button>
                <button id="btnNovoCadastro" type="button" class="btn btn-success">Imprimir</button>
            </div>
            <div id="buscaTopoModal">
                <input class="form-control vazio busca-cadastros" data-func="funcCliente" data-url="/cadastros/clientes/buscar" type="text" autocomplete="false" placeholder="Busque um cliente">
            </div>
        </div>
        <form id="formCadastroCliente" method="POST" action="{{url('cadastros/clientes')}}" class="form-horizontal">
            {{ csrf_field() }}
            <div class="row">
                <div class="col-md-12">
                    <div class="col-md-3 block-chec-pes">
                        <label class="tipo-cliente">Tipo de cliente</label>
                        <div class="chec-tipo-cliente">
                            <input type="hidden" name="cltipo" value="P">
                            <span class="col-md-4 psa-fisica">Física</span>
                            <label class="col-md-4 switch">
                              <input id="inputTipoPessoa" type="checkbox" name="cltipo" value="J">
                              <div class="slider round"></div>
                            </label>
                            <span class="col-md-4 psa-juridica">Jurídica</span>
                        </div>
                    </div>
                    <div class="col-md-9 block-nome form-group">
                        <label>Nome*</label>
                        <input type="text" name="clnome" placeholder="Digite o nome" id="clnome" class="form-control vazio" value="{{old('clnome')}}">
                    </div>
                </div>
            </div>
            <div class="col-md-6 form-group block-cpf">
                <label>CPF*</label>
                <input type="text" placeholder="Digite nº do documento" name="cldocumento" id="cldocumento" class="form-control vazio cpf" value="{{old('cldocumento')}}">
            </div>
            <div class="col-md-6 form-group block-rg">
                <label>RG</label>
                <input type="text" name="cldocumento2" placeholder="Digite nº do documento" id="cldocumento2" class="form-control vazio">
            </div>
            <div class="blok-cod-cliente">
                <div class="row">
                    <div class="col-md-8 dados-cliente">
                        <div class="col-md-6 form-group">
                            <label>Logradouro</label>
                            <input type="text" placeholder="Digite o logradouro" name="cllogradouro" id="cllogradouro" class="form-control vazio">
                        </div>
                        <div class="col-md-2 form-group">
                            <label>Número</label>
                            <input type="text" name="clnumero" placeholder="Digite o número" id="clnumero" class="form-control vazio">
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Bairro</label>
                            <input type="text" placeholder="Digite o bairro" id="clbairro" name="clbairro" class="form-control vazio">
                        </div>
                        <div class="col-md-8 form-group">
                            <label>Complemento</label>
                            <input type="text" name="clcomplemento" id="clcomplemento" placeholder="Digite o complemento" class="form-control vazio">
                        </div>
                        <div class="col-md-4 busca form-group">
                            <label>Cidade-UF</label>
                                <input type="text" placeholder="Digite o nome" autocomplete="off" name="inputCidade" id="inputCidade" class="form-control cidades vazio">
                                <input type="hidden" name="clcidade" class="clcidade">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Segmento Cliente*</label>
                            <input type="text" name="clsigmento" placeholder="Digite o segmento" id="clsigmento" class="form-control vazio">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Logo</label>
                            <input type="file" name="cllogo" class="form-control vazio">
                        </div>
                    </div>
                    <div class="col-md-4 tel-mail">
                        <div class="tel-cliente">
                            <div class="col-md-1 group-mais-campo">
                                <a href="#" title="Adicionar telefone" data-mask="telefone" data-campo="clfone" data-parent="tel-cliente" class="mais-campo"><span class="glyphicon glyphicon-plus"></span></a>
                            </div>
                            <div class="col-md-10">
                                <label>Telefone</label>
                                <input type="text" name="clfone[1]" id="clfone" placeholder="Digite o telefone" class="form-control telefone vazio">
                            </div>
                        </div>
                        <div class="mail-cliente">
                            <div class="col-md-1 form-group group-mais-campo">
                                <a href="#" title="Aticionar e-mail" data-campo="clemail" data-mask="" data-parent="mail-cliente" class="mais-campo"><span class="glyphicon glyphicon-plus"></span></a>
                            </div>
                            <div class="col-md-10  form-group">
                                <label>E-mail</label>
                                <input type="text" name="clemail[1]" id="clemail" placeholder="Digite o email"  class="form-control vazio">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 block-ativo-cliente">
                <label class="tipo-cliente">Cliente</label>
                <div class="chec-tipo-cliente">
                    <input type="hidden" name="clstatus" value="I">
                    <span class="col-md-4 psa-fisica">Inativo</span>
                    <label class="col-md-4 switch">
                      <input type="checkbox" name="clstatus" checked value="A">
                      <div class="slider round"></div>
                    </label>
                    <span class="col-md-4 psa-juridica">Ativo</span>
                </div>
            </div>
            <div class="col-md-6">
                <a href="#" class="local-empresa btn btn-success">Selecione o local da empresa</a>
                <input type="hidden" class="inputLatitude" name="cllatitude">
                <input type="hidden" class="inputLongitude" name="cllongitude">
                <input type="hidden" class="inputRaio" name="clraio">
            </div>
            <div class="form-group block-salvar">
                <div class="col-md-12">
                    <button id="salvarCliente" type="submit" value="save" class="col-md-12 btn btn-primary">
                        Salvar
                    </button>
                </div>
            </div>
       </form>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-danger fechar-modal" data-dismiss="modal">Fechar</button>
</div>
@include('vendor.lrgt.ajax_script', ['form' => '#formCadastroCliente',
'request'=>'App/Http/Requests/ClientesRequest','on_start'=>false])
