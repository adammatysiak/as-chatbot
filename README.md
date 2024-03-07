## AS Chatbot

This project is a demo for Pizza Talk for Amsterdam Standard company.

The aim of the project is to show how to use ChatGPT's assistants and how easily you can create a simple chatbot experience.

Installation:
1. `composer install`
2. `npm install & npm run build`
3. `mv .env.example .env` i zaktualizować klucze OpenAI
4. `php artisan migrate`
5. skopiować pliki bazy wiedzy do `storage/app/wiki`
6. `php artisan gpt:create_assistant`
7. `php artisan serve`

Creator: Adam Matysiak, @adammatysiak
