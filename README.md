# Rinku-Backend

> Backend service for creating and tracking shareable download links with built-in analytics, logging, and optional Google reCAPTCHA enforcement.

[![release][badge]][release link] [![license][license-badge]][license file] [![docker tag][docker-badge]][docker hub]

[license-badge]: https://img.shields.io/github/license/aghontpi/Rinku-Backend?style=flat-square
[license file]: https://github.com/aghontpi/Rinku-Backend/blob/master/LICENSE
[badge]: https://img.shields.io/github/v/release/aghontpi/Rinku-Backend?include_prereleases&style=flat-square
[release link]: https://github.com/aghontpi/Rinku-Backend/releases
[docker-badge]: https://img.shields.io/docker/v/bluepie/rinku?logo=docker&style=flat-square&sort=semver
[docker hub]: https://hub.docker.com/repository/docker/bluepie/rinku

## Why Rinku?

- Generate expirable download links for any file tree and share a clean landing page per asset.
- Track downloads, IP/location metadata, and high-level trends without extra tooling.
- Gate sensitive downloads with configurable reCAPTCHA and session-based auth.
- Deploy the full stack as a single Docker image or run the lightweight PHP framework directly.

## Feature Highlights

- Download link CRUD, per-link landing page, and aggregated stats/logs.
- Toggleable Google reCAPTCHA (enable/disable or swap secrets per environment).
- Self-maintained PHP 7 mini-framework, no Composer dependency sprawl.
- Docker support: `docker-compose` for dev, single image for prod.
- Adminer bundled for quick DB inspection at `http://localhost:8080` when using compose.

UI is here: [React front end](https://github.com/aghontpi/Rinku-Frontend).

## Quick Start

### Run with Docker (recommended)
```bash
docker pull bluepie/rinku:latest
docker run -d -p 80:80 --name rinku \
    -e DB_NAME=backend_db -e DB_USER=user -e DB_PASS=user \
    bluepie/rinku:latest
```
- `bluepie/rinku:v1.2.0` is a multi-arch tag (linux/amd64 and linux/arm64) if you prefer pinning to the release referenced by the Docker badge above.
- Mount your files directory with `-v /host/files:/data -e FILES_PATH=/data`.
- Need DB access? Compose stack exposes Adminer: `docker-compose up` then visit [http://localhost:8080/?server=database&username=root&db=backend_db](http://localhost:8080/?server=database&username=root&db=backend_db).

### Run natively
1. Create a MySQL database and import `Docker/mysql/dbinit/tables.sql` (default user: `testuser` / `123456`).
2. Update `server/interfaces/config.php` with your filesystem path and credentials.
3. Serve `index.php` behind Apache/Nginx/PHP built-in server.

## Configuration

Edit `server/interfaces/config.php` (make sure the target directories are writable):

```php
const path = ".";                         // Directory containing downloadable files
const host = "database_host_name_here";    // DB host or container name
const database = "database_name_here";
const user = "user_database";
const password = "password_database";
const captcha = "enable";                  // enable|disable reCAPTCHA enforcement
const secret = "secret_here";              // Only used when captcha === enable
const domain = "domain_to_verify_captcha"; // Expected host sent to Google
```

Image-based deployments use environment variables instead (see table below).

## Docker Image Reference

The published image bundles PHP-FPM, nginx, and MariaDB so you can drop it onto any host. Source Docker assets live under `Docker/` if you need to extend it.
All `v1.2.0+` tags pushed to Docker Hub are manifest lists covering both `linux/amd64` and `linux/arm64`.

### Build or pull
```bash
# Pull
docker pull bluepie/rinku:latest

# Build locally
docker build -t bluepie/rinku:latest .
```

### Run
```bash
docker run -d -p 80:80 bluepie/rinku:latest
```

### Bind a custom files directory
```bash
docker run -d -p 80:80 \
    -v /absolute/host/files:/data \
    -e FILES_PATH=/data \
    bluepie/rinku:latest
```

### Environment variables

| Variable | Default | Description |
| --- | --- | --- |
| `DB_NAME` | `backend_db` | Schema created during container init. |
| `DB_USER` | `user` | Application DB user inside MariaDB. |
| `DB_PASS` | `user` | Password for `DB_USER`. |
| `DB_ROOT_PASS` | `root` | Internal root password (used by init scripts). |
| `FILES_PATH` | `.` | Base directory scanned for downloads. Mount a host path and point this here. |
| `CAPTCHA_ENABLE` | `disable` | Set to `enable` to require Google reCAPTCHA. |
| `CAPTCHA_SECRET` | `secret` | reCAPTCHA secret token. |
| `CAPTCHA_DOMAIN` | `localhost` | Hostname validated by reCAPTCHA. |

Deployment recipe:

```bash
docker run -d -p 80:80 --name rinku-prod \
    -e DB_NAME=backend_db \
    -e DB_USER=user \
    -e DB_PASS=user \
    -e CAPTCHA_ENABLE=enable \
    -e CAPTCHA_SECRET=your-recaptcha-secret \
    -e CAPTCHA_DOMAIN=downloads.example.com \
    -e FILES_PATH=/data \
    -v /srv/rinku-files:/data \
    bluepie/rinku:latest
```

## Project Layout

- `modules/` – endpoint handlers loaded by `server/classes/request.php`.
- `classes/` & `abstract/` – internal framework scaffolding (database singleton, response helpers, module base class).
- `Docker/` – docker-compose, per-service Dockerfiles, SQL bootstrap scripts (`Docker/mysql/dbinit/`).
- `API_TESTS.md` – curl snippets for every endpoint.

## Development Tips

- Use VS Code Remote Containers to open the PHP container directly (`/var/www/html/`); install Xdebug via [`Docker/README.MD`](Docker/README.MD) for breakpoint debugging.
- Ignore `Docker/.mysql` in watchers and version control; it stores persisted database files for local compose runs.

## Security Footnotes

- The sample CORS header (`Access-Control-Allow-Origin: http://localhost:3000`) exists only for React dev convenience—remove or tighten it in production.

---

For GUI, deployment guidance, or API samples? See `API_TESTS.md`, `Docker/README.MD`, or the [Rinku Frontend](https://github.com/aghontpi/Rinku-Frontend) repository.

## License

Distributed under the [Apache License 2.0](LICENSE). See the `LICENSE` file for full text and attribution requirements.
