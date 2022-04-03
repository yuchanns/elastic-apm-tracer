<?php

namespace Yuchanns\ElasticApmTracer;

use Illuminate\Contracts\Foundation\Application;
use Elastic\Apm\ElasticApm;

class TracerElasticApm
{
    protected $app;

    protected $spanMap;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function getSpan($name, $type, $subType = null, $action = null)
    {
        if (isset($this->spanMap[$name])) {
            return $this->spanMap[$name];
        }

        $span = ElasticApm::getCurrentTransaction()->beginCurrentSpan($name, $type, $subType, $action);
        $this->spanMap[$name] = $span;

        return $span;
    }

    public function endSpan($name)
    {
        if (!isset($this->spanMap[$name])) {
            return;
        }

        $this->spanMap[$name]->end();
    }

    public function inject(array $header)
    {
        ElasticApm::getCurrentTransaction()->injectDistributedTracingHeaders(function(string $name, string $value) use (&$header): void {
            $header[$name] = $value;
        });

        return $header;
    }
}
