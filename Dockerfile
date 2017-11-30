FROM ubuntu:17.10

ENV DEBIAN_FRONTEND noninteractive

# Bring apt up-to-date and have latest packages ready
RUN apt-get update -y && apt-get upgrade -yu && apt-get autoremove -y

# First install supervisor and basic requirements
RUN apt-get install -y supervisor curl wget

# Set up php-fpm 7.1
RUN apt-get install -y php7.1-fpm php7.1-mysql php7.1-gd php7.1-curl php7.1-mcrypt
RUN php -v

# The run directory (and log!) for php does not get created when PHP is installed..
RUN mkdir -p /run/php /run/mysqld
RUN mkdir -p /var/log/php /var/log/mysqld

# Set up mysql 5
RUN apt-get install -y mysql-server-5.7

# Mysql needs write access to /var/run/mysqld
RUN chown -R mysql:mysql /var/run/mysqld /var/lib/mysql

# Work from /root and make sure it ecists
RUN mkdir -p /root
COPY . /www
WORKDIR /www

# Copy the supervisord config files to the proper path
RUN mv /www/docker/supervisord-conf/* /etc/supervisor/conf.d/
RUN ls -lga /etc/supervisor/conf.d

# Move the startup file to /
RUN mv /www/docker/entrypoint.sh /

# Remove the copied docker files because they shouldn't be in /www
RUN rm -rf /www/docker

EXPOSE 80
ENTRYPOINT ["/entrypoint.sh"]

