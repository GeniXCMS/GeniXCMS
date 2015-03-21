#!/bin/bash
/usr/sbin/php-fpm -D && /usr/sbin/nginx

/create_mysql_admin_user.sh
