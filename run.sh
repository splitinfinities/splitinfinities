#!/usr/bin/env bash

_term()
{
  kill -TERM "$child" 2> /dev/null
}

trap _term SIGTERM
hhvm --mode daemon --config /etc/hhvm/server.ini --config /etc/hhvm/php.ini &
nginx &
child=$!
wait "$child"
