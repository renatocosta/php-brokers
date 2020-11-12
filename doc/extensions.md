# Extensões

## Introdução

A lib do [enqueue](https://php-enqueue.github.io/) disponibiliza customizações através de extensões, com isso é possível criar métricas personalizadas, alterar 
o serializer, ignorar a execução do processor ou interromper o consumo de mensagem.

A lib do prokers já define algumas extensões por padrão:
- RetryAndDlqExtension: usado para permitir o fluxo de retry e dlq de forma simplificada.
- SignalExtension: permite o uso de Sinais do sistema operacional para interromper o consumo de mensagens.

Também temos a extensão NewRelicExtension que adiciona algumas métricas do processamento da mensagem na transação ativa, 
mas essa extensão não vem habilitada por padrão.

## Como adicionar uma extensão no Consumer

As extensões podem ser adicionadas pelo método `addExtension`, elas precisam implementar pelo menos uma das interfaces abaixo:
- Enqueue\Consumption\ExtensionInterface
- Enqueue\Consumption\StartExtensionInterface
- Enqueue\Consumption\PreSubscribeExtensionInterface
- Enqueue\Consumption\PreConsumeExtensionInterface
- Enqueue\Consumption\MessageReceivedExtensionInterface
- Enqueue\Consumption\PostMessageReceivedExtensionInterface
- Enqueue\Consumption\MessageResultExtensionInterface
- Enqueue\Consumption\ProcessorExceptionExtensionInterface
- Enqueue\Consumption\PostConsumeExtensionInterface
- Enqueue\Consumption\EndExtensionInterface
- Enqueue\Consumption\InitLoggerExtensionInterface

Cada interface oferece um **hook** onde é possível inserir uma customização, para mais informações consulte a 
[documentação oficial](https://php-enqueue.github.io/client/extensions/)

### Exemplo 
```php
$this->brokerFactory->createConsumer()
    ->addExtension(new SignalExtension())
    ->addExtension(new NewRelicExtension())
    ->consume("demo", $this);
```

## Referências
- [Extensões do enqueue](https://php-enqueue.github.io/consumption/extensions/)
- [SignalExtension](https://github.com/php-enqueue/enqueue-dev/blob/master/pkg/enqueue/Consumption/Extension/SignalExtension.php)
- [NewRelicExtension](https://github.com/PicPay/picpay-prokers/blob/master/src/Extensions/NewRelicExtension.php)
- [RetryAndDlqExtension](https://github.com/PicPay/picpay-prokers/blob/master/src/Extensions/NewRelicExtension.php)