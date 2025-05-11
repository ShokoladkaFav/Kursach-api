# Вихідне PHP-середовище
FROM php:8.2-cli

# Встановити розширення mysqli (для підключення до MySQL)
RUN docker-php-ext-install mysqli

# Встановити директорію як робочу
WORKDIR /app

# Скопіювати всі файли в контейнер
COPY . .

# Відкрити порт
EXPOSE 10000

# Команда запуску
CMD ["php", "-S", "0.0.0.0:10000", "-t", "public"]
