# TicketPulse

TicketPulse is a real-time collaborative Kanban board demonstrating modern Laravel development practices. Built with Laravel 13, Inertia.js, and Vue 3, it showcases a high-quality, maintainable architecture for real-time systems.

## Tech Stack

- **Backend**: PHP 8.4+, Laravel 13, PostgreSQL, Redis, Laravel Reverb, Laravel Sanctum, Laravel Boost, Pest
- **Frontend**: Inertia.js, Vue 3, TypeScript, Pinia, shadcn-vue, VueUse
- **Development**: Docker, Laravel Sail, Laravel Pint, ESLint, Prettier

## Features

- **Real-time Collaboration**: Instant card creation, movement, and updates using Laravel Reverb and Presence Channels.
- **AI-Assisted Workflow**: AI-generated card descriptions, task breakdowns, and comment summaries integrated via Laravel Boost.
- **Activity Feed**: Real-time tracking of all important board actions.
- **Modern UI**: Built with shadcn-vue for a polished, accessible user experience.
- **SOLID & Clean Code**: Adheres to the Action pattern, domain events, and thin controllers.

## Architecture

### Real-time Event Flow
User Action -> Controller -> Action -> Domain Event -> Broadcast Event -> Redis -> Reverb -> Pinia Store -> UI Update

### AI Integration (Laravel Boost)
Dedicated AI actions in `app/Actions/AI` handle card description generation, task breakdown, and comment summarization, demonstrating isolated and maintainable AI integration.

## Installation

```bash
# Clone the repository
git clone https://github.com/yourusername/ticketpulse.git
cd ticketpulse

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Start development environment
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan reverb:start

# Run frontend
npm run dev
```

## Testing

```bash
php artisan test
```

## Screenshots

- **Board View**: [Placeholder]
- **Card Modal**: [Placeholder]
- **Activity Feed**: [Placeholder]
- **Presence Indicators**: [Placeholder]
- **AI Description Generator**: [Placeholder]
