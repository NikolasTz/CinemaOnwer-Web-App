FROM php:7.4-apache
ENV NODE_ENV=production
COPY project_2/ /var/www/html
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf
EXPOSE 80