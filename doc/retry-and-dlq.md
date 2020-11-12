# Retry e DLQ

## Introdução

Em alguns casos é necessário ter um fluxo de tentativas ou separar as mensagens que falharam durante o processamento para 
serem analisadas ou reprocessadas de outra forma mais tarde, para esses casos a lib disponibiliza configurações de 
retry e DLQ.

Essas configurações são definidas no consumer através dos métodos `setAttempts` e `setDlq`

### Consumer::setAttempts

> **Parâmetros:**
> - $attempts *(int)*: A quantidade de vezes que o worker deve tentar processar a mensagem, por padrão esse valor é 1
> - $intervalMs *(int)*: O tempo em milisegundos que o worker deve esperar para devolver a mensagem para a fila para outra 
> tentativa, por padrão esse valor é 1000 (1 segundo).

#### Exemplo:

Consumer configurado para 3 tentativas e com intervalo de 2 segundos entre as tentativas.

```php
$this->brokerFactory
    ->createConsumer()
    ->setAttempts(3, 2000)
    ->consume("demo", new Processor());
```

As iterações são salvas no header `current_attempt` da mensagem, segue um exemplo de uma mensagem que tentou 
ser processada três vezes:

```json
{
   "body":{
      "_id":"5f57c16726aa7"
   },
   "properties":[
      
   ],
   "headers":{
      "contentType":"application\/json",
      "current_attempt":3
   }
}
```

### Consumer::setDlq

> **Parâmetro:**
> - $target *(string)*: O nome da fila/tópico que deseja enviar a mensagem se alguma excessão for lançada, por padrão esse valor é vazio `('')`

#### Exemplo

Consumer configurado para enviar a mensagem para o tópico **demo-DLQ** se alguma excessão for lançada durante o processamento 
da mensagem, como o método `setAttempts` não foi definido esse consumer vai tentar processar a mensagem apenas uma vez.

```php
$this->brokerFactory
    ->createConsumer()
    ->setDlq('demo-DLQ')
    ->consume("demo", new Processor());
```

As mensagens enviadas para a dlq tem o motivo da excessão salva no header **exception** da mensagem, segue um 
exemplo de uma mensagem que foi enviada para a dlq depois de ultrapassar o limite de 3 tentativa de processamento.

```json
{
   "body":{
      "_id":"5f57c2b4e5bdf"
   },
   "properties":[
      
   ],
   "headers":{
      "contentType":"application\/json",
      "current_attempt":3,
      "exception":"Algo deu errado :p"
   }
}
```

## Referências
- [Documento no espaço da Confluence](https://picpay.atlassian.net/wiki/spaces/JAVA/pages/804552715/Kafka#T%C3%B3picos-de-DLQ-(Dead-Letter-Queue):)
- [Construindo reprocessamento confiável e dead letter queues com Kafka](https://imasters.com.br/desenvolvimento/construindo-reprocessamento-confiavel-e-dead-letter-queues-com-kafka)