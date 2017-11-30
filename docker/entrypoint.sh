#!/bin/bash
chown -R mysql:mysql /var/run/mysqld /var/lib/mysql
exec /usr/bin/supervisord --nodaemon -c /etc/supervisor/supervisord.conf
