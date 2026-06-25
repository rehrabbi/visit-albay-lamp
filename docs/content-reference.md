# Visit Albay — Content & Data Reference

A complete inventory of everything on the site: destinations, stays, prices, ratings,
images, page content, and the rules behind pricing and bookings. Generated from the
live `visit_albay` database and the page source. Currency throughout is the Philippine
peso (**₱**).

---

## 1. Site overview

- **Purpose:** Tourism website for Albay, Bicol Region, Philippines — browse destinations,
  book a trip with a stay, and manage bookings through user accounts and an admin dashboard.
- **Stack:** PHP 8.2 + MariaDB 10.4 (PDO), semantic HTML, external CSS, vanilla JS, Leaflet map.
- **Pages:** Home (`index.php`), Destinations (`destinations.php`), Experience (`experiences.php`),
  Plan/booking (`plan.php`), Login (`login.php`), My Bookings (`my-bookings.php`),
  Admin (`admin.php`).

### Demo accounts (auto-seeded on any page load)

| Username | Password | Role  |
|----------|----------|-------|
| `admin`  | `admin`  | admin |
| `user`   | `user`   | user  |

---

## 2. Destinations (6)

Shown with a star rating on the site. Images live in `assets/img/destinations/<slug>.jpg`.

| # | Name | Town | Category | Rating (★) | Duration | Vibe | Tagline |
|---|------|------|----------|-----------|----------|------|---------|
| 1 | Mayon Volcano | Daraga / Legazpi | Nature | 5.0 | Half to full day | Iconic and awe-inspiring | "The world's most perfect cone." |
| 2 | Cagsawa Ruins | Daraga | Heritage | 4.8 | 1 to 2 hours | Reflective and historic | "A belfry that survived 1814." |
| 3 | Lignon Hill | Legazpi | Adventure | 4.6 | 1 to 2 hours | Active and panoramic | "360-degree views and a zipline." |
| 4 | Daraga Church | Daraga | Heritage | 4.7 | About 1 hour | Serene and historic | "Baroque on a hilltop." |
| 5 | Sumlang Lake | Camalig | Nature | 4.5 | 1 to 2 hours | Calm and scenic | "Bamboo rafts under Mayon." |
| 6 | Quitinday Green Hills | Camalig / Jovellar | Adventure | 4.4 | Half day | Off-the-beaten-path | "Albay's rolling green hills." |

### Per-destination detail

**Mayon Volcano** (`mayon`) — Best for: Photos, Adventure, First-timers
- Highlights: The famous symmetrical cone seen across the province · Sunrise and golden-hour views over the fields · ATV and lava-trail tours around the foothills
- Activities: Photography · ATV rides · Guided foothill tours

**Cagsawa Ruins** (`cagsawa`) — Best for: History, Photos, Families
- Highlights: The lone bell tower set against the cone · On-site park, museum, and souvenir stalls · The classic framed photo of tower and volcano
- Activities: Photography · Heritage walk · Souvenir shopping

**Lignon Hill** (`lignon`) — Best for: Adventure, Views, Photos
- Highlights: 360-degree summit viewdeck · Zipline and hanging bridge · Short hike or easy drive to the top
- Activities: Zipline · Hiking · Sightseeing

**Daraga Church** (`daraga`) — Best for: History, Photos, Culture
- Highlights: Carved volcanic-stone facade · Hilltop views of the town and Mayon · A quiet, photogenic heritage setting
- Activities: Photography · Heritage visit · Quiet reflection

**Sumlang Lake** (`sumlang`) — Best for: Relaxation, Families, Photos
- Highlights: Bamboo-raft rides across still water · Clear Mayon reflections on calm days · Lakeside gardens and cafes
- Activities: Bamboo rafting · Photography · Relaxing by the lake

**Quitinday Green Hills** (`quitinday`) — Best for: Adventure, Nature, Photos
- Highlights: Rolling green-hill viewpoint · Nearby underground river and caves · A quieter, less-crowded side of Albay
- Activities: Hiking · Photography · Cave exploring with a guide

---

## 3. Stays / Hotels (9)

Sorted here by price (high → low). Images in `assets/img/hotels/`.
**Note:** the `rating` and `reviews` columns exist in the DB but are intentionally **not shown**
on the site (only destination ratings are displayed as stars).

