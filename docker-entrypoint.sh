#!/bin/bash
set -e

# --- Database Setup ---
echo "Starting MariaDB..."
if [ -f "/etc/init.d/mysql" ]; then
    /etc/init.d/mysql start
elif [ -f "/etc/init.d/mariadb" ]; then
    /etc/init.d/mariadb start
else
    echo "Error: MariaDB init script not found."
    exit 1
fi

# Wait for MySQL to be ready
while ! mysqladmin ping -hlocalhost --silent; do
    echo "Waiting for database connection..."
    sleep 2
done

DB_NAME=${DB_NAME:-backend_db}
DB_USER=${DB_USER:-user}
DB_PASS=${DB_PASS:-user}
DB_ROOT_PASS=${DB_ROOT_PASS:-root}
FILES_BASE=${FILES_PATH:-.}

if [ ! -d "$FILES_BASE" ]; then
    echo "Creating file base directory: $FILES_BASE"
    mkdir -p "$FILES_BASE"
fi

# Check if database exists, if not create it
if ! mysql -e "USE $DB_NAME" 2>/dev/null; then
    echo "Initializing database..."
    
    # Secure installation (simplified) & Create DB/User
    # Note: MariaDB 10.4+ uses mysql.user as a view, so we use ALTER USER
    mysql -e "DELETE FROM mysql.user WHERE User='';"
    mysql -e "DROP DATABASE IF EXISTS test;"
    mysql -e "DELETE FROM mysql.db WHERE Db='test' OR Db='test\\_%';"
    
    mysql -e "CREATE DATABASE IF NOT EXISTS \`$DB_NAME\`;"
    mysql -e "CREATE USER IF NOT EXISTS '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASS';"
    mysql -e "GRANT ALL PRIVILEGES ON \`$DB_NAME\`.* TO '$DB_USER'@'localhost';"
    mysql -e "FLUSH PRIVILEGES;"

    # Import Schema
    if [ -f /var/www/html/init_db.sql ]; then
        echo "Importing schema..."
        mysql "$DB_NAME" < /var/www/html/init_db.sql
    fi
    
    # Set root password LAST so we don't lock ourselves out during init
    mysql -e "ALTER USER 'root'@'localhost' IDENTIFIED BY '$DB_ROOT_PASS';"
    
    echo "Database initialized."
    
    echo "Database initialized."
else
    echo "Database already exists."
fi

# --- Config Generation ---
# Use TCP loopback instead of UNIX socket to avoid PDO DSN issues
DB_HOST=${DB_HOST:-127.0.0.1}

echo "Generating interfaces/config.php..."
cat > /var/www/html/interfaces/config.php <<EOF
<?php

namespace server\interfaces;

interface config{
    /* tells application the root path to operate on */
    const path = "${FILES_BASE}";
    const host = "${DB_HOST}";
    const database = "${DB_NAME}";
    const user = "${DB_USER}";
    const password = "${DB_PASS}";
    const captcha = "${CAPTCHA_ENABLE:-disable}";
    const secret = "${CAPTCHA_SECRET:-secret}";
    const domain = "${CAPTCHA_DOMAIN:-localhost}";
}

?>
EOF

echo "Starting Apache..."
exec "$@"
