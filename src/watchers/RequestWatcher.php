<?php

namespace Yuchanns\ElasticApmTracer\watchers;

use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Support\Facades\Event;
use Elastic\Apm\ElasticApm;

class RequestWatcher
{
    public function register(): void
    {
        Event::listen(RequestHandled::class, function (RequestHandled $event) {
            $transaction = ElasticApm::getCurrentTransaction();

            if ($transaction == null) {
                $name = $event->request->method() . ':' . optional($event->request->route())->uri() ?? $event->request->getPathInfo();
                $transaction = ElasticApm::newTransaction($name, config('APP_NAME') ? config('APP_NAME') : 'http-server')
                    ->distributedTracingHeaderExtractor(
                        function (string $headerName) use ($event): ?string {
                            $header = $event->request->header();
                            return $header[$headerName] ?? null;
                        }
                    )->begin();
            }

            $ctx = $transaction->context();

            $ctx->setLabel('http.host', $event->request->getHost());
            $ctx->setLabel('http.route', str_replace($event->request->root(), '', $event->request->fullUrl()) ?: '/');
            $ctx->setLabel('http.method', $event->request->method());
            $ctx->setLabel('http.status_code', (string)$event->response->getStatusCode());
            $ctx->setLabel('http.error', $event->response->isSuccessful() ? 'false' : 'true');
            $ctx->setLabel('controller_action', optional($event->request->route())->getActionName());

            $data = $event->request->toArray();
            isset($data['password']) && $data['password'] = md5($data['password']);
            isset($data['pwd']) && $data['pwd'] = md5($data['pwd']);
            $header = $event->request->header();
            $header = \Yuchanns\ElasticApmTracer\facades\TracerElasticApm::inject($header);

            $ctx->setLabel('http.requestHeader', json_encode($header, JSON_UNESCAPED_UNICODE));

            $ctx->setLabel('http.requestData', json_encode($data, JSON_UNESCAPED_UNICODE));

            $responseData = json_decode($event->response->getContent(), true);

            if (isset($responseData['code']) && $responseData['code'] == 500) {
                $ctx->setLabel('error', true);
            }

            $ctx->setLabel('http.responseData', json_encode($responseData, JSON_UNESCAPED_UNICODE));
        });
    }
}
