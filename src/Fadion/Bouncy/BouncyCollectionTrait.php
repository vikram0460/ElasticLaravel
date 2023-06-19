<?php namespace Fadion\Bouncy;

use Illuminate\Support\Facades\Config;
use Elasticsearch\Client as ElasticSearch;
use Elasticsearch\ClientBuilder;

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
                    '_type' => $item->getTypeName(),
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
     * @return ElasticSearch
     */
    protected function getElasticClient()
    {
        $configurations = Config::get('elasticsearch');
        $retries = $configurations['retries'];
        $hosts = $configurations['hosts'];
        $connectionPool = $configurations['connectionPoolClass'];
        $selector = $configurations['selectorClass'];
        $serializer = $configurations['serializerClass'];
        $logPath = $configurations['logPath'];
        $logger = ClientBuilder::defaultLogger($logPath);
        $client = ClientBuilder::create()
                                ->setHosts($hosts)        // Set the hosts
                                ->setLogger($logger)      // Set the logger with a default logger
                                ->build();
        
        return $client;
    }

}
