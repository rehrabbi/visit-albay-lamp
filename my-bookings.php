<?php
require_once __DIR__ . '/includes/app.php';

require_login($pdo);

$pageTitle = 'My Bookings - Visit Albay';
$active = 'bookings';
$user = current_user($pdo);
$destinations = all_destinations($pdo);
$hotels = all_hotels($pdo);
$paymentMethods = ['GCash', 'Credit / Debit Card', 'Cash on Arrival'];
$destinationMap = [];
foreach ($destinations as $destination) {
    $destinationMap[(int) $destination['id']] = $destination['name'];
}
$hotelMap = [];
$hotelPriceMap = [];
foreach ($hotels as $hotelRow) {
    $hotelMap[(int) $hotelRow['id']] = $hotelRow['name'];
    $hotelPriceMap[(int) $hotelRow['id']] = (float) $hotelRow['price_per_night'];
}

$stmt = $pdo->prepare(
    'SELECT b.*, d.name AS destination_name, h.name AS hotel_name
     FROM bookings b
     JOIN destinations d ON d.id = b.destination_id
     JOIN hotels h ON h.id = b.hotel_id
     WHERE b.user_id = ?
     ORDER BY b.id DESC'
);
$stmt->execute([$user['id']]);
$bookings = $stmt->fetchAll();

$pendingStmt = $pdo->prepare("SELECT * FROM edit_requests WHERE booking_id = ? AND status = 'pending' ORDER BY id DESC LIMIT 1");
$noticeStmt = $pdo->prepare("SELECT * FROM edit_requests WHERE booking_id = ? AND status IN ('approved','rejected') AND seen = 0 ORDER BY id DESC LIMIT 1");
$markSeenStmt = $pdo->prepare('UPDATE edit_requests SET seen = 1 WHERE id = ?');

$cancelPendingStmt = $pdo->prepare("SELECT * FROM cancellation_requests WHERE booking_id = ? AND status = 'pending' ORDER BY id DESC LIMIT 1");
$cancelNoticeStmt = $pdo->prepare("SELECT * FROM cancellation_requests WHERE booking_id = ? AND status IN ('approved','rejected') AND seen = 0 ORDER BY id DESC LIMIT 1");
$markCancelSeenStmt = $pdo->prepare('UPDATE cancellation_requests SET seen = 1 WHERE id = ?');

