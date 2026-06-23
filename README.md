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

Open Signage is a **framework for building digital‑signage designs**, built on
Laravel, Inertia.js and Vue.js. You write your screens as Vue components, preview
them instantly in the browser with mock data, and the platform handles
playlists, scheduling, live updates (via Laravel Reverb) and kiosk delivery to
Chrome.

It ships with:

- A generic **Starter** project you can copy to bootstrap your own designs.
- An **auto‑fit + scale‑to‑fit grid system** so designs fit any screen
  resolution — never larger than the screen — without hand‑tuning.
- A **`/preview` workflow** to render any design standalone with mock data — no
  database, playlist or websocket server required.
- **Seeders** that produce a working demo screen on a fresh clone.

> Building a real deployment? Keep your event-/customer‑specific designs in their
> own repo and drop the project folder into `resources/js/Projects/`. The
> framework stays generic. For commercial support, contact me@thiritin.com.

## Features

- Auto‑fit / scale‑to‑fit grid + block layout system
- Standalone design preview with mock data (`/preview`)
- Live data updates over websockets (Laravel Reverb)
- Playlists, scheduling and rooms managed from a Filament admin
- Runs full‑screen in Chrome kiosk mode

## Prerequisites

- PHP 8.2 or higher
- Node.js & npm
- Composer

## Quick start

```bash
git clone https://github.com/thiritin/open-signage.git
cd open-signage

composer install
npm install

cp .env.example .env
php artisan key:generate

# Schema + a working demo (a "demo" screen, Starter playlist, rooms, schedule):
php artisan migrate --seed

php artisan serve     # http://localhost:8000  (or ./vendor/bin/sail up)
npm run dev           # Vite dev server with hot reload
```

Then open:

- `http://localhost:8000/preview` — preview designs with mock data
- `http://localhost:8000/screens/demo` — the seeded demo screen
- `http://localhost:8000/admin` — admin (`me@thiritin.com` / `password`)

**👉 Full developer guide: [`docs/DEVELOPMENT.md`](docs/DEVELOPMENT.md)** —
how the grid system works, the preview workflow, and how to create your own
project.

## Contributing

We welcome contributions from everyone. Please read our [Contributing Guidelines](CONTRIBUTING.md) before submitting a pull request or issue.

## License

This project is open-source and is licensed under the [GNU General Public License v2.0](LICENSE).

## Contact

If you have any questions, feel free to [create an issue](https://github.com/thiritin/open-signage/issues/new) or contact the project maintainers.
