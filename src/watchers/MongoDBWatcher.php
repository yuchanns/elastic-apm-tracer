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
        $this->span = $transaction->beginCurrentSpan('DB Query', 'db', 'mongodb', 'query');
        $commands = (array) $event->getCommand();
        foreach ($commands as $label => $value) {
            $this->span->context()->setLabel('db.' . $label, json_encode($value, JSON_UNESCAPED_UNICODE));
        }
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
}