require __DIR__ . '/includes/header.php';
?>
<main class="section">
  <div class="section-head">
    <div>
      <p class="eyebrow">My bookings</p>
      <h1>Your Albay trips</h1>
      <p>Request a change to any booking. An admin must approve it before the booking is updated.</p>
    </div>
    <a class="button button-primary" href="<?= h(url('plan.php')) ?>">Book another trip</a>
  </div>

  <?php if (!$bookings): ?>
    <div class="panel empty-state">
      <p>You do not have any bookings yet.</p>
      <a class="button button-primary" href="<?= h(url('plan.php')) ?>">Plan your visit</a>
    </div>
  <?php else: ?>
    <div class="booking-list">
      <?php foreach ($bookings as $booking): ?>
        <?php
        $pendingStmt->execute([$booking['id']]);
        $pending = $pendingStmt->fetch();
        $noticeStmt->execute([$booking['id']]);
        $notice = $noticeStmt->fetch();
        if ($notice) {
            $markSeenStmt->execute([$notice['id']]);
        }
        $cancelPendingStmt->execute([$booking['id']]);
        $cancelPending = $cancelPendingStmt->fetch();
        $cancelNoticeStmt->execute([$booking['id']]);
        $cancelNotice = $cancelNoticeStmt->fetch();
        if ($cancelNotice) {
            $markCancelSeenStmt->execute([$cancelNotice['id']]);
        }
        $isCancelled = $booking['status'] === 'cancelled';
        // Any pending request (edit or cancellation) locks further actions.
        $canAct = !$isCancelled && !$pending && !$cancelPending;
        ?>
        <article class="card booking-card<?= $isCancelled ? ' is-cancelled' : '' ?>">
          <div class="section-head">
            <div>
              <span class="booking-ref"><?= h($booking['reference_code']) ?></span>
              <h2><?= h($booking['destination_name']) ?></h2>
            </div>
            <?php if ($isCancelled): ?>
              <span class="status-badge status-cancelled">Cancelled</span>
            <?php elseif ($canAct): ?>
              <div class="card-actions">
                <button class="button button-ghost" type="button" data-toggle-edit="edit-<?= (int) $booking['id'] ?>">Request a change</button>
                <button class="button button-danger" type="button" data-toggle-edit="cancel-<?= (int) $booking['id'] ?>">Request cancellation</button>
              </div>
            <?php endif; ?>
          </div>

          <div class="grid grid-3">
            <div><strong>Traveler</strong><br><?= h($booking['full_name']) ?></div>
            <div><strong>Stay</strong><br><?= h($booking['hotel_name']) ?></div>
            <div><strong>Check-in</strong><br><?= h($booking['check_in_date']) ?></div>
            <div><strong>Check-out</strong><br><?= h($booking['check_out_date']) ?></div>
            <div><strong>Guests</strong><br><?= (int) $booking['guests'] ?></div>
            <div><strong>Total</strong><br><?= money($booking['hotel_total']) ?></div>
            <div><strong>Payment</strong><br><?= h($booking['payment_method']) ?></div>
            <div><strong>Email</strong><br><?= h($booking['email']) ?></div>
            <div><strong>Phone</strong><br><?= h($booking['phone']) ?></div>
          </div>

          <?php if ($pending): ?>
            <?php
            $proposed = json_list($pending['proposed']);
            // Recompute the proposed total when the stay, nights, rooms, or
            // check-in date change (check-in can shift it into a peak season).
            $touchesPrice = array_key_exists('hotel_id', $proposed)
                || array_key_exists('nights', $proposed)
                || array_key_exists('rooms', $proposed)
                || array_key_exists('check_in_date', $proposed);
            $proposedTotal = null;
            if ($touchesPrice) {
                $pHotel = (int) ($proposed['hotel_id'] ?? $booking['hotel_id']);
                $pNights = max(1, (int) ($proposed['nights'] ?? $booking['nights']));
                $pRooms = max(1, (int) ($proposed['rooms'] ?? $booking['rooms']));
                $pCheckIn = (string) ($proposed['check_in_date'] ?? $booking['check_in_date']);
                $proposedTotal = compute_booking_total($pdo, (float) ($hotelPriceMap[$pHotel] ?? 0), $pNights, $pRooms, $pCheckIn);
            }
            ?>
            <div class="notice notice-warn">
              <strong>Edit pending approval.</strong> Requested changes:
              <span class="pending-changes">
                <?php foreach ($proposed as $key => $value): ?>
                  <?php
                  if ($key === 'destination_id') {
                      $shown = $destinationMap[(int) $value] ?? $value;
                  } elseif ($key === 'hotel_id') {
                      $shown = $hotelMap[(int) $value] ?? $value;
                  } else {
                      $shown = ($value === '' ? '—' : $value);
                  }
                  ?>
                  <span class="pending-chip"><?= h(booking_field_label($key)) ?>: <strong><?= h($shown) ?></strong></span>
                <?php endforeach; ?>
                <?php if ($proposedTotal !== null): ?>
                  <span class="pending-chip pending-chip-total">Total: <span class="was"><?= money($booking['hotel_total']) ?></span> &rarr; <strong><?= money($proposedTotal) ?></strong></span>
                <?php endif; ?>
              </span>
            </div>
          <?php endif; ?>

          <?php if ($notice): ?>
            <div class="notice <?= $notice['status'] === 'approved' ? 'notice-ok' : 'notice-danger' ?>">
              Your requested change was <?= h($notice['status']) ?>.
            </div>
          <?php endif; ?>

          <?php if ($cancelPending): ?>
            <div class="notice notice-warn">
              <strong>Cancellation pending approval.</strong>
              <span class="pending-reason">Reason: <?= h($cancelPending['reason']) ?></span>
            </div>
          <?php endif; ?>

          <?php if ($cancelNotice): ?>
            <div class="notice <?= $cancelNotice['status'] === 'approved' ? 'notice-danger' : 'notice-ok' ?>">
              <?php if ($cancelNotice['status'] === 'approved'): ?>
                Your cancellation was approved &mdash; this booking is now cancelled.
              <?php else: ?>
                Your cancellation request was rejected. The booking stays active.
              <?php endif; ?>
            </div>
          <?php endif; ?>

          <?php if ($canAct): ?>
          <form id="edit-<?= (int) $booking['id'] ?>" class="edit-form" action="<?= h(url('actions/booking_edit.php')) ?>" method="post" data-prevent-double-submit>
            <?= csrf_field() ?>
            <input type="hidden" name="booking_id" value="<?= (int) $booking['id'] ?>">
            <div class="form-grid">
              <label>
                Full name
                <input name="full_name" value="<?= h($booking['full_name']) ?>">
              </label>
              <label>
                Email
                <input name="email" type="email" value="<?= h($booking['email']) ?>">
              </label>
              <label>
                Phone
                <input name="phone" value="<?= h($booking['phone']) ?>">
              </label>
              <label>
                Address
                <input name="address" value="<?= h($booking['address']) ?>">
              </label>
              <label>
                Destination
                <select name="destination_id">
                  <?php foreach ($destinations as $destination): ?>
                    <option value="<?= (int) $destination['id'] ?>" <?= (int) $destination['id'] === (int) $booking['destination_id'] ? 'selected' : '' ?>>
                      <?= h($destination['name']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </label>
              <label>
                Stay
                <select name="hotel_id" data-hotel-select>
                  <?php foreach ($hotels as $hotelOption): ?>
                    <option value="<?= (int) $hotelOption['id'] ?>"
                      data-price="<?= (float) $hotelOption['price_per_night'] ?>"
                      data-serves="<?= h(implode(',', $hotelOption['destination_ids'])) ?>"
                      data-distances="<?= h(json_encode((object) $hotelOption['distances'])) ?>"
                      data-label="<?= h($hotelOption['name'] . ' — ' . money($hotelOption['price_per_night']) . '/night') ?>"
                      <?= (int) $hotelOption['id'] === (int) $booking['hotel_id'] ? 'selected' : '' ?>>
                      <?= h($hotelOption['name']) ?> &mdash; <?= money($hotelOption['price_per_night']) ?>/night
                    </option>
                  <?php endforeach; ?>
                </select>
              </label>
              <label>
                Check-in date
                <input name="check_in_date" type="date" data-calendar value="<?= h($booking['check_in_date']) ?>">
              </label>
              <label>
                Nights
                <input name="nights" type="number" min="1" max="30" value="<?= (int) $booking['nights'] ?>">
              </label>
              <label>
                Guests
                <input name="guests" type="number" min="1" max="20" value="<?= (int) $booking['guests'] ?>">
              </label>
              <label>
                Rooms
                <input name="rooms" type="number" min="1" max="10" value="<?= (int) $booking['rooms'] ?>">
              </label>
              <label>
                Payment method
                <select name="payment_method">
                  <?php foreach ($paymentMethods as $method): ?>
                    <option value="<?= h($method) ?>" <?= $method === $booking['payment_method'] ? 'selected' : '' ?>><?= h($method) ?></option>
                  <?php endforeach; ?>
                </select>
              </label>
              <label class="wide">
                Special request
                <textarea name="special_request" rows="3"><?= h($booking['special_request']) ?></textarea>
              </label>
            </div>
            <div class="edit-total" data-edit-total data-original-total="<?= (float) $booking['hotel_total'] ?>">
              <span class="edit-total-label">Updated total</span>
              <span class="edit-total-amounts">
                <span class="edit-total-old"><?= money($booking['hotel_total']) ?></span>
                <span class="edit-total-arrow" aria-hidden="true">&rarr;</span>
                <span class="edit-total-new"><?= money($booking['hotel_total']) ?></span>
                <span class="edit-total-peak" hidden></span>
              </span>
            </div>
            <div class="button-row">
              <button class="button button-primary" type="submit">Submit change for approval</button>
              <button class="button button-ghost" type="button" data-toggle-edit="edit-<?= (int) $booking['id'] ?>">Back</button>
            </div>
          </form>

          <form id="cancel-<?= (int) $booking['id'] ?>" class="edit-form cancel-form" action="<?= h(url('actions/booking_cancel.php')) ?>" method="post" data-prevent-double-submit>
            <?= csrf_field() ?>
            <input type="hidden" name="booking_id" value="<?= (int) $booking['id'] ?>">
            <p class="cancel-lead">Tell us why you'd like to cancel. An admin will review your request.</p>
            <label class="wide">
              Reason for cancellation
              <textarea name="reason" rows="3" required minlength="5" placeholder="e.g. Change of travel plans"></textarea>
            </label>
            <div class="button-row">
              <button class="button button-danger" type="submit">Submit cancellation request</button>
              <button class="button button-ghost" type="button" data-toggle-edit="cancel-<?= (int) $booking['id'] ?>">Back</button>
            </div>
          </form>
          <?php endif; ?>
        </article>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</main>
<script>window.VA_PEAK_SEASONS = <?= json_encode(peak_seasons_for_js($pdo), JSON_UNESCAPED_SLASHES) ?>;</script>
<script src="<?= h(url('assets/js/calendar.js')) ?>?v=<?= filemtime(__DIR__ . '/assets/js/calendar.js') ?>"></script>
<script src="<?= h(url('assets/js/validation.js')) ?>"></script>
<script src="<?= h(url('assets/js/edit-booking.js')) ?>?v=<?= filemtime(__DIR__ . '/assets/js/edit-booking.js') ?>"></script>
<?php require __DIR__ . '/includes/footer.php'; ?>
