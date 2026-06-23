# Test Cases

All cases below were executed against the running build (XAMPP — Apache, PHP 8.2.12,
MariaDB 10.4, database `visit_albay`) and verified in the browser and/or directly in
the database.

| # | Feature | Steps | Expected result | Result |
|---|---------|-------|-----------------|--------|
| 1 | Import schema | Import `sql/schema.sql` | 6 tables created; 6 destinations, 9 hotels, 42 hotel–destination links seeded | **Pass** — counts verified (6 / 9 / 42) |
| 2 | Demo seeding | Load any page | `admin/admin` and `user/user` auto-created with hashed passwords | **Pass** — both present, hash `$2y$10$…` (length 60) |
| 3 | User login | Log in with `user/user` | Session starts; protected pages accessible | **Pass** |
| 4 | Admin login | Log in with `admin/admin` | Admin dashboard accessible | **Pass** |
| 5 | Sign up | Register a new username / password | New user stored with a hashed password | **Pass** — new user created, bcrypt hash (length 60) |
| 6 | Access control | Open `plan.php` / `my-bookings.php` logged out, or `admin.php` as a normal user | Redirected to login / blocked | **Pass** |
| 7 | Stay filtering | Choose a destination on `plan.php` | Only hotels serving that destination remain | **Pass** — Sumlang → its 6 expected stays |
| 8 | Booking validation | Submit a booking with a missing required field | Rejected with a flash message; no row inserted | **Pass** — booking count unchanged |
| 9 | Booking create | Complete a valid booking | Reference code issued; total = price × nights × rooms; appears in My Bookings | **Pass** — e.g. ₱3,800 = ₱1,900 × 2 × 1 |
| 10 | Check-out date | Set check-in + nights | Check-out = check-in + nights (no timezone drift) | **Pass** — Jul 10 + 2 → Jul 12; Aug 1 + 2 → Aug 3 |
| 11 | Payment safety | Book with card details | Only `card_last4` + expiry stored; never full card number or CVV | **Pass** — stored `{card_name, card_last4:"1111", card_expiry}` |
| 12 | Edit request | User requests a booking change | Pending request shown to user and admin; booking unchanged until resolved | **Pass** |
| 13 | Admin approval | Admin approves an edit request | Booking updated; request cleared | **Pass** — guests 2 → 4 |
| 14 | Admin rejection | Admin rejects an edit request | Booking unchanged; request cleared | **Pass** — guests stayed 2 |
| 15 | Admin delete | Admin deletes a booking | Booking and its edit requests removed (FK cascade) | **Pass** — count 1 → 0 |

Totals are recomputed server-side from the hotel's database price (the client value is
not trusted). All forms are CSRF-protected and use PDO prepared statements.
