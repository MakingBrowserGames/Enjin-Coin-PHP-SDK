#!/bin/bash
chown -R mysql:mysql /var/run/mysqld /var/lib/mysql
/usr/bin/supervisord -c /etc/supervisor/supervisord.conf
cd public_html && php -S localhost:80
