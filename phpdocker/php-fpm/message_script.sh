#!/usr/bin/env bash
sleep 10;
php /application/bin/console messenger:consume >&1;