<?php

$databaseUrl = getenv('DATABASE_URL');

$conn = pg_connect($databaseUrl . " sslmode=require");

if (!$conn) {
    die("Erro na conexão com o banco.");
}
