#!/bin/bash

#!/bin/bash

git pull
docker compose up -d --build
docker exec couple-balance-app php artisan optimize:clear
