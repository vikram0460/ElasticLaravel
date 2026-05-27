<?php namespace Fadion\Bouncy;

use Illuminate\Support\Facades\Config;
use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\Client;
use Monolog\Logger;
use Monolog\Level;
use Monolog\Handler\StreamHandler;

trait BouncyCollectionTrait {

    /**
     * Indexes all the results from the
     * collection.
     *
     * @return array
     */
    public function index()
    {
        if ($this->isEmpty()) {
            return false;
        }

        $params = array();

        foreach ($this->all() as $item) {
            $params['body'][] = array(
                'index' => array(
                    '_index' => $item->getIndex(),
                    '_id' => $item->getKey()
                )
            );

            $params['body'][] = $item->documentFields();
        }

        return $this->getElasticClient()->bulk($params);
    }

    /**
     * Deletes the indexes of the collection.
     *
     * @return array
     */
    public function removeIndex()
    {
        if ($this->isEmpty()) {
            return false;
        }

        $params = array();

        foreach ($this->all() as $item) {
            $params['body'][] = array(
                'delete' => array(
                    '_index' => $item->getIndex(),                    
                    '_id' => $item->getKey()
                )
            );
        }

        return $this->getElasticClient()->bulk($params);
    }

    /**
     * Reindexes all the results from the
     * collection.
     *
     * @return array
     */
    public function reindex()
    {
        $this->removeIndex();

        return $this->index();
    }

    /**
     * Returns an Elasticsearch\Client instance.
     *
     * @return Client
     */
    protected function getElasticClient()
    {
        $configurations = Config::get('elasticsearch');
        $hosts = $configurations['hosts'] ?? [];
        $logPath = $configurations['logPath'] ?? storage_path('logs/elasticsearch.log');
        $logger = self::defaultLogger($logPath);
        $client = ClientBuilder::create()
                                ->setHosts($hosts)        // Set the hosts
                                ->setLogger($logger)      // Set the logger with a default logger
                                ->build();
        
        return $client;
    }
    
    /**
     * @param $path string
     * @param Level $level
     * @return \Monolog\Logger\Logger
     */
    public static function defaultLogger($path, $level = Level::Warning)
    {
        $log       = new Logger('log');
        $handler   = new StreamHandler($path, $level);
        $log->pushHandler($handler);
        
        return $log;
    }

}
