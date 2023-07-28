# Open Signage

Open Signage is a digital signage solution built on Laravel, Inertia.js, and Vue.js. This platform serves webpages for digital signage screens, running on Chrome in kiosk mode. Utilizing Socketi, Open Signage dynamically updates data on screens. Users can create playlists for their screens, allowing for rotating announcements and various media presentations.

## Features

- Dynamic data update with Socketi
- Playlist creation for rotating announcements
- Operates with Chrome in kiosk mode

## Prerequisites

- PHP 7.3 or higher
- Node.js & npm
- Composer
- Laravel

## Installation

1. Clone the repository:

```
git clone https://github.com/your-github-username/open-signage.git
```

2. Navigate into the project directory:

```
cd open-signage
```

3. Install PHP dependencies:

```
composer install
```

4. Install JavaScript dependencies:

```
npm install
```

5. Copy the `.env.example` file to create your own `.env` file:

```
cp .env.example .env
```

6. Set your application key:

```
php artisan key:generate
```

7. Set up your database credentials in the `.env` file.

8. Run database migrations:

```
php artisan migrate
```

## Usage

1. Start the Laravel server:

```
php artisan serve
```

2. Compile the assets:

```
npm run dev
```

Open Signage should now be accessible at `http://localhost:8000`.

## Contributing

We welcome contributions from everyone. Please read our [Contributing Guidelines](CONTRIBUTING.md) before submitting a pull request or issue.

## License

This project is open-source and is licensed under the [GNU General Public License v2.0](LICENSE.md).

## Contact

If you have any questions, feel free to [create an issue](https://github.com/thiritin/open-signage/issues/new) or contact the project maintainers.
