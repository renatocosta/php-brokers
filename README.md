# Biblioteca para integração com brokers de mensageria, Apache Kafka e beanstalk
[![Codacy Badge](https://app.codacy.com/project/badge/Grade/a9b0a81f6c2144ad8acb6979fbdca867)](https://www.codacy.com?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=PicPay/picpay-prokers&amp;utm_campaign=Badge_Grade)

> Abstração do lib enqueue para facilitar uso de apache kafka em projetos php.

## Compatibilidade

- php >=7.1.*

## Dependências externas

- [Kafka Driver](https://github.com/edenhill/librdkafka)
- [Kafka PHP Extension](https://github.com/arnaud-lb/php-rdkafka)

## Instalação

Adicione o endereço do repositório do projeto no composer

```sh
composer config repositories.picpay-prokers vcs https://github.com/PicPay/picpay-prokers.git
```

Instale o pacote

```sh
composer require picpay/picpay-prokers
```

## Configuração

As configurações devem ser informadas em formato de array, é possível configurar mais que uma conexão e a troca entre as 
conexões pode ocorrer em momento de execução caso desejar.

Recomendamos a escolha de uma conexão padrão caso nenhuma conexão seja informada durante a execução.

As configurações para cada broker pode ser encontrado no site do enqueue na seção [transport](https://php-enqueue.github.io/transport)

> Um exemplo do arquivo de configurações pode sem encontrado em: 
>[./config/enqueue.php](https://github.com/PicPay/picpay-prokers/blob/master/config/enqueue.php)

## Exemplos

Abaixo segue o uso mais simples de um producer e consumer usando uma factory que abstrai todo o processo de construção 
dos mesmos.

```php
$config = require __DIR__ . '/config/enqueue.php';
$factory = new PicPay\Prokers\Factory\BrokerFactory($config);

$producer = $factory->createProducer();
$consumer = $factory->createConsumer();
```

#### Produzindo uma mensagem

```php
$producer->produce("topic_1", ["message" => "messate_test"]);
```

#### Consumindo uma mensagem

```php
<?php

namespace App\Worker;

use Interop\Queue\Context;
use Interop\Queue\Message;
use Interop\Queue\Processor;
use PicPay\Prokers\Contracts\ConsumerInterface;

class DumpWorker implements Processor
{
    /**
     * @var ConsumerInterface
     */
    private $consumer;
    
    // ...

    public function execute()
    {
        $this->consumer->consume('topic_1', $this);
    }

    /**
     * @inheritDoc
     */
    public function process(Message $message, Context $context)
    {
        $this->output->writeln($message->getBody());
        return self::ACK;
    }
}
```

### Payload da mensagem

>O padrão de payload foi definido para manter a compatibilidade com outras plataformas, possibilitando retro-compatibilidade e para uso com apache kafka possibilizar o uso do KSQL para fazer stream de dados em realtime. 

```json
{
   "headers":{
      "Content-Type":"application/json"
   },
   "body":{
       "uuid":"7d345ec4-620a-11ea-bc55-0242ac130003",
       "email":"adrianolaselva@gmail.com",
       "fullName":"Adriano M. La Selva",
       "shippingAddress":"Rua fernão dias, 1011",
       "quantity":2,
       "price":20.00,
       "paymentMethod":"CREDIT_CARD",
       "status":"PAYMENT_WAITING",
       "date":"2018-05-11",
       "card":{
          "uuid":"card_cj6cmcm4301z6696dt3wypskk",
          "date_created":"2017-08-14T20:35:46.036Z",
          "date_updated":"2017-08-14T20:35:46.524Z",
          "brand":"visa",
          "holder_name":"Morpheus Fishburne",
          "first_digits":"411111",
          "last_digits":"1111",
          "country":"UNITED STATES",
          "fingerprint":"3ace8040fba3f5c3a0690ea7964ea87d97123437",
          "valid":true,
          "expiration_date":"0922"
       },
       "items":[
          {
             "description":"Mouse se fio microsoft",
             "price":10.00
          },
          {
             "description":"fone se fio microsoft",
             "price":10.00
          }
       ]
   }
}
```

## Para saber mais

- [Configurações por tópico](./doc/config-per-topic.md)
- [Retry e DLQ](./doc/retry-and-dlq.md)
- [Extensões](./doc/extensions.md)
- [Troubleshoot](./doc/troubleshoot.md)