| Name | Type | Area | Price / night | Rating (hidden) | Reviews (hidden) | Image |
|------|------|------|--------------:|:---:|:---:|-------|
| The Oriental Legazpi | Hotel / 5-star | Taysan, Legazpi / Mayon view | ₱8,200 | 4.5 | 2,000+ | oriental-legazpi.png |
| The Marison Hotel | Hotel / 4-star | Legazpi / spa | ₱3,900 | 4.5 | 900+ | marison.jpeg |
| Hotel Venezia | Hotel / 3-star | Legazpi / near airport | ₱2,700 | 4.2 | 1,100+ | venezia.jpg |
| The Pepperland Hotel | Hotel / 3-star | Legazpi | ₱2,400 | 4.0 | 600+ | pepperland.jpg |
| Hotel St. Ellis | Hotel / 3-star | Legazpi | ₱2,200 | 4.1 | 700+ | st-ellis.jpg |
| Daraga Mayon-View Casa | Airbnb / whole place | Daraga / sleeps 4 | ₱2,100 | 4.8 | 150+ | daraga-casa.jpg |
| Vals Farm Guesthouse | Guesthouse | Camalig / near Sumlang | ₱1,900 | 4.9 | 300+ | vals-farm.jpeg |
| FG Lodge | Guesthouse | Daraga / rooftop Mayon view | ₱1,400 | 4.4 | 200+ | fg-lodge.jpeg |
| Mayon Lodging House | Budget inn | Daraga | ₱1,200 | 4.0 | 250+ | mayon-lodging.jpeg |

### Which stay serves which destination (with distance, km)

The stay picker filters by the chosen destination and lists the **nearest first**.

| Stay | Serves (distance km) |
|------|----------------------|
| The Oriental Legazpi | Mayon 4.0 · Lignon 4.0 · Cagsawa 9.0 · Daraga 9.0 · Quitinday 17.0 · Sumlang 17.0 |
| The Marison Hotel | Mayon 4.0 · Lignon 4.0 · Cagsawa 9.0 · Daraga 9.0 · Sumlang 17.0 · Quitinday 17.0 |
| Hotel Venezia | Mayon 4.0 · Lignon 4.0 · Cagsawa 9.0 · Daraga 9.0 · Sumlang 17.0 · Quitinday 17.0 |
| The Pepperland Hotel | Mayon 4.0 · Lignon 4.0 · Cagsawa 9.0 · Daraga 9.0 · Quitinday 17.0 · Sumlang 17.0 |
| Hotel St. Ellis | Mayon 4.0 · Lignon 4.0 · Cagsawa 9.0 · Daraga 9.0 · Sumlang 17.0 · Quitinday 17.0 |
| Daraga Mayon-View Casa | Mayon 3.5 · Cagsawa 3.5 · Daraga 3.5 |
| FG Lodge | Mayon 3.5 · Cagsawa 3.5 · Daraga 3.5 |
| Mayon Lodging House | Mayon 3.5 · Cagsawa 3.5 · Daraga 3.5 |
| Vals Farm Guesthouse | Sumlang 3.0 · Quitinday 3.0 · Cagsawa 16.0 |

---

## 4. Pricing model

**Base stay total** = `price_per_night × nights × rooms`.

**Peak-season surcharge: +15%** when the **check-in date** falls inside a peak window.
Final total = base × 1.15 during peak, otherwise base × 1.00. Computed in one place
(`compute_booking_total()` in `includes/functions.php`) and applied consistently on
booking, edit-approval, and the live previews.

### Peak seasons (`peak_seasons` table)

| Window | Dates | Surcharge |
|--------|-------|-----------|
| Holy Week 2026 | Mar 29 – Apr 5, 2026 | +15% |
| Magayon Festival 2026 | May 1 – 31, 2026 | +15% |
| Christmas & New Year 2026 | Dec 15, 2026 – Jan 5, 2027 | +15% |
| Holy Week 2027 | Mar 21 – 28, 2027 | +15% |
| Magayon Festival 2027 | May 1 – 31, 2027 | +15% |
| Christmas & New Year 2027 | Dec 15, 2027 – Jan 5, 2028 | +15% |

> Example: ₱1,400/night × 7 nights × 1 room = ₱9,800 off-peak → ₱11,270 during a peak window.

### Payment methods

- **GCash** — stores account name + mobile number.
- **Credit / Debit Card** — stores name + **last 4 digits only** + expiry (never the full number or CVV).
- **Cash on Arrival** — no payment details stored.

---

## 5. Booking lifecycle

- **Status:** `active` or `cancelled`.
- **Edit requests:** a user proposes changes (any field incl. stay/nights/rooms/payment);
  status `pending → approved/rejected`; on approval the price/total/check-out recompute.
- **Cancellation requests:** a user submits a required **reason**; status `pending → approved/rejected`;
  on approval the booking becomes `cancelled`.
- A booking can have at most one pending edit **or** one pending cancellation at a time.
- The admin dashboard tabs: **Bookings**, **Pending edits**, **Cancellations**, **Users**.

---

## 6. Home page content

### Why Albay (3 points)
- **The perfect cone** — Mayon's near-symmetrical silhouette is unlike anything else on Earth, visible from almost everywhere in the province.
- **Living history** — From the 1814 Cagsawa Ruins to the hilltop Daraga Church, Albay's past is carved into volcanic stone.
- **Bicolano flavor** — Taste Bicol Express, laing, and pili nuts — bold, coconut-rich cooking born of the region.

