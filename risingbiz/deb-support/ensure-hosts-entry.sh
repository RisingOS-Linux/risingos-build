#!/bin/sh
# ensure-hosts-entry.sh — garantiza que risingbiz.local resuelva a 127.0.0.1
# Se invoca desde risingbiz-hosts.service en cada arranque.
grep -qxF "127.0.0.1 risingbiz.local" /etc/hosts || echo "127.0.0.1 risingbiz.local" >> /etc/hosts
