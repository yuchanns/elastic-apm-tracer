<?php

return [

    'watchers' => [
        Yuchanns\ElasticApmTracer\watchers\RequestWatcher::class,
        Yuchanns\ElasticApmTracer\watchers\FrameworkWatcher::class,
    ],

];
