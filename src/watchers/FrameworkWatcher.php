<?php

namespace Yuchanns\ElasticApmTracer\watchers;

use Elastic\Apm\ElasticApm;

class FrameworkWatcher
{
    public function register(): void
    {
        app()->terminating(function() {
            $transaction = ElasticApm::getCurrentTransaction();

            $transaction->end();
        });
    }
}
