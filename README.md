# Rinku-Backend

> Create/manage download links for filesystems, Re-captcha, analytics,& download log for downloaded files,

[![release][badge]][release link] [![license][license-badge]][license file]

[license-badge]: https://img.shields.io/github/license/aghontpi/Rinku-Backend?style=flat-square
[license file]: https://github.com/aghontpi/Rinku-Backend/blob/master/LICENSE
[badge]: https://img.shields.io/github/v/release/aghontpi/Rinku-Backend?include_prereleases&style=flat-square
[release link]: https://github.com/aghontpi/Rinku-Backend/releases


## Features

- Create download link for any files
- Manage all the download links
- Download page for files is seperate
- Get analytics of the download statistics
- Includes google Recaptcha (configurable, can be turned off/on)
- Get log of download files

## checkout the [FrontEnd](https://github.com/aghontpi/Rinku-Frontend) written in React 

## Built with

- php 7
- Famework
    - created from scratch, that is very simple to use
- Docker
- reCAPTCHA - google

## Folder structure


- Docker-compose which utilizes 3 docker containers
- Docker/
    - php
    - mysql
    - adminer
        
- mysql setup files
    - Docker/mysql/dbinit/


## Documentation

Set the path, make sure the path mentioned has appropriate permission.

```php

server/interfaces/config.php
   
    const path = ".";
    const host = "database_host_name_here";
    const database = "database_name_here";
    const user = "user_database";
    const password = "password_database";
    const captcha = "enable";
    const secret = "secret_here"; // only matters if captcha is enabled
    const domain = "domain_to_verify_captcha"; // only matters if captcha is enabled

```

## Running Natively

create mysql database, import the following file

```bash

Docker/mysql/dbinit/tables.sql

# credentials for default user to login
# username: testuser
# password: 123456

```

## Run with Docker

### Server

You must have Docker with docker-compose installed.


```bash

docker-compose up

```
note: 

project-path is mounted

mysql-container's lib files are mounted under "Docker/.mysql" to maintain persistence.

ignore above in watchers & .gitignore

For more, use [README](https://github.com/aghontpi/Rinku-Backend/blob/master/Docker/README.MD) inside "Docker/" folder.

**Once docker-compose is up, navigate to [http://localhost:8080/?server=database&username=root&db=backend_db](http://localhost:8080/?server=database&username=root&db=backend_db) to view the database in adminer.

## Recommend for development: running with vscode remote Container

Start php contaniner with vscode remote container feature,

Change working directory to /var/www/html/

install xdebug [instructions here](https://github.com/Gopinath001/Rinku-Backend/blob/master/Docker/README.MD)

end - hassle free setup 

## Security

Remove "Access-Control-Allow-Origin: http://localhost:3000" from the whole project. Its not removed 
to support development with react.
