---
layout: default
title: Getting Started Guide
---

# Getting Started with Open Signage

## Introduction

In the current state, Open Signage is not an out-of-the-box solution. It targets developers who are willing to create custom Vue layouts and pages for their event and then use Open Signage to manage and display those Vue layouts.

As a developer, you don't need to worry about data updates, PHP, Laravel, or anything similar. You can use some [preset properties](/preset-properties/) or create your own, which users can fill in on a Playlist. For example, an `Announcement.vue` Component could have a title and content.

## Developer Setup

To get started, you'll need the following prerequisites:

- Docker
- PHP 8.2 & Composer locally installed
- NPM & Node.js locally installed

### Clone the Project

```bash
git clone https://github.com/Thiritin/open-signage
```
### Copy .env.example to .env

You don't need to edit anything; EF29 is set by default.

```bash
cp .env.example .env
```

### Install Composer Dependencies

```bash
composer install
```

### Install NPM Dependencies

```bash
npm install
```

### Run Sail
It may take a few minutes to fully build the containers.

```bash
./vendor/bin/sail up -d
```

### Migrate & Seed the Database

```bash
./vendor/bin/sail migrate --seed
```

### Run Vite

```bash
vite
```

### Access the Admin
The admin interface is only seeded if APP_ENV=local is set in the .env file.

| Property     | Value                  |
|--------------|------------------------|
| **URL**      | http://localhost/admin |
| **User**     | me@thiritin.com        |
| **Password** | password               |
