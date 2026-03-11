#!/bin/bash

#!/bin/bash

git pull
docker compose up -d --build
docker exec couple-balance-app php artisan optimize:clear
docker exec couple-balance-app php artisan cache:clear
docker exec couple-balance-app php artisan view:clear
docker exec couple-balance-app php artisan route:clear
