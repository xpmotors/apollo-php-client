<?php
echo "
DB_HOST={$namor['mysql.url']}
DB_PORT={$namor['mysql.port']}
DB_DATABASE={$namor['mysql.db']}
DB_USERNAME={$namor['mysql.user']}
DB_PASSWORD={$namor['mysql.password']}

REDIS_HOST={$namor['redis.url']}
REDIS_PORT={$namor['redis.port']}
REDIS_PASSWORD={$namor['redis.password']}
REDIS_DB={$namor['redis.db']}
";
