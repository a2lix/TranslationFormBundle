FROM php:8.5-cli-trixie

RUN apt-get update && apt-get install -y --no-install-recommends \
    file \
    git \
    openssh-client \
    && rm -rf /var/lib/apt/lists/*

ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN set -eux; \
    install-php-extensions \
        @composer \
        apcu \
        opcache \
        # xdebug \
    ;

RUN useradd -m vscode

USER vscode
WORKDIR /workspace
