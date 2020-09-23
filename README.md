quadcode-test-uri-minifier
====================

Задание:
--------
Сервис uri minifier - минимизирует ссылки путем преобразования их на домен сервиса от 3х до 6ти символов в пути (example.com/abc etc)
Путь должен состоять только из цифробуквенной последовательности нижнего регистра
Должен предоставлять HTTP API
 - создать минимизированную ссылку для uri
 - при переходе по минимизрованной ссылке перенаправлять пользователя на оригинальный uri с кодом 301
 - получить кол-во обращений к ссылке
 - получить кол-во доступных (свободных) ссылок для каждой разрядности (от 3х до 6ти символов в пути)
Ограничения:
 - для оригинального uri может быть только одна минимизированная ссылка
Будет плюсом решение состояния гонки (параллельные запросы на создание ссылки)

### Для запуска необходим docker и docker-compose

для удобства запуска используйте Makefile

#### для сборки (инициализации) проекта
make init

Для удобства добавлен файл в корне проекта test.http с примерами запросов.

##### POST

```
POST http://127.0.0.1:8088/shorturl/generate
accept: application/json
Content-Type: application/json

{
    "url": "https://github.com/i4erkasov/quadcode-test-uri-minifier"
}
```
- Ответ:
```json
    {
        "status": "success",
        "data": {
            "id": 2,
            "code": "00g",
            "url": "https:\/\/github.com\/i4erkasov\/quadcode-test-uri-minifier",
            "short_url": "http:\/\/127.0.0.1:8088\/00g"
        }
    }
```

```
Получить кол-во доступных (свободных) ссылок для каждой разрядности
{length} - длина кода (Oбязательные параметр)

GET http://127.0.0.1:8088/shorturl/limits/{length} 
accept: application/json
```
- Ответ:
```json
    {
        "status": "success",
        "data": {
            "limit": 46655
        }
    }
```

```
Получить кол-во обращений к ссылке
{page} - номер страници (по умолчанию равен 1. Не обязательные параметр) 
{limit} - количество записей на странице (по умолчанию равен 10. Не обязательные параметр)

GET http://127.0.0.1:8088/statistic/redirect?page={page}&limit={limit} 
accept: application/json
```
- Ответ:
```json
    {
        "status": "success",
        "data": {
            "items": [
                {
                    "id": 2,
                    "redirect": 1,
                    "url": "https:\/\/github.com\/i4erkasov\/quadcode-test-uri-minifier",
                    "short_url": "http:\/\/127.0.0.1:8088\/cme"
                },
                {
                    "id": 3,
                    "redirect": 1,
                    "url": "https://github.com/i4erkasov/quadcode-test-uri-minifier/blob/master/README.md",
                    "short_url": "http:\/\/127.0.0.1:8088\/0k8"
                }
            ]
        }
    }
```

```
Получить кол-во обращений к ссылке
{id} - id short_url. (Oбязательные параметр)

GET http://127.0.0.1:8088/statistic/redirect/{id} 
accept: application/json
```
- Ответ:
```json
    {
        "status": "success",
        "data": {
            "id": 2,
            "code": "cme",
            "redirect": 1,
            "url": "https:\/\/github.com\/i4erkasov\/quadcode-test-uri-minifier",
            "short_url": "http:\/\/127.0.0.1:8088\/cme"
        }
    }
```