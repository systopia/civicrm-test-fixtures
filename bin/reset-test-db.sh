#!/usr/bin/env bash
set -euo pipefail

echo "Resetting CiviCRM test database…"

DB_HOST="${TEST_DB_HOST}"
DB_USER="${TEST_DB_USER}"
DB_PASS="${TEST_DB_PASS}"
DB_NAME="${TEST_DB_NAME}"

SQL_DIR="/var/www/html/core/sql"

echo "→ Dropping database ${DB_NAME}"
mysql -h"${DB_HOST}" -u"${DB_USER}" -p"${DB_PASS}" -e "DROP DATABASE IF EXISTS ${DB_NAME};"

echo "→ Creating database ${DB_NAME}"
mysql -h"${DB_HOST}" -u"${DB_USER}" -p"${DB_PASS}" -e "
  CREATE DATABASE ${DB_NAME}
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
"

echo "→ Importing base_tables.mysql"
mysql -h"${DB_HOST}" -u"${DB_USER}" -p"${DB_PASS}" "${DB_NAME}" < "${SQL_DIR}/base_tables.mysql"

#echo "→ Importing test_data.mysql"
#mysql -h"${DB_HOST}" -u"${DB_USER}" -p"${DB_PASS}" "${DB_NAME}" < "${SQL_DIR}/test_data.mysql"
#
#if [[ -f "${SQL_DIR}/test_data_second_domain.mysql" ]]; then
#  echo "→ Importing test_data_second_domain.mysql"
#  mysql -h"${DB_HOST}" -u"${DB_USER}" -p"${DB_PASS}" "${DB_NAME}" < "${SQL_DIR}/test_data_second_domain.mysql"
#fi

echo "✔ Test database reset complete"
