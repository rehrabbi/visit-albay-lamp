<?php
require_once __DIR__ . '/includes/app.php';

require_admin($pdo);

$pageTitle = 'Admin Dashboard - Visit Albay';
$active = 'admin';
$destinations = all_destinations($pdo);

// Create a lookup dictionary to map destination IDs to human-readable names.
// This is required for formatting the 'diff' view in pending edit requests.
$destinationMap = [];
foreach ($destinations as $destination) {
    $destinationMap[(int) $destination['id']] = $destination['name'];
}

// Create a lookup dictionary to map hotel IDs to human-readable names,
// plus a price lookup used to project the new total on edit requests.
$hotelMap = [];
$hotelPriceMap = [];
foreach ($pdo->query('SELECT id, name, price_per_night FROM hotels')->fetchAll() as $hotelRow) {
    $hotelMap[(int) $hotelRow['id']] = $hotelRow['name'];
    $hotelPriceMap[(int) $hotelRow['id']] = (float) $hotelRow['price_per_night'];
}

// Bookings load 50 at a time to keep memory and the DOM bounded as the database
// scales. "Show more" raises the limit via the ?show query parameter; the value is
// clamped to a 50-row step and never below 50.
$bookingsTotal = (int) $pdo->query('SELECT COUNT(*) FROM bookings')->fetchColumn();
$bookingsStep = 50;
$bookingsLimit = max($bookingsStep, (int) ($_GET['show'] ?? $bookingsStep));
$bookingsLimit = (int) (ceil($bookingsLimit / $bookingsStep) * $bookingsStep);

$bookings = $pdo->query(
    'SELECT b.*, u.username AS owner, d.name AS destination_name, h.name AS hotel_name
     FROM bookings b
     JOIN users u ON u.id = b.user_id
     JOIN destinations d ON d.id = b.destination_id
     JOIN hotels h ON h.id = b.hotel_id
     ORDER BY b.id DESC
     LIMIT ' . $bookingsLimit
)->fetchAll();
$bookingsHasMore = $bookingsTotal > count($bookings);

// Fetch all pending edit requests with their original booking context.
$editRequests = $pdo->query(
    "SELECT e.*, b.reference_code, b.full_name, b.destination_id, b.hotel_id, b.check_in_date,
            b.nights, b.guests, b.rooms, b.hotel_total, b.payment_method, b.email, b.phone, b.address,
            b.special_request, u.username AS owner
     FROM edit_requests e
     JOIN bookings b ON b.id = e.booking_id
     JOIN users u ON u.id = b.user_id
     WHERE e.status = 'pending'
     ORDER BY e.id DESC"
)->fetchAll();

// Fetch all pending cancellation requests with their booking context.
$cancelRequests = $pdo->query(
    "SELECT c.*, b.reference_code, b.full_name, b.hotel_total, b.check_in_date,
            d.name AS destination_name, h.name AS hotel_name, u.username AS owner
     FROM cancellation_requests c
     JOIN bookings b ON b.id = c.booking_id
     JOIN destinations d ON d.id = b.destination_id
     JOIN hotels h ON h.id = b.hotel_id
     JOIN users u ON u.id = b.user_id
     WHERE c.status = 'pending'
     ORDER BY c.id DESC"
)->fetchAll();

// Aggregate user statistics, calculating total bookings per user.
$users = $pdo->query(
    'SELECT u.id, u.username, u.role, u.created_at, COUNT(b.id) AS bookings
     FROM users u
     LEFT JOIN bookings b ON b.user_id = u.id
     GROUP BY u.id, u.username, u.role, u.created_at
     ORDER BY u.id ASC'
)->fetchAll();

/**
 * Formats raw database values into human-readable strings for the Admin UI.
 * Specifically converts foreign keys (destination_id, hotel_id) into their actual text names.
 */
function admin_format_value(string $key, $value, array $destinationMap, array $hotelMap): string
{
    if ($value === null || $value === '') {
        return '—';
    }
    if ($key === 'destination_id') {
        return $destinationMap[(int) $value] ?? (string) $value;
    }
    if ($key === 'hotel_id') {
        return $hotelMap[(int) $value] ?? (string) $value;
    }

    return (string) $value;
}

