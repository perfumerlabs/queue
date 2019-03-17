Queue provides scheduled execution of tasks.

Installation
============

```bash
docker run \
-p 80:80/tcp \
-e QUEUE_HOST=example.com \
-e "QUEUE_WORKERS={\"my_worker\":1}" \
-v tarantool:/var/lib/tarantool \
-d perfumerlabs/queue:v1.0.0
```

Environment variables
=====================

- QUEUE_HOST - server domain (without http://). Required.
- QUEUE_WORKERS - list of workers in json format (see below). Required.

Volumes
=======

- /var/lib/tarantool - Tarantool data directory.

If you want to make any additional configuration of container, mount your bash script to /opt/setup.sh. This script will be executed on container setup.

Software
========

1. Ubuntu 16.04 Xenial
2. Tarantool 1.7
3. PHP 7.1

How it works
============

Queueing is a common task for web applications. Queue responsibility is to register a task and execute it at particular time.

This Queue works in terms of HTTP-requests. Firstly, you register a task via REST API. You can provide delay or datetime request parameter to tell Queue, when to execute the task. Then, in this time Queue makes HTTP-request with parameters you provided to your backend.

Workers
=======

Often it is needed to have multiple types of workers or several workers of same type. For example, you send sms via some provider and they restricts API calls to 3 requests per second. In this case you have to use dedicated queue worker with 333 milliseconds freeze time between tasks. Or, for instance, you send push notifications. In this case you should configure multiple workers of same type, because single worker will handle tasks too slow.

Queue supports configuring any number of workers and its types. For example, we want to have 3 workers with name "foo", 2 workers with name "bar" and 1 - "baz". To configure it provide QUEUE_WORKERS environment variable in json format like this:

```
-e "QUEUE_WORKERS={\"foo\":3,\"bar\":2,\"baz\":1}"
```

Queue automatically configures database to have 3 tables for queues and start 6 workers.

API Reference
=============

###### Create new task

POST /task

- worker: which tube will execute task. Required.
- url: URL that will be called. Required.
- method: HTTP request method that will be called. Required.
- delay: delay in seconds. Required, if no "datetime" provided.
- datetime: Datetime of execution. Format "YYYY-MM-DD HH:ii:ss". UTC. Required, if no "delay" provided.
- headers: headers that will be sent with the request. Optional.
- json: json body that will be sent with the request. Optional.
- query_string: object will be transformed to query string (for example, "param1=foo&param2=bar") and sent as body. If request method is GET or HEAD it will be appended to url. Optional.
- body: Raw body that will be sent with the request. Optional.
- sleep: Time in microseconds to sleep after execution of the task. Optional.

Use one of "delay" or "datetime". Use one of "json", "query_string" or "body".

Request body example:

```javascript
{
    "worker": "foo",
    "url": "http://my-site.com/action1",
    "method": "post",
    "delay": 30,
    "headers": {
        "Authorization": "Bearer my_token"
    },
    "json": {
        "param": "value"
    }
}
```

Request body example:

```javascript
{
    "worker": "bar",
    "url": "http://my-site.com/action2",
    "method": "get",
    "datetime": "2019-01-01 10:00:00",
    "query_string": {
        "param": "value"
    }
}
```

Request body example:

```javascript
{
    "worker": "baz",
    "url": "http://my-site.com/action3",
    "method": "put",
    "delay": 30,
    "body": "raw body",
    "sleep": 100
}
```

Response example:

```javascript
{
    "status": true
}
```