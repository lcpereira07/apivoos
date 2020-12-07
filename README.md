# apivoos

# Descrição

    Api para consultar opções de voos organizados em grupos, de acordo com o tipo da tarifa e do valor da tarifa de ida e volta.

    O objetivo é realizar o agrupamento de voos de ida e volta e exibir as possibilidades dentro do mesmo tipo de tarifa.

# Recursos Utilizados

Laravel Framework 8.17.0;

PHP 7.4.12;

Xampp;

# Pré-requisitos

PHP 7.4 ou superior;

Composer instalado;

Servidor PHP rodando;

# Instruções de instalação

Para clonar o repositório executar o seguinte comando no terminal:

```git clone git@github.com:lcpereira07/apivoos.git```

A pasta vendor já está homologada, sendo assim não é necessário instalar nenhuma dependência

# Como utilizar

Após o término da configuração do ambiente, para consumir a API, basta realizar um GET no seguinte caminho:

http://localhost/apivoos/public/api/flights

Onde "http://localhost" é o path do servidor php

# Response

```{
	"flights": // retorne aqui os voos consultados na api em prova.123milhas.net
	"groups": [
		{
			"uniqueId": // id unico do grupo
			"totalPrice": // preço total do grupo
			"outbound": [ // voo(s) de ida
				{
					"id": "id do voo"
				},...
			]
			"inbound": [ // voo(s) de volta
				{
					"id": "id do voo
				},...
			]
		},...
	],
	"totalGroups": // quantidade total de grupos
	"totalFlights": // quantidade total de voos únicos
	"cheapestPrice": // preço do grupo mais barato
	"cheapestGroup": // id único do grupo mais barato
}```