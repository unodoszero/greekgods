# GreekGods

![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?logo=php&logoColor=white)
![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?logo=laravel&logoColor=white)
![PostgreSQL](https://img.shields.io/badge/Database-PostgreSQL-4169E1?logo=postgresql&logoColor=white)
![Supabase](https://img.shields.io/badge/Backend-Supabase-3FCF8E?logo=supabase&logoColor=white)
![Vite](https://img.shields.io/badge/Build-Vite-646CFF?logo=vite&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Styles-Tailwind_CSS-06B6D4?logo=tailwindcss&logoColor=white)
![Vercel](https://img.shields.io/badge/Deploy-Vercel-000000?logo=vercel&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-yellow)

GreekGods is a Laravel fitness planning application for creating weekly workout programs, managing personal body metrics, and estimating training-related nutrition targets. The project began as a PHP fitness website and was migrated into a Laravel application with Blade views, session authentication, PostgreSQL persistence, OAuth login, and Vercel deployment support.

## Table of Contents

- [Preview](#preview)
- [Tech Stack](#tech-stack)
- [Repository Overview](#repository-overview)
- [Features](#features)
- [Installation Guide](#installation-guide)
- [Usage](#usage)
- [Migration and Domain Logic](#migration-and-domain-logic)
- [Constraints and Future Improvements](#constraints-and-future-improvements)
- [License](#license)

## Preview

| Preview                                                                                                                                                   | Description                                                                                             |
| --------------------------------------------------------------------------------------------------------------------------------------------------------- | ------------------------------------------------------------------------------------------------------- |
| <img src="public/preview/Screenshot%202026-07-03%20at%204.28.35%E2%80%AFPM.png" alt="GreekGods home page" width="360">                                    | Home page with authenticated navigation and a monochrome fitness hero visual.                           |
| <img src="public/preview/FireShot%20Capture%20018%20-%20GreekGods%20-%20Profile%20-%20%5B127.0.0.1%5D.png" alt="GreekGods profile dashboard" width="360"> | Profile dashboard showing saved body metrics, BMI, calorie targets, and today's workout.                |
| <img src="public/preview/Screenshot%202026-07-03%20at%204.31.06%E2%80%AFPM.png" alt="GreekGods weekly workout board" width="360">                         | Program builder with split selection, weekly schedule mapping, rest-day locking, and editable workouts. |

## Tech Stack

| Technology        | Role                                                                                                          |
| ----------------- | ------------------------------------------------------------------------------------------------------------- |
| PHP 8.3           | Main server-side language for the Laravel application and migration scripts.                                  |
| Laravel 13        | Application framework for routing, controllers, validation, sessions, Eloquent models, migrations, and tests. |
| Blade             | Server-rendered view layer for the app layout, auth screens, profile dashboard, and program builder.          |
| JavaScript        | Powers interactive profile and workout-program behavior on the client side.                                   |
| Tailwind CSS 4    | Styles the Laravel views, legacy pages, and Vite-managed frontend assets.                                     |
| PostgreSQL        | Primary relational database for users, body metrics, programs, workouts, sessions, cache, and jobs.           |
| Supabase          | Hosted PostgreSQL target used by the documented deployment setup.                                             |
| Laravel Socialite | Google and Microsoft OAuth login, account linking, and social signup flow.                                    |
| Vite              | Frontend build tool for compiling application CSS and JavaScript assets.                                      |

## Features

- Email/password registration and login with Laravel session authentication.
- Google and Microsoft OAuth login with account linking and a profile-completion step for new social signups.
- Profile dashboard with height, weight, age, activity level, sex, BMI, BMR, TDEE, calorie targets, and protein estimates.
- Metric conversion for height and weight across `cm`, `m`, `in`, `ft`, `kg`, and `lb`.
- Workout split catalog covering full body, upper/lower, PPL, PPL/Upper/Lower, PPL/Upper/ShArms, Arnold, and body-part splits.
- Weekly program builder with schedule selection, preconfigured workout templates, editable workouts, and rest-day restrictions.
- Legacy URL redirects for previous `/files/*.php`, `/files/*.html`, `/articles/*.php`, and `index.php` routes.
- Supabase PostgreSQL configuration and Vercel deployment support.

## Usage

To use the web app navigate to the repository 'about' panel and click the link.

## Constraints and Future Improvements

- Some legacy PHP, CSS, and JavaScript assets remain in the repository while the Laravel migration is in progress.
- The preview images are stored as static screenshots rather than generated from an automated visual test workflow.
- Workout recommendations are rule/template based; future versions could support progression history, set completion tracking, and analytics.
- The current database target is PostgreSQL/Supabase; local SQLite setup is not the primary documented path.
- Additional tests would improve confidence around social auth edge cases, profile metric validation, and workout schedule constraints.
- CI/CD could be added to run tests, formatting, and build checks before deployment.

## License

This project is licensed under the [MIT License](LICENSE).
