composer install 

cp .env.example .env

cd oss

composer install
//配置电魂 composer 
composer config secure-http false
composer config repo.dianhun composer http://192.168.110.234:8080/svn/composer
composer require linearsoft/composer-svn-export
composer require dianhun/ams ~0.2.0