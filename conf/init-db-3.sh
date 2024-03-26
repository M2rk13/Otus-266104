#!/bin/bash
set -e

echo '##########'
echo 'Database configuration script part-3'
env
echo '##########'

mysql -h "$1" -u root -ppassword <<-EOSQL
    USE test;

    INSERT INTO Provider
      (id, name, active, createdAt, updatedAt)
    VALUES
      (1, 'one', 1, now(), now()),
      (2, 'two', 1, now(), now()),
      (3, 'three', 1, now(), now()),
      (4, 'four', 1, now(), now()),
      (5, 'five', 1, now(), now()),
      (10, 'cascade', 1, now(), now());

    INSERT INTO ProviderDetails
        (id, apiKey, secretKey, providerConfig, createdAt, updatedAt)
      VALUES
        (1, 'some_key_1', 'some_secret_1', NULL, now(), now()),
        (2, 'some_key_2', 'some_secret_2', NULL, now(), now()),
        (3, 'some_key_3', 'some_secret_3', NULL, now(), now()),
        (4, 'some_key_4', 'some_secret_4', NULL, now(), now()),
        (5, 'some_key_5', 'some_secret_5', NULL, now(), now()),
        (10, NULL, NULL, '{"cascadeList": ["one", "two", "three", "four", "five"]}', now(), now());

    INSERT INTO PlayerToken
      (id, playerToken, playerId, active, createdAt, updatedAt)
    VALUES
      (555, 'ekjnvejknvkjvn', 123, 1, now(), now());
EOSQL
