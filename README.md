# Teste de Backend: API de Gerenciamento de Notas Fiscais

Este foi um teste realizado a partir de um processo seletivo. O teste consiste na construção de um pequeno backend
(servindo como API) para cadastro de notas fiscais com disparo de email após sua criação (através das queues).

## Enunciado

Desenvolver uma api Rest para controle das notas fiscais dos usuários:

1. Criar endpoints para cadastro e login dos usuários;
2. Criar CRUD para o gerenciamento das notas fiscais:
    - As api’s das notas fiscais só podem ser acessadas por usuários autenticados;
    - Cada nota só pode ser acessada pelo usuário que a criou;
    - A cada nota fiscal criada, disparar um email para o usuário que a criou.
3. Fazer devidos retornos de response e HTTP status code;
4. Criar um projeto público no github com a solução do desafio.

### Tecnologia Exigida

- PHP 8+;
- Banco de Dados: MySQL ou Mongodb;
- Framework: Laravel 10+;
- Pontos extras:
    - Utilizar docker;
    - Rodar pipeline no [Github Actions](https://github.com/gabrielmath/invoice-manager/actions) para execução dos
      testes.

(Se clicar no link acima, irá diretamente para a página das pipelines que foram rodadas)

### Campos solicitados

<table>
<thead>
<tr>
    <th>Campo (* obrig.)</th>
    <th>Tipo</th>
    <th>Descrição</th>
</tr>
</thead>
<tbody>
<tr>
<td>numero*</td>
<td>char(9)</td>
<td>Código único da NF (com exatamente 9 caracteres)</td>
</tr>

<tr>
<td>data_emissao*</td>
<td>date</td>
<td>Data de emissão da NF</td>
</tr>

<tr>
<td>valor*</td>
<td>decimal(10,2)</td>
<td>Valor do frete (sempre maior que zero)</td>
</tr>

<tr>
<td>cnpj_remetente*</td>
<td>string(14)</td>
<td>CNPJ da empresa remetente - Poderá enviá-lo com ou sem a máscara</td>
</tr>

<tr>
<td>nome_remetente*</td>
<td>string(100)</td>
<td>Nome da empresa remetente</td>
</tr>

<tr>
<td>cnpj_transportador*</td>
<td>string(14)</td>
<td>
CNPJ do transportador - Poderá enviá-lo com ou sem a máscara
</td>
</tr>

<tr>
<td>nome_transportador*</td>
<td>string(100)</td>
<td>Nome da empresa de transporte</td>
</tr>
</tbody>
</table>

## Ambiente Local Utilizado

No banco de dados resolvi utilizar o **MySQL** e para o processamento das filas, **Redis**. Ainda a respeito das filas,
como envolve disparo de emails, aproveitei do **Mailtip** já configurado no LaravelSail. Usei também o **NGINX** como
servidor HTTP e utilizei o **PHP em sua versão 8.2**. Como esperado, usei o **Docker a partir do LaravelSail**,
que já trás um ambiente robusto e completamente configurado.
Rodei isso no **WSL2 com Ubuntu** e o docker instalado nele, nativamente.

## Instalação

Após clonar o projeto em seu aparelho, basta rodar o comando do composer para instalar as
dependências:

```bash
composer install
```

OBS: caso não tenha o composer em sua máquina, recomendo utilizar seu [container
docker](https://hub.docker.com/_/composer) para instalar as dependências do projeto.

Após a instalação das dependências, para rodar os comandos dentro do container, utilizaremos o comando do sail:

```bash
./vendor/bin/sail <command>
```

Considere criar um `alias` para rodar apenas `sail` na linha de comando. Pesquise a melhor forma de fazer de acordo com
seu
sistema operacional.

Em seguida, faça uma cópia do arquivo `.env.example` para `.env` e configure seu ambiente de desenvolvimento como
banco de dados e etc.

E lembre-se também de gerar uma chave para aplicação:

```bash
sail artisan key:generate
```

Finalmente, para rodar a aplicação:

```bash
sail up -d
```

Daqui pra frente irei considerar como domínio principal o `http://localhost`.

## Resumo da construção da aplicação/API

### Documentação da API

Este é o link da [documentação do Postman](https://documenter.getpostman.com/view/9341288/2s9YeHZAqr)

### Filas (Queues)

Antes de iniciarmos o banco de dados, sugiro deixar a fila configurada e rodando para acompanhar seus disparos.

Deixe também o servidor local de email (**Mailpit** já configurado nos containers docker) aberto em alguma aba de seu
navegador. Siga as instruções abaixo:

1. Accesse seu servidor de email;
    - Caso não tenha nenhuma configuração disponível, recomendo o uso do `Mailtip` que já vem setado por padrão no
      LaravelSail. Basta subir os containers (como mencionado anteriormente) e acessar a url
      [`http://localhost:8025`](http://localhost:8025) (ou mude a porta de acesso caso tenha outra configuração).
2. Com o terminal aberto e dentro do diretório do projeto, basta usar o comando:

```bash
sail artisan queue:work
```

Se o projeto estiver configurado corretamente e não tiver conflito com portas, a fila estará pronta para ser executada.

### Laravel Horizon

Se quiser uma interface visual para acompanhar a execução das filas, poderá instalar o horizon. Na verdade ele já está
instalado.

1. Caso tenha executado as _queues_ anteriormente, finalize o processo com `Ctrl+C`;

2. Instale o Horizon:

```bash
sail artisan horizon:install
```

3. Publique seus arquivos de configuração:

```bash
sail artisan horizon:publish
```

4. Acesse a rota ``http://localhost/horizon``;
5. Execute as queues a partir do horizon:

```bash
sail artisan horizon
```

Verá na tela a execução das filas e um conjunto de relatórios e métricas a respeito das mesmas.

### Banco de Dados

No banco de dados, além das tabelas padrão que o laravel já traz (incluindo usuário), criei apenas 1 tabela: _invoices_.
Nela, inseri os dados solicitados no teste e fiz o relacionamento com a tabela de usuários.

Após criar sua base de dados local e configura-la em seu `.env`, execute o comando:

```bash
sail artisan migrate --seed
```

Caso tenha populado o banco de dados antes de rodar as queues e não tenha visto a enxurrada de emails, basta executar:

```bash
sail artisan migrate:fresh --seed
```

Dessa forma você verá os emails de notificação serem enviados em grande quantidade.

### Detalhes do Projeto

1. Tratamento dos dados com FormRequest (`InvoiceRequest`):
    - Para validar os CNPJ, criei uma `Rule` chamada `DocumentRule` com uma validação espefícica. Independente disso,
      o usuário poderá envia os CNPJ com ou sem máscara. O importante é que este seja válido;
    - Mantive os campos em inglês na base de dados, mas dei liberdade para o usuário mandar seus dados em
      português, tratando a request ante de sua validação.
2. Exibição dos dados em Json através das Resources:
    - Como mencionado anteriormente, estou exibindo os dados em português por ter sido solicitado no enunciado,
      mas internamente os campos estão com o nome em inglês. Utilizei também `Attributes` e `Mutations` para validar os
      dados na saída (ambos CNPJ e exibição do valor monetário da NF).
3. Criação de `Observer` para a criação de uma nova nota fiscal (`InvoiceObserver`);
    - `InvoiceObserver::creating`: De todos os dados necessários para a Nota Fiscal ser criada, observei que 2 deles não
      deveriam estar no controle do usuário: `numero` e `data_emissao`.
      Logo, após todo o processo de validação e cadastro, eu crio de forma automática
      o número/código da NF e atribuo a data atual como a data de emissão do documento;
    - `InvoiceObserver::created`: Após o cadastro de uma nova nota fiscal, este método executará um job para
      enviar uma notificação com os dados para seu dono/criador. O job é o `SendInvoiceJob.php`
4. Para não permitir que um usuário veja, edite ou exclua uma nota fiscal que não seja dele, criei o
   `InvoicePolicy` para barrar qualquer uma das ações citadas anteriormente;
5. CRUDs construídos usando a metodologia do TDD;
    - Para rodar os testes:

```bash
# Para rodar todos de uma vez
sail pest

# Para rodá-los separadamente
sail pest tests/Feature/Auth/RegisterTest.php
sail pest tests/Feature/Auth/AuthenticationTest.php
sail pest tests/Feature/InvoiceTest.php
```

6. Endpoints/rotas da API:

```php
  POST       api/auth/login ...................................................... Auth\AuthenticationController@login
  GET|HEAD   api/auth/logout .................................................... Auth\AuthenticationController@logout
  POST       api/auth/register ............................................................... Auth\RegisterController
  GET|HEAD   api/invoices ................................................... invoices.index › InvoiceController@index
  POST       api/invoices ................................................... invoices.store › InvoiceController@store
  GET|HEAD   api/invoices/{invoice} ........................................... invoices.show › InvoiceController@show
  DELETE     api/invoices/{invoice} ..................................... invoices.destroy › InvoiceController@destroy
```

7. Não realizei o `update` das Notas Fiscais justamente por serem Notas fiscais.

## Considerações Finais

Este foi um teste desafiador e também motivador. Espero ter conseguido passar um pouco da ideia que tive
pra solucionar o problema e como me organizei, como estruturei o código para deixá-lo melhor manutenível,
com fácil compreensão e rápida leitura.

Recomendo fortemente que dê ua olhadas nas minhas
[pipelines](https://github.com/gabrielmath/invoice-manager/actions), [issues](https://github.com/gabrielmath/invoice-manager/issues?q=is%3Aissue+project%3Agabrielmath%2F2+author%3A%40me+sort%3Acomments-desc+is%3Aclosed)
e no [kanban](https://github.com/users/gabrielmath/projects/2) que montei pra esse projeto vinculado as Pull Requests.
E, novamente, segue a [documentação da API no Postman](https://documenter.getpostman.com/view/9341288/2s9YeHZAqr).

### Obrigado!
