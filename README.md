# Laravel Vue App with Real-time Features and MoMo Payment

This is a modern web application built with Laravel and Vue.js, featuring real-time functionality using WebSockets and MoMo payment integration.

## Features

- Laravel 10.x backend
- Vue 3 frontend
- JWT Authentication
- Real-time features using WebSockets
- MoMo payment integration
- Docker support
- MySQL database
- Redis for caching and real-time features

## Prerequisites

- Docker and Docker Compose
- Node.js and NPM
- Composer

## Setup

1. Clone the repository:

```bash
git clone <repository-url>
cd laravel-vue-app
```

2. Copy the environment file:

```bash
cp .env.example .env
```

3. Update the `.env` file with your configuration:

- Set your database credentials
- Configure MoMo payment credentials
- Set Pusher credentials for real-time features

4. Build and start the Docker containers:

```bash
docker-compose up -d --build
```

5. Install PHP dependencies:

```bash
docker-compose exec app composer install
```

6. Generate application key:

```bash
docker-compose exec app php artisan key:generate
```

7. Run database migrations:

```bash
docker-compose exec app php artisan migrate
```

8. Install Node.js dependencies:

```bash
npm install
```

9. Build frontend assets:

```bash
npm run build
```

## Running the Application

1. Start the Docker containers:

```bash
docker-compose up -d
```

2. The application will be available at:

- Frontend: http://localhost:8000
- API: http://localhost:8000/api

## Development

1. Start the development server:

```bash
npm run dev
```

2. For real-time development:

```bash
docker-compose exec app php artisan websockets:serve
```

## Testing

Run the test suite:

```bash
docker-compose exec app php artisan test
```

## Security

- All API endpoints are protected with JWT authentication
- MoMo payment integration uses secure webhooks
- Real-time features are authenticated using Pusher

## License

This project is licensed under the MIT License.
