<?php

return [
    'default' => 'rdkafka',
    'connections' => [
        'rdkafka' => [
            'common' => [
                'global' => [
                    'group.id' => 'kafka',
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
                'phpunit-test' => [
                    'global' => [
                        'group.id' => 'phpunit',
                    ],
                    'topic' => [
                        'auto.offset.reset' => 'latest',
                    ],
                    'log_level' => 5,
                ],
            ],
        ],

        'beanstalkd' => [
            'common' => [
                'host' => 'beanstalkd',
                'port' => '11300',
                'timeout' => '1000',
            ],
            'queues' => [
                'phpunit-test' => [
                    'timeout' => '5000',
                ],
            ],
        ],

        'redis' => [
            'common' => [
                'host' => 'redis',
                'port' => '6233',
                'scheme_extensions' => [
                    'predis',
                ],
                'predis_options' => [
                    'prefix'  => '',
                ]
            ],
            'queues' => [
                'phpunit-test' => [
                    'port' => '1234',
                    'predis_options' => [
                        'prefix'  => 'php_',
                    ],
                ],
            ],
        ],
    ]
];
