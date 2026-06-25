# Visit Albay - Tourism Website

A tourism website for Albay, Bicol Region, Philippines. Browse signature destinations
with detailed guides and an interactive map, explore the province by experience, and
book a trip with a stay matched to your destination that issues a confirmed reference
code. Visitors manage their own bookings, request changes, and request cancellations,
while an admin dashboard reviews bookings, approves or rejects edit and cancellation
requests, and lists registered users.

## Stack

- **PHP 8.x**: server-rendered pages, sessions for login state, CSRF-protected forms
- **MySQL / MariaDB** via XAMPP, accessed through PDO prepared statements
- **Semantic HTML, external CSS, and vanilla JavaScript**, including a fetch-based
  availability calendar
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

### Running with the PHP built-in server (optional)

You can also run the site without Apache, but MySQL must still be running (start it
from XAMPP). From the folder that contains `index.php`:

```bash
php -S localhost:8000
```

Then open `http://localhost:8000/`. Use the full URL with `http://` and the port,
not just `index.php`.

## Pages

| Page | Purpose |
|------|---------|
| `index.php` | Landing page: hero, destinations, experiences, reviews, interactive map |
| `destinations.php` | Destination guide with category filters and detail modals |
| `experiences.php` | Three ways to experience Albay plus a best-season guide |
| `plan.php` | Booking form (login required) with a destination-matched stay picker and availability calendar |
| `login.php` | Login and sign-up |
| `my-bookings.php` | A user's bookings, change requests, and cancellation requests |
| `admin.php` | Admin booking management, edit and cancellation approvals, and user list |

## Action endpoints

Form posts and AJAX calls live under `actions/`:

| Endpoint | Purpose |
|----------|---------|
| `login.php`, `signup.php` | Authentication |
| `booking_create.php` | Create a booking with a generated reference code |
| `booking_edit.php` | Submit a change request on an existing booking |
| `booking_cancel.php` | Submit a cancellation request |
| `get_availability.php` | Returns booked date ranges as JSON for the calendar |
| `admin_booking.php`, `admin_edit.php` | Admin review of bookings and edit requests |
| `admin_cancel.php` | Admin approve or reject a cancellation request |

## Database

Eight tables: `users`, `peak_seasons`, `destinations`, `hotels`,
`hotel_destinations`, `bookings`, `edit_requests`, and `cancellation_requests`.
Destinations and hotels are normalized into their own tables and linked many-to-many
through `hotel_destinations`. Bookings reference users, destinations, and hotels via
foreign keys. Edit requests and cancellation requests cascade-delete with their
booking. `peak_seasons` drives the best-season guidance shown on the experience page.
Booking totals are computed server-side from the hotel's stored price, and card
payments save only the last four digits and expiry, never the full number or CVV.

See `docs/er-diagram.md` for the ER diagram, `docs/test-cases.md` for test results,
and `docs/content-reference.md` and `docs/sources-and-references.md` for content
attribution.