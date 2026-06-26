<?php
require_once __DIR__ . '/includes/app.php';

require_login($pdo);

$pageTitle = 'Plan Your Visit - Visit Albay';
$active = 'plan';
$destinations = all_destinations($pdo);
$hotels = all_hotels($pdo);
$selectedDestination = (int) ($_GET['destination'] ?? 0);

require __DIR__ . '/includes/header.php';
?>
<main class="section">
  <div class="page-head">
    <p class="eyebrow">Plan your visit</p>
    <h1>Book your Albay trip</h1>
    <p class="lead">Pick a destination and your dates, choose a stay matched to that route, then confirm. No charge today &mdash; your reference code confirms the booking; pay on arrival or as arranged.</p>
  </div>

  <form class="booking-form" action="<?= h(url('actions/booking_create.php')) ?>" method="post" data-booking-form data-prevent-double-submit>
    <?= csrf_field() ?>

    <section class="form-section">
      <h2 class="form-section-title">Your details</h2>
      <div class="form-grid">
        <label>
          Full name
          <input name="full_name" autocomplete="name" required>
        </label>
        <label>
          Email
          <input name="email" type="email" autocomplete="email" required>
        </label>
        <label>
          Phone
          <input name="phone" autocomplete="tel" pattern="^\+?[0-9]+$" title="Phone number must contain only numbers and an optional starting +" required>
        </label>
        <label>
          Address <span class="optional">(optional)</span>
          <input name="address" autocomplete="street-address">
        </label>
      </div>
    </section>

    <section class="form-section">
      <h2 class="form-section-title">Trip &amp; dates</h2>
      <div class="form-grid">
        <label class="wide">
          Destination
          <select name="destination_id" required>
            <option value="">Choose destination</option>
            <?php foreach ($destinations as $destination): ?>
              <option value="<?= (int) $destination['id'] ?>" <?= $selectedDestination === (int) $destination['id'] ? 'selected' : '' ?>>
                <?= h($destination['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </label>
        <label>
          Check-in date
          <input name="check_in_date" type="date" data-calendar required>
        </label>
        <label>
          Guests
          <span class="stepper">
            <button type="button" data-stepper="guests" data-delta="-1" aria-label="Decrease guests">&minus;</button>
            <input name="guests" type="number" min="1" max="20" value="2" required>
            <button type="button" data-stepper="guests" data-delta="1" aria-label="Increase guests">+</button>
          </span>
        </label>
        <label>
          Nights
          <span class="stepper">
            <button type="button" data-stepper="nights" data-delta="-1" aria-label="Decrease nights">&minus;</button>
            <input name="nights" type="number" min="1" max="30" value="1" required>
            <button type="button" data-stepper="nights" data-delta="1" aria-label="Increase nights">+</button>
          </span>
        </label>
        <label>
          Rooms
          <span class="stepper">
            <button type="button" data-stepper="rooms" data-delta="-1" aria-label="Decrease rooms">&minus;</button>
            <input name="rooms" type="number" min="1" max="10" value="1" required>
            <button type="button" data-stepper="rooms" data-delta="1" aria-label="Increase rooms">+</button>
          </span>
        </label>
      </div>
    </section>

    <section class="form-section">
      <h2 class="form-section-title">Where to stay</h2>
      <p class="stays-hint">Accommodations near your destination &mdash; the price shown is per night. Choose a destination above to narrow the list.</p>
      <div class="stay-list">
        <?php foreach ($hotels as $hotel): ?>
          <label class="hotel-card">
            <input
              type="radio"
              name="hotel_id"
              value="<?= (int) $hotel['id'] ?>"
              data-price="<?= h($hotel['price_per_night']) ?>"
              data-serves="<?= h(implode(',', $hotel['destination_ids'])) ?>"
              data-distances="<?= h(json_encode((object) $hotel['distances'])) ?>"
              required
            >
            <span class="hotel-card-inner">
              <img src="<?= h(url($hotel['image_path'])) ?>" alt="<?= h($hotel['name']) ?>">
              <span class="info">
                <span class="pill pill-green"><?= h($hotel['accommodation_type']) ?></span>
                <strong><?= h($hotel['name']) ?></strong>
                <span class="hotel-area"><?= h($hotel['area']) ?></span>
              </span>
              <span class="hotel-price"><?= money($hotel['price_per_night']) ?> <span class="per">/ night</span></span>
            </span>
          </label>
        <?php endforeach; ?>
      </div>
    </section>

    <section class="form-section">
      <h2 class="form-section-title">Payment</h2>
      <div class="form-grid">
        <label class="wide">
          Payment method
          <select name="payment_method" required>
            <option value="" disabled selected hidden>Choose payment</option>
            <option value="GCash">GCash</option>
            <option value="Credit / Debit Card">Credit / Debit Card</option>
            <option value="Cash on Arrival">Cash on Arrival</option>
          </select>
        </label>
      </div>

      <div class="payment-panel" data-payment-panel="GCash" aria-label="GCash details">
        <div class="form-grid">
          <label>
            GCash account name
            <input name="gcash_name" autocomplete="name">
          </label>
          <label>
            GCash mobile number
            <input name="gcash_number" placeholder="09XXXXXXXXX">
          </label>
        </div>
      </div>

      <div class="payment-panel" data-payment-panel="Credit / Debit Card" aria-label="Card details">
        <div class="form-grid">
          <label>
            Name on card
            <input name="card_name" autocomplete="cc-name">
          </label>
          <label>
            Card number
            <input name="card_number" autocomplete="cc-number" inputmode="numeric">
          </label>
          <label>
            Expiry
            <input name="card_expiry" placeholder="MM/YY" autocomplete="cc-exp">
          </label>
          <label>
            CVV
            <input name="card_cvv" autocomplete="cc-csc" inputmode="numeric">
          </label>
        </div>
      </div>
    </section>

    <section class="form-section">
      <h2 class="form-section-title">Anything else?</h2>
      <label>
        Special request <span class="optional">(optional)</span>
        <textarea name="special_request" rows="3" placeholder="Pickup notes, accessibility needs, or preferred schedule"></textarea>
      </label>
    </section>

    <aside class="booking-total" data-total-box>
      <span class="label">Estimated stay total</span>
      <strong data-total-text>₱0</strong>
      <span class="booking-total-peak" data-peak-note hidden></span>
      <span>Check-out: <span data-checkout-text>Choose a check-in date</span></span>
    </aside>

    <div class="button-row">
      <button class="button button-primary" type="submit">Confirm booking</button>
      <a class="button button-ghost" href="<?= h(url('destinations.php')) ?>">Review destinations</a>
    </div>
  </form>
</main>
<script>window.VA_PEAK_SEASONS = <?= json_encode(peak_seasons_for_js($pdo), JSON_UNESCAPED_SLASHES) ?>;</script>
<script src="<?= h(url('assets/js/calendar.js')) ?>?v=<?= filemtime(__DIR__ . '/assets/js/calendar.js') ?>"></script>
<script src="<?= h(url('assets/js/booking.js')) ?>?v=<?= filemtime(__DIR__ . '/assets/js/booking.js') ?>"></script>
<script src="<?= h(url('assets/js/validation.js')) ?>?v=<?= filemtime(__DIR__ . '/assets/js/validation.js') ?>"></script>
<?php require __DIR__ . '/includes/footer.php'; ?>
