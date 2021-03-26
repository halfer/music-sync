FROM php:8.0-cli-alpine

# @todo Weird, this pulls in PHP 7.4!
RUN apk update && apk upgrade && apk add -y composer

WORKDIR /project

CMD ["/bin/sh"]
