# Maki et Lotfi - Application Full Stack

Ce projet contient une application full stack avec un backend Laravel et un frontend React.

## Structure du projet

```
maki_et_lotfi_laravel/
├── back maki_et_lotfi/     # Backend Laravel avec JWT et Swagger
└── frontend maki_et_lotfi/ # Frontend React
```

## Backend (Laravel)

Le backend est une API REST construite avec Laravel 12, incluant:
- Authentification JWT
- Documentation Swagger/OpenAPI
- Gestion des utilisateurs et des notes
- Middleware d'authentification et d'administration

### Démarrer le backend

```bash
cd "back maki_et_lotfi"
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

L'API sera disponible sur `http://localhost:8000`

Documentation Swagger: `http://localhost:8000/api/documentation`

## Frontend (React)

Le frontend est une application React avec:
- Authentification utilisateur
- Gestion de profil
- Interface utilisateur moderne

### Démarrer le frontend

```bash
cd "frontend maki_et_lotfi"
npm install
npm start
```

L'application sera disponible sur `http://localhost:3000`

## Technologies utilisées

### Backend
- Laravel 12
- PHP 8.2+
- JWT Authentication
- Swagger/OpenAPI
- MySQL

### Frontend
- React 19
- React Router
- Axios
- CSS moderne

## Auteurs

- Lotfi
- Maki
