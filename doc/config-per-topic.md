# Configuração por tópico

A biblioteca foi adaptada para aceitar configurações por fila/tópico e não mais ter apenas umas configuração por projeto. Com isso é possível termos um pouco mais de flexibilidade ao usar a biblioteca.

## Único para todas as filas no formato antigo

```php
return [
    'default' => 'rdkafka',
    'connections' => [
        'rdkafka' => [
            'global' => [
                'group.id' => 'default',
                'metadata.broker.list' => 'kafka:9092',
                'enable.auto.offset.store' => 'false',
                'message.send.max.retries' => '2',
                'request.required.acks' => '-1',
                'max.poll.interval.ms' => '10000',
            ],
            'topic' => [
                'auto.offset.reset' => 'earliest',
            ],
            'partitioner' => null,
            'log_level' => 0, // 0 .. 7
            'commit_async' => true,
        ],
    ],
];

```

## Único para todas as filas no formato novo

Notem que temos agora uma nova chave dentro da configuração da conexão, chamada `common`.

```php
return [
    'default' => 'rdkafka',
    'connections' => [
        'rdkafka' => [
            'common' => [
                'global' => [
                    'group.id' => 'default',
                    'metadata.broker.list' => 'kafka:9092',
                    'enable.auto.offset.store' => 'false',
                    'message.send.max.retries' => '2',
                    'request.required.acks' => '-1',
                    'max.poll.interval.ms' => '10000',
                ],
                'topic' => [
                    'auto.offset.reset' => 'earliest',
                ],
                'partitioner' => null,
                'log_level' => 0, // 0 .. 7
                'commit_async' => true,
            ],
        ],
    ],
];

```

## Configuração a nível de tópico

Neste caso de exemplo temos configurações para duas novas filas: 
- `core_contact-score`: altera o número de `acks`, aumenta o _max poll interval_ e altera a política de _reset_ para os _offsets_ do tópico.
- `core_mixpanel-consumer-update`: além de alterar também `acks` e _max poll interval_, aumenta o nível de _log_.

```php
return [
    'default' => 'rdkafka',
    'connections' => [
        'rdkafka' => [
            'common' => [
                'global' => [
                    'group.id' => 'default',
                    'metadata.broker.list' => 'kafka:9092',
                    'enable.auto.offset.store' => 'false',
                    'message.send.max.retries' => '2',
                    'request.required.acks' => '-1',
                    'max.poll.interval.ms' => '10000',
                ],
                'topic' => [
                    'auto.offset.reset' => 'earliest',
                ],
                'partitioner' => null,
                'log_level' => 0, // 0 .. 7
                'commit_async' => true,
            ],
            'queues' => [
                'core_contact-score' => [
                    'global' => [
                        'request.required.acks' => '0',
                        'max.poll.interval.ms' => '30000',
                    ],
                    'topic' => [
                        'auto.offset.reset' => 'latest',
                    ],
                ],
                'core_mixpanel-consumer-update' => [
                    'global' => [
                        'request.required.acks' => '1',
                        'max.poll.interval.ms' => '1000',
                    ],
                    'log_level' => 5,
                ],
            ],
        ],
    ],
];
```

> Neste caso a estratégia de configuração é utilizar todos os valores informados no `common` e sobrescrever com as chaves 
> informadas no tópico em específico. Caso a chave exista ela é substituída, caso contrário é criada.