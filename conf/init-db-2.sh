#!/bin/bash
set -e

echo '##########'
echo 'Database configuration script part-2'
env
echo '##########'

mysql -h "$1" -u root -ppassword <<-EOSQL
    USE test;

    CREATE TABLE TransactionDetails (
      id BIGINT UNSIGNED NOT NULL,
      transactionId BIGINT UNSIGNED NOT NULL,
      details varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
      createdAt DATETIME NOT NULL,
      updatedAt DATETIME NOT NULL,
      PRIMARY KEY(id));

    CREATE TABLE TransactionDetailsKey (
      id BIGINT UNSIGNED NOT NULL,
      transactionId BIGINT UNSIGNED NOT NULL,
      decryptKey varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
      createdAt DATETIME NOT NULL,
      updatedAt DATETIME NOT NULL,
      PRIMARY KEY(id));

    CREATE TABLE ProviderDetails (
      id BIGINT UNSIGNED NOT NULL,
      apiKey varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
      secretKey varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
      providerConfig json DEFAULT NULL,
      createdAt DATETIME NOT NULL,
      updatedAt DATETIME NOT NULL,
      PRIMARY KEY(id));

    INSERT INTO Player
      (id, email, password, createdAt, updatedAt)
    VALUES
      (123, 'john@doe.qu', '123456', now(), now());
EOSQL