require __DIR__ . '/includes/header.php';
?>
<main class="section">
  <div class="section-head">
    <div>
      <p class="eyebrow">Admin</p>
      <h1>Dashboard</h1>
      <p>Manage bookings, review user edit requests, and inspect registered accounts.</p>
    </div>
  </div>

  <div class="tabs" role="tablist" aria-label="Admin sections" style="margin-top: 2rem; margin-bottom: 2rem;">
    <button class="button is-active" type="button" data-tab-target="bookings">Bookings <?= $bookingsTotal ?></button>
    <button class="button" type="button" data-tab-target="edits">Pending edits <?= count($editRequests) ?></button>
    <button class="button" type="button" data-tab-target="cancellations">Cancellations <?= count($cancelRequests) ?></button>
    <button class="button" type="button" data-tab-target="users">Users <?= count($users) ?></button>
  </div>

  <section class="tab-panel is-active" data-tab-panel="bookings" aria-labelledby="bookings-tab">
    <?php foreach ($bookings as $booking): ?>
      <form id="update-booking-<?= (int) $booking['id'] ?>" action="<?= h(url('actions/admin_booking.php')) ?>" method="post">
        <?= csrf_field() ?>
        <input type="hidden" name="intent" value="update">
        <input type="hidden" name="booking_id" value="<?= (int) $booking['id'] ?>">
      </form>
      <form id="delete-booking-<?= (int) $booking['id'] ?>" action="<?= h(url('actions/admin_booking.php')) ?>" method="post">
        <?= csrf_field() ?>
        <input type="hidden" name="intent" value="delete">
        <input type="hidden" name="booking_id" value="<?= (int) $booking['id'] ?>">
      </form>
    <?php endforeach; ?>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Ref</th>
            <th>Owner</th>
            <th>Traveler</th>
            <th>Destination</th>
            <th>Stay</th>
            <th>Date</th>
            <th>Total</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php if (!$bookings): ?>
          <tr><td colspan="8" class="empty-state">No bookings yet.</td></tr>
        <?php endif; ?>
        <?php foreach ($bookings as $booking): ?>
          <tr>
            <td class="booking-ref"><?= h($booking['reference_code']) ?></td>
            <td><?= h($booking['owner']) ?></td>
            <td>
              <input form="update-booking-<?= (int) $booking['id'] ?>" name="full_name" value="<?= h($booking['full_name']) ?>" aria-label="Full name">
              <input form="update-booking-<?= (int) $booking['id'] ?>" name="email" type="email" value="<?= h($booking['email']) ?>" aria-label="Email">
              <input form="update-booking-<?= (int) $booking['id'] ?>" name="phone" value="<?= h($booking['phone']) ?>" aria-label="Phone">
            </td>
            <td>
              <select form="update-booking-<?= (int) $booking['id'] ?>" name="destination_id" aria-label="Destination">
                <?php foreach ($destinations as $destination): ?>
                  <option value="<?= (int) $destination['id'] ?>" <?= (int) $destination['id'] === (int) $booking['destination_id'] ? 'selected' : '' ?>>
                    <?= h($destination['name']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </td>
            <td><?= h($booking['hotel_name']) ?></td>
            <td>
              <input form="update-booking-<?= (int) $booking['id'] ?>" name="check_in_date" type="date" value="<?= h($booking['check_in_date']) ?>" aria-label="Check-in">
              <label>
                Guests
                <input form="update-booking-<?= (int) $booking['id'] ?>" name="guests" type="number" min="1" max="20" value="<?= (int) $booking['guests'] ?>">
              </label>
            </td>
            <td>
              <?= money($booking['hotel_total']) ?><br>
              <span class="meta"><?= (int) $booking['nights'] ?> night(s), <?= (int) $booking['rooms'] ?> room(s)</span>
            </td>
            <td>
              <div class="inline-actions">
                <button class="button button-ok" form="update-booking-<?= (int) $booking['id'] ?>" type="submit">Save</button>
                <button class="button button-danger" form="delete-booking-<?= (int) $booking['id'] ?>" type="submit" onclick="return confirm('Delete booking <?= h($booking['reference_code']) ?>?')">Delete</button>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php if ($bookingsHasMore): ?>
      <div class="load-more">
        <p class="meta">Showing <?= count($bookings) ?> of <?= $bookingsTotal ?> bookings</p>
        <a class="button button-ghost" href="<?= h(url('admin.php?show=' . ($bookingsLimit + $bookingsStep))) ?>#bookings">Show more</a>
      </div>
    <?php endif; ?>
  </section>

  <section class="tab-panel" data-tab-panel="edits">
    <?php if (!$editRequests): ?>
      <div class="panel empty-state">No pending edit requests.</div>
    <?php else: ?>
      <div class="booking-list">
        <?php foreach ($editRequests as $request): ?>
          <?php
          $proposed = json_list($request['proposed']);
          // Project the new total when the stay, nights, rooms, or check-in change.
          $touchesPrice = array_key_exists('hotel_id', $proposed)
              || array_key_exists('nights', $proposed)
              || array_key_exists('rooms', $proposed)
              || array_key_exists('check_in_date', $proposed);
          $proposedTotal = null;
          if ($touchesPrice) {
              $pHotel = (int) ($proposed['hotel_id'] ?? $request['hotel_id']);
              $pNights = max(1, (int) ($proposed['nights'] ?? $request['nights']));
              $pRooms = max(1, (int) ($proposed['rooms'] ?? $request['rooms']));
              $pCheckIn = (string) ($proposed['check_in_date'] ?? $request['check_in_date']);
              $proposedTotal = compute_booking_total($pdo, (float) ($hotelPriceMap[$pHotel] ?? 0), $pNights, $pRooms, $pCheckIn);
          }
          ?>
          <article class="card booking-card">
            <div class="section-head">
              <div>
                <span class="booking-ref"><?= h($request['reference_code']) ?></span>
                <h2>Requested by <?= h($request['owner']) ?></h2>
              </div>
              <div class="inline-actions">
                <form action="<?= h(url('actions/admin_edit.php')) ?>" method="post">
                  <?= csrf_field() ?>
                  <input type="hidden" name="edit_request_id" value="<?= (int) $request['id'] ?>">
                  <input type="hidden" name="action" value="approve">
                  <button class="button button-ok" type="submit">Approve</button>
                </form>
                <form action="<?= h(url('actions/admin_edit.php')) ?>" method="post">
                  <?= csrf_field() ?>
                  <input type="hidden" name="edit_request_id" value="<?= (int) $request['id'] ?>">
                  <input type="hidden" name="action" value="reject">
                  <button class="button button-danger" type="submit">Reject</button>
                </form>
              </div>
            </div>
            <div class="diff-list">
              <?php foreach ($proposed as $key => $value): ?>
                <div class="diff-row">
                  <span class="diff-field"><?= h(booking_field_label($key)) ?></span>
                  <span class="diff-values">
                    <span class="diff-from"><?= h(admin_format_value($key, $request[$key] ?? '', $destinationMap, $hotelMap)) ?></span>
                    <span class="diff-arrow" aria-hidden="true">&rarr;</span>
                    <span class="diff-to"><?= h(admin_format_value($key, $value, $destinationMap, $hotelMap)) ?></span>
                  </span>
                </div>
              <?php endforeach; ?>
              <?php if ($proposedTotal !== null): ?>
                <div class="diff-row diff-row-total">
                  <span class="diff-field">Total</span>
                  <span class="diff-values">
                    <span class="diff-from"><?= h(money($request['hotel_total'])) ?></span>
                    <span class="diff-arrow" aria-hidden="true">&rarr;</span>
                    <span class="diff-to"><?= h(money($proposedTotal)) ?></span>
                  </span>
                </div>
              <?php endif; ?>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </section>

  <section class="tab-panel" data-tab-panel="cancellations">
    <?php if (!$cancelRequests): ?>
      <div class="panel empty-state">No pending cancellation requests.</div>
    <?php else: ?>
      <div class="booking-list">
        <?php foreach ($cancelRequests as $request): ?>
          <article class="card booking-card">
            <div class="section-head">
              <div>
                <span class="booking-ref"><?= h($request['reference_code']) ?></span>
                <h2>Requested by <?= h($request['owner']) ?></h2>
              </div>
              <div class="inline-actions">
                <form action="<?= h(url('actions/admin_cancel.php')) ?>" method="post">
                  <?= csrf_field() ?>
                  <input type="hidden" name="cancellation_request_id" value="<?= (int) $request['id'] ?>">
                  <input type="hidden" name="action" value="approve">
                  <button class="button button-danger" type="submit">Approve cancellation</button>
                </form>
                <form action="<?= h(url('actions/admin_cancel.php')) ?>" method="post">
                  <?= csrf_field() ?>
                  <input type="hidden" name="cancellation_request_id" value="<?= (int) $request['id'] ?>">
                  <input type="hidden" name="action" value="reject">
                  <button class="button button-ok" type="submit">Keep booking</button>
                </form>
              </div>
            </div>
            <div class="grid grid-3">
              <div><strong>Traveler</strong><br><?= h($request['full_name']) ?></div>
              <div><strong>Destination</strong><br><?= h($request['destination_name']) ?></div>
              <div><strong>Stay</strong><br><?= h($request['hotel_name']) ?></div>
              <div><strong>Check-in</strong><br><?= h($request['check_in_date']) ?></div>
              <div><strong>Total</strong><br><?= money($request['hotel_total']) ?></div>
            </div>
            <div class="notice notice-warn cancel-reason">
              <strong>Reason:</strong> <?= h($request['reason']) ?>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </section>

  <section class="tab-panel" data-tab-panel="users">
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Username</th>
            <th>Role</th>
            <th>Bookings</th>
            <th>Joined</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($users as $row): ?>
            <tr>
              <td><strong><?= h($row['username']) ?></strong></td>
              <td><span class="pill <?= $row['role'] === 'admin' ? 'pill-green' : '' ?>"><?= h($row['role']) ?></span></td>
              <td><?= (int) $row['bookings'] ?></td>
              <td><?= h(substr((string) $row['created_at'], 0, 10)) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>