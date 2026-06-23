# Visit Albay — Tourism Website

A tourism website for Albay, Bicol Region, Philippines. Browse signature destinations
with detailed guides and an interactive map, explore the province by experience, and
book a trip — choosing a stay matched to your destination — that issues a confirmed
reference code. Visitors manage their own bookings and request changes, while an admin
dashboard reviews bookings, approves or rejects edits, and lists registered users.

## Stack

- **PHP 8.x** — server-rendered pages, sessions for login state, CSRF-protected forms
- **MySQL / MariaDB** via XAMPP, accessed through PDO prepared statements
- **Semantic HTML, external CSS, and vanilla JavaScript**
- **Leaflet** (interactive map) loaded from a CDN

## Setup with XAMPP

1. Start **Apache** and **MySQL** in the XAMPP Control Panel.
2. Open `http://localhost/phpmyadmin/` and import `sql/schema.sql`.
3. Place this folder under `htdocs` as `visit-albay`.
4. Open `http://localhost/visit-albay/`.

The two demo accounts are created automatically on first load:

| Role  | Username | Password |
|-------|----------|----------|
| Admin | `admin`  | `admin`  |
| User  | `user`   | `user`   |

## Pages

| Page | Purpose |
|------|---------|
| `index.php` | Landing page — hero, destinations, experiences, reviews, interactive map |
| `destinations.php` | Destination guide with category filters and detail modals |
| `experiences.php` | Three ways to experience Albay + a best-season guide |
| `plan.php` | Booking form (login required) with a destination-matched stay picker |
| `login.php` | Login and sign-up |
| `my-bookings.php` | A user's bookings and change requests |
| `admin.php` | Admin booking management, edit approvals, and user list |

## Database

Six tables: `users`, `destinations`, `hotels`, `hotel_destinations`, `bookings`,
`edit_requests`. Destinations and hotels are normalized into their own tables and
linked many-to-many through `hotel_destinations`. Bookings reference users,
destinations, and hotels via foreign keys; edit requests cascade-delete with their
booking. Booking totals are computed server-side from the hotel's stored price, and
card payments save only the last four digits and expiry — never the full number or CVV.

See `docs/er-diagram.md` for the ER diagram and `docs/test-cases.md` for test results.
