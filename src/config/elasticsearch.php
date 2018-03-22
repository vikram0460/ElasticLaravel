<?php

return array(

	'connectionClass' => '\Elasticsearch\Connections\GuzzleConnection',
	'connectionFactoryClass' => '\Elasticsearch\Connections\ConnectionFactory',
    'connectionPoolClass' => '\Elasticsearch\ConnectionPool\StaticNoPingConnectionPool',
    'selectorClass' => '\Elasticsearch\ConnectionPool\Selectors\StickyRoundRobinSelector',
    'serializerClass' => '\Elasticsearch\Serializers\SmartSerializer',
    'hosts' => ['127.0.0.1:9200'],
    'sniffOnStart' => false,
    'connectionParams' => array(),
    'logging' => false,
    'logObject' => null,
    'logPath' => storage_path().'/logs/elasticsearch.log',
    'logLevel' => Monolog\Logger::WARNING,
    'traceObject' => null,
    'tracePath' => storage_path().'/logs/elasticsearch_trace.log',
    'traceLevel' => Monolog\Logger::WARNING,
    'guzzleOptions' => array(),
    'connectionPoolParams' => [ 'randomizeHosts' => true],
    'retries' => null

);