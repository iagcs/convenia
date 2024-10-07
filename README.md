# Sistema de Gestão de Funcionários

Este projeto tem como objetivo auxiliar gestores na administração de seus funcionários por meio de uma interface que oferece funcionalidades básicas de CRUD (Create, Read, Update,
Delete) para os dados dos colaboradores. Além das operações de CRUD, o sistema permite a importação em massa de funcionários, facilitando a adição de múltiplos registros de uma
vez. Essa importação em massa pode ser realizada através de uma rota específica que processa, valida e importa um arquivo de entrada.

O sistema oferece um conjunto de rotas para realizar o gerenciamento dos funcionários, permitindo criar, editar, visualizar e deletar registros individuais.

Além do CRUD, há uma rota dedicada para a importação em massa de funcionários. Essa rota processa arquivos CSV ou Excel e realiza a inserção dos registros de maneira assíncrona,
utilizando os conceitos de Job Batching oferecidos pelo Laravel, o que permite o processamento eficiente de grandes volumes de dados. A validação de cada linha do arquivo é
realizada durante esse processo, e o resultado é notificado ao usuário tanto em caso de sucesso (da validação e importação) quanto em situações onde ocorram erros de validação.

Para otimizar o desempenho e reduzir o custo das operações, o sistema utiliza manipulação de cache para armazenar temporariamente os erros encontrados durante o processo de
validação. Isso foi implementado porque o acesso ao cache é consideravelmente mais rápido em comparação com o banco de dados tradicional, além de ter um custo computacional mais
baixo. Essa abordagem melhora a eficiência ao lidar com operações assíncronas, permitindo que os erros sejam rapidamente recuperados e exibidos ao usuário sem comprometer o
desempenho do sistema como um todo.

## Pré-requisitos

