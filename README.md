# Semplice the modern web framework

> Semplice means "simple" in Italiano.
> センプリチェ, sémplitʃèi

## Prerequisites

- PHP >=8.1.0
- node.js ^16.0.0

## Features

### Backend: Modern PHP Framework

- Super zero-dependencies!
    - You need external dependencies only runtime, not framework-side.
- Super Supports PSR!
    - Can use [any PSR-7 HTTP Message implementation](https://packagist.org/providers/psr/http-message-implementation).
    - Super minimal PSR-11 Container implementation inside.
    - Can use [any PSR-15 HTTP Middleware implementation](https://packagist.org/providers/psr/http-server-middleware-implementation).

### Frontend: Modern TypeScript Framework

- Super zero-dependencies!
    - [Web-Components](https://www.webcomponents.org/)-based components.

## Install

[Please see in template repository](https://github.com/il-m-yamagishi/semplice-template).

## Contribute

### Backend: PHP

```bash
$ cd backend
$ composer install
$ composer ci
```

### Frontend: node.js

```bash
$ cd frontend
$ npm install
$ npm run ci
```