### Flavors of Bicol (food, 4)
| Dish | Image | Description |
|------|-------|-------------|
| Bicol Express | scenery/bicol-express.jpg | Pork in coconut milk with siling labuyo — the dish that put Bicol on the map. |
| Laing | scenery/laing.png | Dried taro leaves simmered in thick coconut milk. Earthy, creamy, and deeply Bicolano. |
| Pinangat | scenery/pinangat.jpg | Taro and pork wrapped in coconut fronds — a slow, smoky Camalig specialty. |
| Pili Nuts | scenery/pili.jpg | Grown only in Bicol — buttery and rich. The souvenir everyone brings home. |

### Traveler reviews (3, all shown ★★★★★)
| Name | From | About | Quote |
|------|------|-------|-------|
| Sarah Reyes | Manila | Mayon Volcano | "Nothing prepared me for the actual scale of Mayon… We woke up at 4am for sunrise and it was worth every second." |
| James Okoro | Cebu City | Cagsawa Ruins | "Standing by the belfry with Mayon looming behind it was one of the most cinematic moments of my life…" |
| Ana Villanueva | Singapore | Sumlang Lake | "We took a bamboo raft at golden hour and Mayon reflected perfectly in the still water… Come in dry season." |

> Review avatars reuse: Sarah → sumlang.jpg, James → cagsawa-mayon.jpg, Ana → quitinday.jpg.

### Gallery images
mayon-crater.jpg · cagsawa-mayon.jpg (scenery) · sumlang.jpg · daraga.jpg · quitinday.jpg (destinations).

### Interactive map
Leaflet 1.9.4 with 6 destination pins (positioned by each destination's `map_x` / `map_y`).
Tiles load from `basemaps.cartocdn.com` (the one online dependency).

---

## 7. Experience page content

### Three ways to experience Albay (bands)
| # | Theme | Blurb | Places |
|---|-------|-------|--------|
| 01 | Nature | Lakes, reflections, and the perfect cone — slow, scenic days. | Mayon Volcano, Sumlang Lake |
| 02 | Heritage | Volcanic-stone churches and a belfry that outlived an eruption. | Cagsawa Ruins, Daraga Church |
| 03 | Adventure | Ziplines, viewdecks, and rolling green hills. | Lignon Hill, Quitinday Green Hills |

### Best time to visit (month guide — display only, separate from pricing peaks)
| Month | Sky | Temp | Note |
|-------|-----|------|------|
| Jan | Clear | 26°C | Peak season |
| Feb | Sunny | 27°C | Peak season |
| Mar | Sunny | 29°C | Dry season |
| Apr | Hot | 31°C | Holy Week |
| May | Mixed | 30°C | Magayon Festival |
| Jun | Rainy | 28°C | Wet season starts |
| Jul | Storms | 27°C | Typhoon risk |
| Aug | Storms | 27°C | Typhoon risk |
| Sep | Heavy | 27°C | Ibalong Festival |
| Oct | Mixed | 27°C | Shoulder |
| Nov | Clearing | 27°C | Good visibility |
| Dec | Clear | 26°C | Peak season |

> Note: this visual month guide is a general visitor's calendar; the actual +15% surcharge is
> driven by the `peak_seasons` table in §4. The two are aligned — Holy Week (Apr), Magayon (May),
> and Christmas/New Year (Dec–Jan) are the peak windows in both.

---

## 8. Asset inventory

### Destination photos — `assets/img/destinations/`
cagsawa.jpg · daraga.jpg · lignon.jpg · mayon.jpg · quitinday.jpg · sumlang.jpg

### Scenery / food / gallery — `assets/img/scenery/`
bicol-express.jpg · cagsawa-mayon.jpg · laing.png · mayon-crater.jpg · pili.jpg · pinangat.jpg

### Hotel photos — `assets/img/hotels/`
daraga-casa.jpg · fg-lodge.jpeg · marison.jpeg · mayon-lodging.jpeg · oriental-legazpi.png · pepperland.jpg · st-ellis.jpg · vals-farm.jpeg · venezia.jpg

### Image sources / credits
- Destination & scenery photos: **Wikimedia Commons** (CC-licensed) — credited in the footer as "Photography · Wikimedia Commons".
- `laing.png`: the owner's own photo.
- All images are bundled locally (no hotlinking).

### Fonts — `assets/fonts/` (SIL Open Font License, bundled locally)
- **Schibsted Grotesk** (Regular, Medium, Bold, ExtraBold) — primary UI font
- **JetBrains Mono** (Regular, Bold) — monospace accents/labels
- **Hanken Grotesk** (Medium, Bold) — secondary
- License texts: `*-OFL.txt`

### Map library — `assets/vendor/leaflet/`
Leaflet 1.9.4 (`leaflet.css`, `leaflet.js`, marker `images/`), bundled locally.

---

## 9. Quick stats

- 6 destinations · 9 stays · 42 stay-destination links · 6 peak windows
- Price range: ₱1,200 – ₱8,200 / night · Peak surcharge: +15%
- 3 destination categories: Nature, Heritage, Adventure
- 4 food items · 3 reviews · 3 experience themes · 12-month travel guide
