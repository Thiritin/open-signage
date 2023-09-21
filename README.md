![Banner Open Signage](https://banners.beyondco.de/Open%20Signage.png?theme=light&packageManager=&packageName=&pattern=architect&style=style_1&description=Unleash+Dynamic+Signage&md=1&showWatermark=0&fontSize=100px&images=information-circle)

![GitHub issues](https://img.shields.io/github/issues/thiritin/open-signage)
![GitHub pull requests](https://img.shields.io/github/issues-pr/thiritin/open-signage)
![GitHub](https://img.shields.io/github/license/thiritin/open-signage)
![GitHub release (latest by date)](https://img.shields.io/github/v/release/thiritin/open-signage)
![GitHub Workflow Status](https://img.shields.io/github/workflow/status/thiritin/open-signage/CI)
![GitHub contributors](https://img.shields.io/github/contributors/thiritin/open-signage)
![GitHub last commit](https://img.shields.io/github/last-commit/thiritin/open-signage)
![GitHub commit activity](https://img.shields.io/github/commit-activity/m/thiritin/open-signage)
![GitHub top language](https://img.shields.io/github/languages/top/thiritin/open-signage)
![GitHub code size in bytes](https://img.shields.io/github/languages/code-size/thiritin/open-signage)
![GitHub repo size](https://img.shields.io/github/repo-size/thiritin/open-signage)
![GitHub All Releases](https://img.shields.io/github/downloads/thiritin/open-signage/total)
# Open Signage

Open Signage is a digital signage solution built on Laravel, Inertia.js, and Vue.js. This platform serves webpages for digital signage screens, running on Chrome in kiosk mode. Utilizing Socketi, Open Signage dynamically updates data on screens. Users can create playlists for their screens, allowing for rotating announcements and various media presentations.

## Features

- Dynamic data update with Socketi
- Playlist creation for rotating announcements
- Operates with Chrome in kiosk mode

## Prerequisites

- PHP 8.1 or higher
- Node.js & npm/yarn
- Composer
- Laravel

## Installation

1. Clone the repository:

```
git clone https://github.com/thiritin/open-signage.git
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
./vendor/bin/sail up
```

Or use 

2. Start vite dev mode:

```
vite
```

Open Signage should now be accessible at `http://localhost`.

## Contributing

We welcome contributions from everyone. Please read our [Contributing Guidelines](CONTRIBUTING.md) before submitting a pull request or issue.

## License

This project is open-source and is licensed under the [GNU General Public License v2.0](LICENSE.md).

## Contact

If you have any questions, feel free to [create an issue](https://github.com/thiritin/open-signage/issues/new) or contact the project maintainers.