- [PHP](https://www.php.net/downloads) >= 8.3
- [Composer](https://getcomposer.org/)
- [Redis](https://redis.io/download)

## Configuração

1. Clone o repositório da aplicação Laravel:

   ```bash
   git clone https://github.com/iagcs/convenia.git

2. Navegue até o diretório da aplicação:

   ```bash
   cd convenia

3. Crie um arquivo .env na raiz do diretório da aplicação, baseando-se no arquivo .env.example. Você pode usar o comando cp no Unix/Linux ou copy no Windows:

   ```bash
   cp .env.example .env

4. Edite o arquivo .env com as configurações de banco de dados e outras configurações específicas da sua aplicação, se necessário.

### Banco de dados

- Certifique-se de que você tem um banco de dados configurado e ajuste as variáveis de ambiente no arquivo .env para refletir sua configuração local.

### Fila

- Para a execução das filas de mensagens, o Redis é usado como o serviço de mensageria. Para configurar, siga os passos abaixo:

1. Certifique-se de que o Redis está instalado e rodando em sua máquina local. Você pode conferir como fazer isso aqui.

2. Configure as variáveis de ambiente no arquivo .env para utilizar o Redis como driver de filas:

    ```dotenv
    QUEUE_CONNECTION=redis

    REDIS_HOST=127.0.0.1
    REDIS_PASSWORD=null
    REDIS_PORT=6379
   ```

### Cache

- O sistema utiliza o cache para otimizar algumas operações durante o processo de importação, garantindo maior eficiência e desempenho. É necessário que o cache esteja corretamente configurado para o funcionamento adequado dessas operações.

1. Certifique-se de que o Redis está instalado e rodando em sua máquina local.

2. Configure as variáveis de ambiente no arquivo .env para utilizar o Redis como sistema 
de cache distribuido:

    ```dotenv
    QUEUE_CONNECTION=redis

    CACHE_STORE=redis
    CACHE_DRIVER=redis
    

## Execução

1. Para rodar o servidor local da aplicação Laravel, utilize o comando:

   ```bash
   php artisan serve

2. Após iniciar o servidor, você pode acessar a aplicação em seu navegador através do endereço:

    ```bash
   http://localhost:8000


3. Para popular o banco:

    ```bash
    php artisan db:seed

4. Para executar a fila:

    ```bash
    php artisan queue:listen redis

5. Para executar os testes da aplicacao:
    ```bash
    php artisan test
    ```

5. Para gerar o relatorio da cobertura de testes no codigo (deve ter o Xdebug configurado):
    ```bash
    ./vendor/bin/pest --coverage-clover report.xml
    ```
   
## Relatório de Cobertura de Testes

O projeto inclui um relatório de cobertura de testes gerado com o Pest, uma ferramenta de testes para PHP que facilita a escrita de testes legíveis e eficazes. Para obter uma análise detalhada da cobertura, foi utilizado o Xdebug, que permite rastrear quais partes do código foram cobertas pelos testes.

O relatório de cobertura já foi gerado e está disponível na raiz do projeto sob o nome report.xml. Este arquivo contém informações detalhadas sobre a execução dos testes, incluindo quais linhas de código foram testadas e quais não foram
    

# Documentação da API

## Rotas de Autenticacao do Usuario/Gestor

### 1. Logar usuario

A rota `/login` é usada para logar um usuario/gestor.

#### Método

`POST`

#### Endpoint

`/login`

#### Corpo da Requisição

| Parâmetro | Tipo   | Descrição        |
|-----------|--------|------------------|
| email     | string | Email do usuario |
| password  | string | Senha do usuario |

#### Exemplo de Corpo da Requisição

```json
{
    "email": "gustavo@silva.com",
    "password": "qweqwe"
}
```

## Resposta

#### Exemplo de Resposta da Requisição

```json
{
    "id": "9d2f62d0-1a03-4533-928c-1f8a4b5a8e13",
    "name": "Gustavo Silva",
    "email": "gustavo@silva.com",
    "access_token": "3|PHqHkp2zHQcz6owUwKnn5jJzdyTbtxGlHMhz23jD",
    "expires_in": -259199.996278,
    "created_at": "2024-10-07T02:34:28+00:00",
    "updated_at": "2024-10-07T02:34:28+00:00"
}
```

### Códigos de Resposta

| Código | Descrição                      |
|--------|--------------------------------|
| 201    | Token gerado com sucesso       |
| 400    | Erro na validação dos dados da |

## Regras de Validação

| Campo    | Regras                                                  |
|----------|---------------------------------------------------------|
| email    | string, obrigatoria, deve existir na tabela de usuarios |
| password | string, obrigatoria                                     |

## Rotas de CRUD do Employee

### 1. Criar um Employee

A rota `/employees` é usada para criar um novo funcionário.

#### Método

`POST`

#### Endpoint

`/employees`

#### Corpo da Requisição

| Parâmetro | Tipo   | Descrição                        |
|-----------|--------|----------------------------------|
| name      | string | Nome completo do funcionário     |
| email     | string | Email do funcionário             |
| cpf       | string | CPF do funcionário               |
| city      | string | Cidade onde o funcionário reside |
| state     | string | Estado onde o funcionário reside |

#### Exemplo de Corpo da Requisição

```json
{
    "name": "João Silva",
    "email": "joao.silva@email.com",
    "cpf": "12345678900",
    "city": "São Paulo",
    "state": "SP"
}
```

## Resposta

#### Exemplo de Resposta da Requisição

```json
{
    "id": "be427de1-4a39-3de8-a262-04de4835ff67",
    "name": "João Silva",
    "email": "joao.silva@email.com",
    "cpf": "12345678900",
    "city": "São Paulo",
    "state": "SP"
}
```

### Códigos de Resposta

| Código | Descrição                      |
|--------|--------------------------------|
| 201    | Funcionário criado com sucesso |
| 400    | Erro na validação dos dados da |

## Regras de Validação

| Campo | Regras                                                                     |
|-------|----------------------------------------------------------------------------|
| name  | string, obrigatoria                                                        |
| email | string, obrigatoria, deve ser diferente de qualquer uma no banco de dados. |
| cpf   | string, obrigatoria                                                        |
| city  | string, obrigatoria                                                        |
| state | string, obrigatoria                                                        |

### 2. Editar um Employee

A rota `/employees` é usada para editar um funcionário.

#### Método

`Put`

#### Endpoint

`/employees/{employeeId}`

#### Corpo da Requisição

| Parâmetro | Tipo   | Descrição                        |
|-----------|--------|----------------------------------|
| name      | string | Nome completo do funcionário     |
| email     | string | Email do funcionário             |
| cpf       | string | CPF do funcionário               |
| city      | string | Cidade onde o funcionário reside |
| state     | string | Estado onde o funcionário reside |

#### Exemplo de Corpo da Requisição

```json
{
    "name": "João Pereira",
    "cpf": "09876543221"
}
```

## Resposta

```json
{
    "id": "be427de1-4a39-3de8-a262-04de4835ff67",
    "name": "João Pereira",
    "email": "joao.silva@email.com",
    "cpf": "09876543221",
    "city": "São Paulo",
    "state": "SP"
}
```

### Códigos de Resposta

| Código | Descrição                       |
|--------|---------------------------------|
| 201    | Funcionário editado com sucesso |
| 400    | Erro na validação dos dados     |

## Regras de Validação

| Campo | Regras                                                        |
|-------|---------------------------------------------------------------|
| name  | string                                                        |
| email | string, deve ser diferente de qualquer uma no banco de dados. |
| cpf   | string                                                        |
| city  | string                                                        |
| state | string                                                        |

### 3. Ver um Employee

A rota `/employees/{employeeId}` é usada para ver um novo funcionário.

#### Método

`Get`

#### Endpoint

`/employees`

## Resposta

#### Exemplo de Resposta da Requisição

```json
{
    "id": "be427de1-4a39-3de8-a262-04de4835ff67",
    "name": "João Silva",
    "email": "joao.silva@email.com",
    "cpf": "12345678900",
    "city": "São Paulo",
    "state": "SP"
}
```

### Códigos de Resposta

| Código | Descrição                         |
|--------|-----------------------------------|
| 200    | Funcionário retornado com sucesso |

### 4. Deletar um Employee

A rota `/employees/{employeeId}` é usada para deletar um novo funcionário.

#### Método

`Delete`

#### Endpoint

`/employees/{employeeId}`

## Resposta

### Códigos de Resposta

| Código | Descrição                        |
|--------|----------------------------------|
| 204    | Funcionário deletado com sucesso |

### 5. Ver todos os Employees

A rota `/employees` é usada para ver todos os funcionários do gestor.

#### Método

`Get`

#### Endpoint

`/employees`

## Resposta

#### Exemplo de Resposta da Requisição

```json
[
    {
        "id": "9d2edd9b-7e53-4cc8-9653-a8bee6c7626d",
        "user_id": "9d2edd9b-7555-47df-b4fd-7b95c5faf81d",
        "name": "Miss Asa Reinger",
        "email": "carmine99@hotmail.com",
        "cpf": "358",
        "city": "Wuckertborough",
        "state": "chester"
    },
    {
        "id": "9d2edd9b-7eaa-4df0-8981-409758385ae5",
        "user_id": "9d2edd9b-7555-47df-b4fd-7b95c5faf81d",
        "name": "Jailyn Rolfson I",
        "email": "kautzer.kevon@hotmail.com",
        "cpf": "499",
        "city": "East Merlehaven",
        "state": "stad"
    },
    {
        "id": "9d2edd9b-7ef8-4555-92da-2226b435da5d",
        "user_id": "9d2edd9b-7555-47df-b4fd-7b95c5faf81d",
        "name": "Jaeden Schroeder",
        "email": "rosalinda.huels@runte.com",
        "cpf": "175",
        "city": "Hillschester",
        "state": "mouth"
    }
]
```

### Códigos de Resposta

| Código | Descrição                    |
|--------|------------------------------|
| 200    | Retorno dos funcionarios ok. |

### 6. Importar Funcionarios

A rota `/employees/import` permite a importação em massa de funcionários. Como esse processo é executado de forma assíncrona, o usuário será notificado por e-mail em três
situações: se ocorrerem erros de validação no arquivo, se houver erros internos durante o processo (status 500), e quando a importação for concluída com sucesso. Apenas a validação
dos cabeçalhos do arquivo é realizada de forma síncrona, ou seja, imediatamente durante a requisição.

#### Método

`Post`

#### Endpoint

`/employees/import`

## Resposta

### Códigos de Resposta

| Código | Descrição                                 |
|--------|-------------------------------------------|
| 200    | Importacao iniciada com sucesso.          |
| 400    | Erro na validacao do cabecalho do arquivo |


