# Yuchanns/ElasticApmTracer

An elastic apm package for laravel which requires the extension elastic_apm.so

## Requirements
* Enable of [Elastic Apm Extension](https://github.com/elastic/apm-agent-php).
* Laravel
* update `/etc/php7/php-fpm.d/www.conf` or config files like this if php-fpm is used:
    ```ini
    clear_env = no
    env["ELASTIC_APM_SERVER_URL"] = $ELASTIC_APM_SERVER_URL
    env["ELASTIC_APM_SERVICE_NAME"] = $ELASTIC_APM_SERVICE_NAME
    env["ELASTIC_APM_SECRET_TOKEN"] = $ELASTIC_APM_SECRET_TOKEN
    ```
## Install
```bash
composer require yuchanns/elastic-apm-tracer
```

## TODO Maybe (**PR is Welcome**)
- [ ] Injection of Eloquent
- [ ] Injection of Redis
