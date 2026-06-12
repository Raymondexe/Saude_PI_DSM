<?php

$url = "postgresql://neondb_owner:npg_LsFwvph8Yn9K@ep-tiny-thunder-ahxteyhw-pooler.c-3.us-east-1.aws.neon.tech/neondb?sslmode=require";

$conn = pg_connect($url);

if (!$conn) {
    die("Erro na conexão com o banco.");
}
?>
