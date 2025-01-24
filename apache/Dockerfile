FROM httpd:2.4.63-alpine

LABEL maintainer="evilnick002@gmail.com"

RUN apk update; \
		apk upgrade;

COPY local.apache.conf /usr/local/apache2/conf/local.apache.conf

RUN echo "Include /usr/local/apache2/conf/local.apache.conf" \
		>> /usr/local/apache2/conf/httpd.conf