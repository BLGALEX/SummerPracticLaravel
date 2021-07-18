# Описание и гайд по запуску проекта.

## Был разрабтан REST API в соответствии со следующим заданием:

Разработать REST API для каталога интернет-магазина с использованием PHP фреймворка Laravel. Тематика магазина – на ваш выбор (ниже будут предоставлены варианты, но можете использовать свой). Реализуемое API должно предоставлять набор методов для: 
получения информации о товарах каталога с возможностью фильтрации по категории
корзины (просмотр/добавление/изменение/удаление товаров)
оформления заказа

### Этапы разработки

#### 1. Проектирование базы данных (1 балл)
С помощью миграций (https://laravel.com/docs/migrations) и Eloquent ORM (https://laravel.com/docs/eloquent) спроектировать базу данных и модели для будущего интернет-магазина.
Рекомендуемая структура БД:
1) Таблица категорий (название)
2) Таблица товаров (название, описание, цена, категория, различные характеристики)
3) Таблица заказов (контактные данные пользователя, список товаров)
4) Промежуточные таблицы для связей многие ко многим, если понадобятся

#### 2. Парсинг данных с существующего сайта (2 балла)
Реализовать скрипт (https://laravel.com/docs/artisan#generating-commands) для парсинга информации о товарах и категориях с существующего сайта.
Парсить данные можно с помощью регулярных выражений. 
Используемый сайт – www.muztorg.ru
Страница для парсинга – https://www.muztorg.ru/search/%D0%B3%D0%B8%D1%82%D0%B0%D1%80%D0%B0?in-stock=1&pre-order=1&per-page=48

#### 3. Разработка API (2 балла)
Разработать REST JSON API (https://habr.com/ru/post/447322) для каталога.
Для тестирования и документирования API использовался Postman.

Рекомендуемый набор методов:
1) GET /products
Метод для получения списка товаров. Должен поддерживать фильтрацию по категории (/products?category={id}).
2) GET /products/{id}
Метод для получения товара по ID.
3) GET /cart
Метод для получения списка товаров из корзины. Вместе со списком товаров нужно возвращать количество и итоговую стоимость товаров в корзине.
4) POST /cart/add
Метод для добавления товара в корзину. ID добавленных товаров и их количество хранить в сессии пользователя.
5) POST /cart/update
Метод для изменения количества товара в корзине.
6) POST /cart/delete
Метод для удаления товара из корзины.
7) POST /cart/submit
Метод для оформления заказа. Должен принимать контактные данные пользователя и сохранять их вместе с товарами из корзины в базе данных.


## Загрузка и запуск проекта на Windows 10.

Для клонирвания проекта с репозитория рекомендуется использовать phpStorm.

#### 1) Cкачать и установить следующее:

PHP 8 – https://www.php.net/downloads.php  (не забываем добавить переменную в path)

PhpStorm – https://www.jetbrains.com/phpstorm/download/

Composer – https://getcomposer.org/download/

MAMP – https://www.mamp.info/en/downloads/

MySQL – https://dev.mysql.com/downloads/installer/

#### 2) Запуск MAMP и PHPStorm:

Запускаем PHPStorm и клонируем проект с этого репозитория. Заходим в терминал в проекте, переходим в папку "music1" и пишем команду:

> composer update

Должна появиться папка vendor. 
Теперь зпускаем MAMP и жмём "start servers" (галочка около mysql server должна стать зелёной), затем жмём "open webpage" (http://localhost/MAMP/)
и нас должно перекинуть на страницу, на которой можно увидеть табличку с данными следующего типа:

![image](https://user-images.githubusercontent.com/58458024/126075884-71c96a54-32fb-4fbd-9d25-27c7439e4626.png)

Находим в папке проекта файл .env и убеждаемся что следующие переменные соответствуют таблице, например:

![image](https://user-images.githubusercontent.com/58458024/126075896-3a6c79c1-9764-48e8-9637-b1881d47911e.png)

После этого переходим по ссылке "phpMyAdmin" (http://localhost/MAMP/index.php?page=phpmyadmin&language=English), которая есть на странице MAMP. 

#### 3) Создание базы данных, миграция и парсинг:

Теперь на странице phpmyAdmin жмем на создание новой базы данных и указываем её имя. Теперь это имя нужно будет прописать в файле .env, в переменную:
DB_DATABASE=music_store (имя, которое указал я при создании)
Возвращаемся в терминал и пишем команды:

> php artisan migrate

> php artisan command:parser

Для выполнения второй команды может понадобиться некоторое время, в процессе парсинга каждого товара со страницы магазина будет выведен лог в консоль. Должно быть добавлено около 40 товаров.

#### 4) Запуск:

Для запуска локального сервера пишем команду в консолипроекта:

> php artisan serve

Теперь можно проверять все запросы из задания. С тем замечанием, что они будут иметь префикс /api/ например:

GET http://127.0.0.1:8000/api/products

GET http://127.0.0.1:8000/api/products?category=akusticheskie-gitary

GET http://127.0.0.1:8000/api/cart

## Структура базы данных

#### диаграмма базы данных

![db](https://user-images.githubusercontent.com/58458024/126083384-85349bf8-77b6-4d18-86d1-65cdfd475928.png)

categories – сущность включающая в себя id и названия всех категорий добавленных товаров.


products – сущность товара, который относится к какой либо категории. Содержит следующие поля:

id – идентефикатор

name – наименование товара (строковый тип)

price – стоимость товара в рублях (целочисленный тип)

picture – ссылка на изображение товара (строковый тип)

category_id – id категории, которой принадлежит товар

characteristics – характеристики товара (текстовый тип, может быть NULL, так как не все товары имеют характеристики)


cart – сущность корзины, содержит следующие поля:

id – идентефикатор

ip – ip пользователя, который создал корзину

email, phone – почта и телефон пользователя, добаляется только после оформления заказа (submit)

submited – поле отражающее была ли корзина оформлена или пользователь всё ещё добавяет/удаляет товары. Если false, то значит заказ ещё не оформлен, если true, то корзина является оформленной и к ней обращения больше не происходят. Для пользователя с таким же ip будут создаваться новая корзина со значением false.


products_lists – промежуточная сущность для решения связи многие ко многим между товарами и корзинами. Поля:

product_id – id товара

cart_id – id корзины,  в которой он лежит

count – количество единиц товара

