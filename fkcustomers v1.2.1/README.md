# FokusFirst

### Disponibiliza campos adicionais ao cadastro de clientes, automatiza entrada de dados e valida os campos preenchidos.

É possível configurar o FKcustomers para trabalhar em dois modos de operação:

* Modo Completo: destinado a lojas que utilizam o tema padrão do Prestashop ou temas que não alterem o padrão dos arquivos .tpl originais.
* Modo Compatibilidade: para lojas que possuam temas diferentes do padrão Prestashop, ou onde o modo completo não funcione corretamente.
Principais características conforme o modo de operação:

## Completo
* Utiliza arquivos .tpl customizados em substituição aos do tema. São eles: address.tpl, authentication.tpl, identity.tpl, order-opc.tpl e order-opc-new-account.tpl.
* Adiciona os campos CPF/CNPJ, RG/IE, Número e Complemento do endereço.
* Verifica validade e preenchimento de CPF e CNPJ.
* Permite verificar duplicidade de CPF e CNPJ.
* Permite verificar o preenchimento do RG e IE.
* Verifica o preenchimento do campo Número do endereço.
* Valida o DDD dos campos Telefone e Celular.
* Faz a pesquisa online do CEP e preenche automaticamente os campos Endereço, Bairro, Cidade e Estado.
* Quando o cliente for Pessoa Jurídica, é possível direcioná-lo para um Grupo de Clientes específico.
* Permite ao cliente alterar os dados de CPF/CNPJ e RG/IE.
* Todos os recursos descritos anteriormente também estão disponíveis na Área Administrativa.
* Os novos campos criados podem ser visualizados na Área Administrativa em Cadastro de Clientes, Cadastro de Endereços e Detalhes do Pedido e também na Fatura.
* Trabalha em conjunto com o FKpagseguro evitando entrada em duplicidade de dados pelos clientes.
* Verifica as configurações do PHP e do Prestashop que podem gerar mau funcionamento do módulo e informa as ações a serem adotadas.

## Compatibilidade
* Mantém os .tpl originais do tema.
* Adiciona os campos CPF/CNPJ e RG/IE.
* Neste modo, o número deve ser digitado no mesmo campo do endereço. Quando o preenchimento do número não for detectado, uma mensagem solicitando confirmação será emitida.
* Verifica validade e preenchimento de CPF e CNPJ.
* Permite verificar duplicidade de CPF e CNPJ.
* Permite verificar o preenchimento do RG e IE.
* Valida o DDD dos campos Telefone e Celular.
* Faz a pesquisa online do CEP e preenche automaticamente os campos Endereço, Bairro, Cidade e Estado.
* Quando o cliente for Pessoa Jurídica, é possível direcioná-lo para um Grupo de Clientes específico.
* Todos os recursos descritos anteriormente também estão disponíveis na Área Administrativa.
* Os novos campos criados podem ser visualizados na Área Administrativa em Cadastro de Clientes, Cadastro de Endereços e Detalhes do Pedido e também na Fatura.
* Trabalha em conjunto com o FKpagseguro evitando entrada em duplicidade de dados pelos clientes.
* Verifica as configurações do PHP e do Prestashop que podem gerar mau funcionamento do módulo e informa as ações a serem adotadas.

## Importante: Considerações sobre compatibilidade
Quando utilizado o Modo Completo, garantimos a compatibilidade do módulo somente com o tema padrão do Prestashop ou com temas que não alterem o padrão dos arquivos .tpl originais.

Nos casos de incompatibilidade, fornecemos na documentação técnica os procedimentos necessários para alteração dos .tpl envolvidos para que o módulo funcione com temas não padrões. Para efetuar tais procedimentos é necessário conhecimento em html e smarty.
<p align="center">
<strong>DETALHES DO PRODUTO</strong> <br>
Versão 1.2.1 <br>
Plataforma PrestaShop <br>
Compatibilidade v1.6.0.1 à v1.6.1.24 <br>
Requisito do Servidor SOAP, fopen e output_buffering Habilitados no PHP <br>
</p> 
