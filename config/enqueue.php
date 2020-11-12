<?php

return [
    'default' => 'rdkafka',
    'connections' => [

        /**
         * @link https://php-enqueue.github.io/transport/kafka/
         * @link https://github.com/edenhill/librdkafka/blob/master/CONFIGURATION.md
         * @link https://github.com/edenhill/librdkafka/issues/826
         */
        'rdkafka' => [
            'global' => [
                'group.id' => $_ENV['PICPAY_BROKER_KAFKA_GROUP_ID'],
                'metadata.broker.list' => $_ENV['PICPAY_BROKER_KAFKA_BROKERS'],
                'enable.auto.offset.store' => 'false',
                'message.send.max.retries' => '2',
                'request.required.acks' => '-1',
                'max.poll.interval.ms' => $_ENV['PICPAY_BROKER_KAFKA_MAX_POOL_INTERVAL_MS'],
                'queued.max.messages.kbytes' => '10000',
                'queued.min.messages' => '50'
            ],
            'topic' => [
                'auto.offset.reset' => 'earliest',
            ],
            /**
             * @link https://arnaud-lb.github.io/php-rdkafka/phpdoc/rdkafka-topicconf.setpartitioner.html
             */
            'partitioner' => null,
            'log_level' => 0, // 0 .. 7
            'commit_async' => true,
        ],

        /**
         * @link https://php-enqueue.github.io/transport/pheanstalk/
         */
        'beanstalkd' => [
            'host' => $_ENV['PICPAY_BROKER_BEANSTALKD_HOST'],
            'port' => $_ENV['PICPAY_BROKER_BEANSTALKD_PORT'],
            'timeout' => $_ENV['PICPAY_BROKER_BEANSTALKD_TIMEOUT'],
        ],

        /**
         * @link https://php-enqueue.github.io/transport/redis/
         */
        'redis' => [
            'host' => $_ENV['PICPAY_BROKER_REDIS_HOST'],
            'port' => $_ENV['PICPAY_BROKER_REDIS_PORT']
        ],
    ]
];
