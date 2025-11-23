# API Endpoint & Test Guide

This document lists every module endpoint exposed by `index.php`, provides ready-to-run `curl` commands, and records the expected authentication requirements.

## 1. Prerequisites
- Docker Desktop running locally.
- Image `bluepie/rinku:latest` built or pulled.
- `curl` and (optionally) `jq` installed on your host.

### Start the all-in-one container
```bash
docker run -d -p 8081:80 --name rinku-api bluepie/rinku:latest
```
> Stop & remove when done:
> ```bash
> docker rm -f rinku-api
> ```

All requests below target `http://localhost:8081/index.php` and use JSON bodies of the form:
```json
{"endPoint":"<moduleName>","data":{ ... }}
```
Use a cookie jar to persist the PHP session:
```bash
COOKIE_JAR=cookies.txt
```

## 2. Public Endpoints

### 2.1 Login (creates session cookie)
```bash
curl -i -c "$COOKIE_JAR" -H "Content-Type: application/json" \
  -d '{
    "endPoint":"login",
    "data":{"uname":"testuser","pword":"123456","captcha":""}
  }' \
  http://localhost:8081/index.php
```
Expected: `response":"success"`, user info, and `Set-Cookie: aghontpi=...`.

### 2.2 Download metadata (no auth required)
Replace `<DOWNLOAD_NAME>` with a valid token (default seed `4c7c3f0893b92962`).
```bash
curl -i -H "Content-Type: application/json" \
  -d '{
    "endPoint":"download",
    "data":{"fileid":"4c7c3f0893b92962","action":"","filepath":"","captcha":""}
  }' \
  http://localhost:8081/index.php
```

### 2.3 Download by file path lookup (requires login)
> Use the same relative path you supplied to `createDL` (ex: `/index.php`).
```bash
curl -i -b "$COOKIE_JAR" -H "Content-Type: application/json" \
  -d '{
    "endPoint":"download",
    "data":{"fileid":"","action":"","filepath":"/index.php","captcha":""}
  }' \
  http://localhost:8081/index.php
```

### 2.4 Trigger download & retrieve file (requires login if you want logs attributed)
```bash
# Step 1: request download action (sets session download info)
curl -i -b "$COOKIE_JAR" -H "Content-Type: application/json" \
  -d '{
    "endPoint":"download",
    "data":{"fileid":"4c7c3f0893b92962","action":"download","filepath":"","captcha":""}
  }' \
  http://localhost:8081/index.php

# Step 2: perform GET to fetch the binary
curl -L -b "$COOKIE_JAR" -o downloaded-index.php http://localhost:8081/index.php
```

## 3. Authenticated Endpoints (login first)

For every command below, ensure `-b "$COOKIE_JAR"` is present.

### 3.1 Create Download Link (`createDL`)
```bash
curl -i -b "$COOKIE_JAR" -H "Content-Type: application/json" \
  -d '{
    "endPoint":"createDL",
    "data":{"file":"/index.php"}
  }' \
  http://localhost:8081/index.php
```

### 3.2 List Files (`fileOperation`)
```bash
curl -s -b "$COOKIE_JAR" -H "Content-Type: application/json" \
  -d '{
    "endPoint":"fileOperation",
    "data":{"operation":"list"}
  }' \
  http://localhost:8081/index.php | jq '.' | head
```

### 3.3 Manage Links – List (`managelinks`)
```bash
curl -i -b "$COOKIE_JAR" -H "Content-Type: application/json" \
  -d '{
    "endPoint":"managelinks",
    "data":{"limit":10}
  }' \
  http://localhost:8081/index.php
```
> Response includes a JSON-encoded `list`. Parse to extract `id` values for updates.

### 3.4 Manage Links – Update Status
Replace `1` with an actual `download_id`, and `Y`/`N` as desired.
```bash
curl -i -b "$COOKIE_JAR" -H "Content-Type: application/json" \
  -d '{
    "endPoint":"managelinks",
    "data":{"id":1,"update":"Y"}
  }' \
  http://localhost:8081/index.php
```

### 3.5 Download Logs (`downloadLogs`)
```bash
curl -i -b "$COOKIE_JAR" -H "Content-Type: application/json" \
  -d '{
    "endPoint":"downloadLogs",
    "data":{"limit":10}
  }' \
  http://localhost:8081/index.php
```

### 3.6 Stats (`stats`)
```bash
curl -i -b "$COOKIE_JAR" -H "Content-Type: application/json" \
  -d '{
    "endPoint":"stats",
    "data":{"date":"2025-11-23"}
  }' \
  http://localhost:8081/index.php
```

### 3.7 Logout (`logout`)
```bash
curl -i -b "$COOKIE_JAR" -H "Content-Type: application/json" \
  -d '{"endPoint":"logout","data":{}}' \
  http://localhost:8081/index.php
```

## 4. Notes
- `limit` parameters must stay below 10,000.
- `download` requests log activity when `action` is `download`.
- All success responses follow `{ "response":"success", "content":{...} }`.
- Errors follow `{ "response":"error", "errors":{ "errMsg":"..." } }`.

Use this document as the canonical checklist for regression testing before publishing new Docker images.
