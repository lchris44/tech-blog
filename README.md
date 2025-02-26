# Laravel-Based Blog Application

This is a Laravel 12 and Vue 3-based Single Page Application (SPA) for managing a blog with localization support. The application features a public blog section, an authenticated admin panel for managing post, tags, users and a REST API for fetching posts with filtering and pagination.

## Features

- **Localization**: Users can switch locales via a navbar dropdown.
- **Authentication**: Users can log in to add, modify posts, and manage tags.
- **Media Upload**: Upload covers for blog posts.
- **Responsive Design**: Fully optimized for different screen sizes.
- **Dark Mode & Theme Customization**: Users can select a preferred theme color and toggle dark mode.
- **API for Posts**: Fetch all posts with optional tag-based filtering and pagination.

## Tech Stack

- **Backend**: Laravel 12
- **Frontend**: Vue 3 (SPA with Inertia.js 2)
- **Database**: MySQL
- **Authentication**: API-based auth with Inertia.js
- **Localization**: spatie/laravel-translatable & vue-i18n
- **UI Components**: PrimeVue, Tailwind CSS
- **Containerization**: Laravel Sail (Docker-based environment)

## Installation

### Prerequisites
- Docker, PHP 8.2+ and Composer

### Steps

1. **Clone the repository:**
   ```sh
   git clone https://github.com/lchris44/tech-blog.git
   cd tech-blog
   ```

2. **Set up environment variables:**
   ```sh
   cp .env.example .env
   ```

3. **Install dependencies:**
   ```sh
   composer install
   ```  

4. **Start Laravel Sail:**
   ```sh
   ./vendor/bin/sail up -d
   ```

5. **Run migrations and seed data:**
   ```sh
   ./vendor/bin/sail artisan migrate --seed
   ```

**Note:** This will create a default admin user:
- **Email:** `admin@tech.com`
- **Password:** `secret`

6. **Create storage symlink:**
   ```sh
   ./vendor/bin/sail artisan storage:link
   ```

7. **Generate the application key:**
   ```sh
   ./vendor/bin/sail artisan key:generate
   ```

8. **Install frontend dependencies:**
   ```sh
   ./vendor/bin/sail npm install
   ```

9. **Build frontend assets:**
   ```sh
   ./vendor/bin/sail npm run build
   ```

10. **Access the application:**
   Open `http://localhost:8080` in your browser.

## API Usage

The API is throttle-protected to prevent abuse and features caching for faster results. It also supports locale switching.

### Fetch all posts with optional tag filtering:
```sh
GET /api/{locale}/v1/posts?tags=tag1,tag2&page=1
```
- **locale** (required): Locale code for the translations in the results (e.g., en, it).
- **tags** (optional): Comma-separated list of tags to filter posts.
- **page** (optional): Pagination page number. (Default pagination is set to 10)

Examples:
```sh
GET /api/en/v1/posts?tags=tag1,tag2&page=1
```
will return English results.

```sh
GET /api/it/v1/posts?tags=tag1,tag2&page=1
```
will return Italian results.

Example response:
```json
{
  "data": [
    {
    "id":1,
    "title":"Aut accusantium quae molestiae.",
    "short_description":"Maiores omnis hic velit alias neque fugiat fugiat. Fuga ea alias quia voluptates eum consequatur rem. Deleniti velit quaerat eos sequi magnam cupiditate qui.",
    "content":"Itaque consequuntur et esse voluptatem cupiditate. Sint suscipit ut dolor eos consectetur et. Animi quibusdam praesentium natus ut dolorem iure.",
    "cover":"https://via.placeholder.com/800x400.png/00ee55?text=technology+nesciunt",
    "user":{
        "id":2,
        "full_name":"Marlen Heaney"
    },
    "tags":[
        {
            "id":4,
            "name":"laboriosam"
        },
        {
            "id":1,
            "name":"jusyd"
        }
    ],
    "created_at": "2025-02-25 18:27:25",
    "updated_at": "2025-02-25 18:27:25"
    }
  ],
  "links": {
    "first": "http://localhost/api/en/v1/posts?page=1",
    "last": "http://localhost/api/en/v1/posts?page=3",
    "prev": null,
    "next": "http://localhost/api/en/v1/posts?page=2"
  },
  {
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 3,
    "links": [
      {
        "url": null,
        "label": "« Previous",
        "active": false
      },
      {
        "url": "http://localhost/api/en/v1/posts?page=1",
        "label": "1",
        "active": true
      },
      {
        "url": "http://localhost/api/en/v1/posts?page=2",
        "label": "2",
        "active": false
      },
      {
        "url": "http://localhost/api/en/v1/posts?page=3",
        "label": "3",
        "active": false
      },
      {
        "url": "http://localhost/api/en/v1/posts?page=2",
        "label": "Next »",
        "active": false
      }
    ],
    "path": "http://localhost/api/en/v1/posts",
    "per_page": 10,
    "to": 10,
    "total": 21
  }
}
```

## Testing

The application includes tests for both the API and the main controllers to ensure reliability and correctness.

To run tests:

```sh
   ./vendor/bin/sail artisan test
   ```

## Design Decisions

- **Inertia.js for SPA**: Eliminates API layer complexity while keeping Laravel routing.
- **spatie/laravel-translatable**: Simplifies localization handling for post content and tags.
- **PrimeVue & Tailwind CSS**: Provides a modern, flexible UI with minimal customization.
- **Dark Mode & Theme Selector**: Enhances UX by allowing users to personalize the UI.
- **RESTful API**: Allows external clients to fetch blog posts efficiently with filtering.

## License
MIT License.

