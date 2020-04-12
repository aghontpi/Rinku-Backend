# Docker Section

> how to setup.

# Use docker compose

```bash

docker-compose up

```

If everything went alright, you should see soemthing like below

```

CONTAINER ID        IMAGE               COMMAND                  CREATED             STATUS              PORTS                               NAMES
c48840f8a1a6        docker_server       "docker-php-entrypoi…"   56 seconds ago      Up 51 seconds       0.0.0.0:80->80/tcp, 9000/tcp        personal-cloud-php
21126efb6128        docker_database     "docker-entrypoint.s…"   59 seconds ago      Up 56 seconds       0.0.0.0:3306->3306/tcp, 33060/tcp   personal-cloud-mysq
`
```


## To build images seperately.

from Docker dirctory run, this will build the image for you.

```bash

docker build -t phpserver:0.0.4 php/

```

## To run the built images,

first Verify the existence of the images,

```bash

docker images ls phpserver

REPOSITORY          TAG                 IMAGE ID            CREATED              SIZE
phpserver           0.0.4               5f7b4c7374b5        About a minute ago   416MB

```

then run the command.

```bash

docker run -d -p 80:80  --mount='src=/current/whole/directory,dst=/var/www/html/,type=bind' --name phpserver  phpserver:0.0.4

docker ps 

CONTAINER ID        IMAGE               COMMAND                  CREATED             STATUS              PORTS                NAMES
1123bd735b1d        phpserver:0.0.1     "docker-php-entrypoi…"   10 seconds ago      Up 9 seconds        0.0.0.0:80->80/tcp   phpserver

```

## Xdebug


If you want to install xdebug to install on your own

```bash


RUN pecl install -f xdebug \
    && echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > /usr/local/etc/php/conf.d/xdebug.ini


```

xdebug is prebuilt in the images, you just have to configure it with autostart, ip & remote debug.

```bash

echo "xdebug.remote_enable = 1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.remote_autostart = 1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \ 
    && echo "xdebug.remote_host=192.168.1.33" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \ 


```
connect to port 9000 from the client, sublime/vscode, but incase if you want to enable logfile for xdebug, connect to the contaienr then do the following command, and reload apache2.

```bash

echo 'xdebug.remote_log="/tmp/xdebug.log"' >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

```


