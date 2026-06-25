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

// Create a lookup dictionary to map hotel IDs to human-readable names.
$hotelMap = [];
foreach ($pdo->query('SELECT id, name FROM hotels')->fetchAll() as $hotelRow) {
    $hotelMap[(int) $hotelRow['id']] = $hotelRow['name'];
}

// Fetch the 50 most recent bookings with their associated owner, destination, and hotel names.
// LIMIT 50 is enforced to prevent server memory overload and DOM lag as the database scales.
$bookings = $pdo->query(
    'SELECT b.*, u.username AS owner, d.name AS destination_name, h.name AS hotel_name
     FROM bookings b
     JOIN users u ON u.id = b.user_id
     JOIN destinations d ON d.id = b.destination_id
     JOIN hotels h ON h.id = b.hotel_id
     ORDER BY b.id DESC
     LIMIT 50'
)->fetchAll();

// Fetch all pending edit requests with their original booking context.
$editRequests = $pdo->query(
    "SELECT e.*, b.reference_code, b.full_name, b.destination_id, b.hotel_id, b.check_in_date,
            b.nights, b.guests, b.rooms, b.payment_method, b.email, b.phone, b.address,
            b.special_request, u.username AS owner
     FROM edit_requests e
     JOIN bookings b ON b.id = e.booking_id
     JOIN users u ON u.id = b.user_id
     WHERE e.status = 'pending'
     ORDER BY e.id DESC"
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
    <button class="button is-active" type="button" data-tab-target="bookings">Bookings <?= count($bookings) ?></button>
    <button class="button" type="button" data-tab-target="edits">Pending edits <?= count($editRequests) ?></button>
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
  </section>

  <section class="tab-panel" data-tab-panel="edits">
    <?php if (!$editRequests): ?>
      <div class="panel empty-state">No pending edit requests.</div>
    <?php else: ?>
      <div class="booking-list">
        <?php foreach ($editRequests as $request): ?>
          <?php $proposed = json_list($request['proposed']); ?>
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