FROM ubuntu:utopic

MAINTAINER Puguh Wijayanto

ENV DEBIAN_FRONTEND noninteractive

RUN apt-get update
RUN apt-get -y install build-essential g++ git
RUN apt-get install -y nginx \
	php5-fpm php5-mysql php-apc php5-imagick php5-imap php5-mcrypt php5-gd libssh2-php \
	mariadb-server 

RUN echo "cgi.fix_pathinfo = 0;" >> /etc/php5/fpm/php.ini
ADD docker-config/nginx.conf /etc/nginx/nginx.conf
ADD docker-config/sites-enabled /etc/nginx/sites-enabled

RUN rm -fr /var/www/html && git clone https://github.com/semplon/GeniXCMS /var/www/html
VOLUME ["/var/www/html"]

RUN chmod -R 777 /var/www/html/assets/images && \
	chmod -R 777 /var/www/html/assets/images/uploads && \
	chmod -R 777 /var/www/html/assets/images/uploads/thumbs && \
	chmod -R 777 /var/www/html/inc/mod && \
	chmod -R 777 /var/www/html/inc/themes

RUN sed -i -e 's/^listen =.*/listen = \/var\/run\/php5-fpm.sock/' /etc/php5/fpm/pool.d/www.conf

ADD docker-config/create_mysql_admin_user.sh /create_mysql_admin_user.sh
RUN chmod 755 /*.sh

EXPOSE 80 3306

ADD docker-config/run.sh /run.sh
RUN chmod +x /run.sh

CMD ["/run.sh"]
