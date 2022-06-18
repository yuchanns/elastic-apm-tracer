<?php

namespace Yuchanns\ElasticApmTracer\watchers;

use Elastic\Apm\ElasticApm;
use MongoDB\Driver\Monitoring\CommandFailedEvent;
use MongoDB\Driver\Monitoring\CommandStartedEvent;
use MongoDB\Driver\Monitoring\CommandSucceededEvent;
use MongoDB\Driver\Monitoring\CommandSubscriber as CommandSubscriberBase;

use function MongoDB\Driver\Monitoring\addSubscriber;

class MongoDBWatcher implements CommandSubscriberBase
{
    private $span;

    public function register(): void
    {
        if (!extension_loaded('mongodb')) {
            return;
        }

        addSubscriber($this);
    }

    public function commandStarted(CommandStartedEvent $event)
    {
        $transaction = ElasticApm::getCurrentTransaction();
        $spanName = $event->getCommandName();
        $cmd = $event->getCommand();
        if (($collectionName = $this->collectionName($spanName, (array)$cmd)) != "") {
            $spanName = $collectionName . "." . $spanName;
        }
        $this->span = $transaction->beginCurrentSpan($spanName, 'db', 'mongodb', 'query');
        $command = json_encode($cmd);
        $this->span->context()->db()->setStatement($command);
    }

    public function commandSucceeded(CommandSucceededEvent $event)
    {
        $this->span->end();
    }

    public function commandFailed(CommandFailedEvent $event)
    {
        $this->span->context()->setLabel("error", true);
        $this->span->end();
    }

    public function collectionName(string $commandName, array $command)
    {
        switch ($commandName) {
            case "aggregate":
            case "count":
            case "distinct":
            case "mapReduce":
                // Geospatial Commands
            case "geoNear":
            case "geoSearch":
                // Query and Write Operation Commands
            case "delete":
            case "find":
            case "findAndModify":
            case "insert":
            case "parallelCollectionScan":
            case "update":
                // Administration Commands
            case "compact":
            case "convertToCapped":
            case "create":
            case "createIndexes":
            case "drop":
            case "dropIndexes":
            case "killCursors":
            case "listIndexes":
            case "reIndex":
                // Diagnostic Commands
            case "collStats":
                return $command[$commandName] ?? "";
            case "getMore":
                return $command["collection"] ?? "";
        }
        return "";
    }
}
