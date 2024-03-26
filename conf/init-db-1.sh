#!/bin/bash
set -e

echo '##########'
echo 'Database configuration script part-1'
env
echo '##########'

mysql -h "$1" -u root -ppassword <<-EOSQL
    DROP DATABASE test;
    CREATE DATABASE test;
    USE test;

    CREATE TABLE Player (
        id BIGINT UNSIGNED NOT NULL,
        email varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
        password varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
        createdAt DATETIME NOT NULL,
        updatedAt DATETIME NOT NULL,
        PRIMARY KEY(id));

    CREATE TABLE PlayerToken (
        id BIGINT UNSIGNED NOT NULL,
        playerId BIGINT UNSIGNED NOT NULL,
        playerToken VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
        active tinyint(1) NOT NULL DEFAULT 0,
        createdAt DATETIME NOT NULL,
        updatedAt DATETIME NOT NULL,
        PRIMARY KEY(id));

    CREATE TABLE Provider (
        id BIGINT UNSIGNED NOT NULL,
        name varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
        apiKey varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        secretKey varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        active tinyint(1) NOT NULL DEFAULT 0,
        createdAt DATETIME NOT NULL,
        updatedAt DATETIME NOT NULL,
        PRIMARY KEY(id));

    CREATE TABLE Transaction (
        id BIGINT UNSIGNED NOT NULL,
        playerId BIGINT UNSIGNED DEFAULT NULL,
        providerId BIGINT UNSIGNED NOT NULL,
        amount BIGINT UNSIGNED NOT NULL,
        externalTransactionId varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        status varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
        createdAt DATETIME NOT NULL,
        updatedAt DATETIME NOT NULL,
        PRIMARY KEY(id));

    CREATE TABLE TransactionCascade (
        id BIGINT UNSIGNED NOT NULL,
        primalTransactionId BIGINT UNSIGNED NOT NULL,
        currentTransactionId BIGINT UNSIGNED NOT NULL,
        status varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
        createdAt DATETIME NOT NULL,
        updatedAt DATETIME NOT NULL,
        PRIMARY KEY(id));
EOSQL
