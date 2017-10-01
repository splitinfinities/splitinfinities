FROM hhvm/hhvm:3.18-lts-latest

# apt stuff
RUN add-apt-repository ppa:nginx/stable && \
    apt-get -qq update && \
    apt-get -y install nginx curl unzip

# "external" stuff
RUN curl https://wordpress.org/latest.zip -o /var/www/latest.zip && \
  curl https://downloads.wordpress.org/plugin/secure-db-connection.1.1.2.zip -Lo /var/www/secure-db-connection.zip && \
  curl https://downloads.wordpress.org/plugin/sendgrid-email-delivery-simplified.zip -o /var/www/sendgrid.zip && \
  curl https://s3.amazonaws.com/rds-downloads/rds-combined-ca-bundle.pem -o /etc/aws-ca.pem && \
  cd /var/www && \
  unzip latest.zip && \
  unzip secure-db-connection.zip && \
  unzip sendgrid.zip && \
  rm -rf *.zip

# hhvm stuff
COPY hhvm/server.ini /etc/hhvm
COPY hhvm/php.ini /etc/hhvm

# nginx stuff
COPY nginx.conf /etc/nginx

# wordpress stuff
RUN cd /var/www && \
  mv secure-db-connection wordpress/wp-content/plugins && \
  mv sendgrid-email-delivery-simplified wordpress/wp-content/plugins
COPY wp-config.php /var/www/wordpress
COPY email-settings.php /var/www/wordpress

WORKDIR /srv
EXPOSE 3000

COPY run.sh .
CMD ["./run.sh"]
