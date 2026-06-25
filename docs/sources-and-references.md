# Visit Albay — Sources & References

Where every piece of information on the site comes from. Read this together with
[content-reference.md](content-reference.md), which lists the data itself.

## How to read this document (important & honest)

This is a **WebDev course project**, not a commercial booking site. So the data falls
into two clearly different buckets:

- **Factual content — sourced.** The destinations, their history, the festivals, the
  cuisine, and the real hotels are genuine. Every factual claim is backed by the
  authoritative links below (Wikipedia, government/tourism sites, Smithsonian/Britannica,
  hotel booking pages).
- **Illustrative data — created for the demo.** The **star ratings, exact nightly
  prices, review counts ("2,000+"), the hotel-to-destination distances, and the traveler
  reviews/names** are **representative values composed for this academic project**. They
  are *informed by* typical figures you'd see on booking/review platforms (Agoda,
  Booking.com, TripAdvisor) but were **not scraped from a live source**, so they should be
  presented as plausible sample data — not as official figures. The +15% peak surcharge is
  a project pricing rule (see §4), not a published rate.

This distinction is deliberate so the project can be defended honestly in the demo Q&A.

---

## 1. Destinations — factual sources

| Destination | Primary sources |
|---|---|
| **Mayon Volcano** | [Wikipedia: Mayon](https://en.wikipedia.org/wiki/Mayon) · [Smithsonian Global Volcanism Program](https://volcano.si.edu/volcano.cfm?vn=273030) · [Britannica: Mayon Volcano](https://www.britannica.com/place/Mayon-Volcano) · [UNESCO tentative list](https://whc.unesco.org/en/tentativelists/6790/) |
| **Cagsawa Ruins** | [Wikipedia: Cagsawa Ruins](https://en.wikipedia.org/wiki/Cagsawa_Ruins) · [Daraga LGU tourism](https://daraga.gov.ph/tourism-daraga/) · [Atlas Obscura](https://www.atlasobscura.com/places/cagsawa-ruins) |
| **Lignon Hill** | [Wikipedia: Ligñon Hill](https://en.wikipedia.org/wiki/Lig%C3%B1on_Hill) · [TripAdvisor: Lignon Hill](https://www.tripadvisor.com/Attraction_Review-g317122-d1953993-Reviews-Lignon_Hill-Legazpi_Albay_Province_Bicol_Region_Luzon.html) |
| **Daraga Church** (Nuestra Señora de la Porteria) | [Daraga LGU tourism](https://daraga.gov.ph/tourism-daraga/) · context in [Wikipedia: Cagsawa Ruins](https://en.wikipedia.org/wiki/Cagsawa_Ruins) (notes the 1773 Daraga Church is distinct from Cagsawa) |
| **Sumlang Lake** | [Province of Albay — Tourist Spots](https://albay.gov.ph/tourist-spot/) · [Camalig LGU tourist spots](https://www.camalig.gov.ph/camalig-tourist-spots/) |
| **Quitinday Green Hills** | [TripAdvisor: Quitinday Green Hills Formation Reserve](https://www.tripadvisor.com/Attraction_Review-g7729614-d9882722-Reviews-Quitinday_Green_Hills_Formation_Reserve-Camalig_Albay_Province_Bicol_Region_Luzo.html) · [Lakwatsero: Quitinday Hills](https://www.lakwatsero.com/spots/quitinday-hills-camalig-albay/) |

**Key sourced facts used in the descriptions:**
- Mayon is an active stratovolcano, ~2,463 m, famed as one of the world's most symmetrical cones; most active volcano in the Philippines, monitored by PHIVOLCS from Ligñon Hill (Wikipedia/Smithsonian).
- Cagsawa: 16th-century Franciscan church; the **1814 Mayon eruption** (its strongest recorded) buried the town — only the belfry survives (Wikipedia, Daraga LGU).
- Daraga Church: built **1773**, distinct from Cagsawa (Wikipedia/Daraga LGU).
- Lignon Hill: ~156 m, panoramic Mayon/Legazpi views, zipline + trails (Wikipedia).
- Sumlang Lake: lake in Camalig with bamboo rafts and Mayon reflections (Albay/Camalig LGU).
- Quitinday: rolling limestone "green hills" near an underground river, often likened to the Chocolate Hills (TripAdvisor/Lakwatsero).

> **Star ratings (5.0, 4.8, 4.7…)** are illustrative for the demo — see the methodology note above.

---

## 2. Stays / Hotels — real hotels, representative figures

These are **real Legazpi/Albay properties**; their existence and general description are
sourced. **Nightly prices, star tiers, ratings, and review counts in the app are sample
values** (informed by typical booking-site listings, not live rates).

| Hotel | Source |
|---|---|
| **The Oriental Legazpi** | [Booking.com](https://www.booking.com/hotel/ph/the-oriental-legazpi.html) |
| **Hotel Venezia** | [Official site](https://hotelvenezia.com.ph/) · [TripAdvisor](https://www.tripadvisor.com/Hotel_Review-g317122-d1062838-Reviews-Hotel_Venezia-Legazpi_Albay_Province_Bicol_Region_Luzon.html) · [Agoda](https://www.agoda.com/hotel-venezia/hotel/legazpi-ph.html) |
| **The Marison Hotel** | Listed on Agoda/Booking (search "Marison Hotel Legazpi") |
| The Pepperland Hotel, Hotel St. Ellis, Daraga Mayon-View Casa, Vals Farm Guesthouse, FG Lodge, Mayon Lodging House | Real Legazpi/Daraga/Camalig accommodations; look up on Agoda / Booking.com / Google Maps for live details |

> **Hotel→destination distances** (e.g. "4 km", "17 km") are **approximate values composed
> for the nearest-first sort** — they are rough town-proximity estimates, not measured routes.

---

## 3. Traveler reviews

The three reviews (Sarah Reyes / Manila, James Okoro / Cebu City, Ana Villanueva /
Singapore) are **fictional sample testimonials written for the demo** — not real user
reviews. The 5-star display is illustrative.

---

## 4. Pricing & peak-season surcharge (+15%)

The **base price model** (`price_per_night × nights × rooms`) is the project's own logic.
The **peak windows and the idea that peak dates cost more** are grounded in real Philippine
travel patterns:

- **Holy Week** (Mar/Apr) and **Christmas–New Year** (≈ Dec 20 – Jan 5) are the Philippines'
  most crowded, most expensive travel periods; accommodation can run **2–3× low-season
  rates**. Sources: [Tropical Experience PH — tourist seasons](https://www.tropicalexperiencephilippines.com/answer-high-low-season), [JetsetterAlerts — cheapest/most expensive times](https://www.jetsetteralerts.com/cheapest-and-most-expensive-times-to-visit-the-philippines/), [Journal Online — domestic Holy Week travel 2026](https://journal.com.ph/majority-of-filipinos-choose-domestic-travel-for-holy-week-2026/).
- **Magayon Festival** is a **month-long May festival in Albay** (Daragang Magayon) — hence
  May is a local peak. Sources: [Tourism Promotions Board](https://tpb.gov.ph/events/magayon-festival/), [Festivalscape — Magayon Festival](https://www.festivalscape.com/philippines/albay/magayon-festival/), [Philippine News Agency](https://www.pna.gov.ph/articles/1223834).

> The specific **+15%** rate and the exact seeded date ranges in the `peak_seasons` table
> are a **project pricing rule** chosen to demonstrate seasonal pricing — not an official
> published surcharge.

---

## 5. Flavors of Bicol (food)

| Dish | Source |
|---|---|
| **Bicol Express** | [Wikipedia: Bicol express](https://en.wikipedia.org/wiki/Bicol_express) |
| **Laing** | [Bicol's Best — IFEX Connect](https://www.ifexconnect.com/story/bicols-best-from-laing-to-pili-nuts) · [7641 Islands — Bicol cuisine](https://7641islands.ph/explore/eating-through-region-5-a-taste-of-bicols-distinctive-cuisine/) |
| **Pinangat** | [ManilaToBicol — Bicolano food](https://manilatobicol.ph/foods) |
| **Pili Nuts** | [Bicol's Best — IFEX Connect](https://www.ifexconnect.com/story/bicols-best-from-laing-to-pili-nuts) |

Sourced facts: Bicol Express is a pork stew in coconut milk (gata) with chili and shrimp
paste; Laing is dried taro leaves in coconut milk; Pinangat wraps taro/meat in leaves;
Pili nuts are indigenous to Bicol and thrive in volcanic soil.

---

## 6. Images — exact sources & license

All destination/scenery photos are bundled locally under `assets/img/` but originate from
**Wikimedia Commons** (Creative Commons licensed; credited in the site footer as
"Photography · Wikimedia Commons"). Original source files:

| Local file | Wikimedia Commons source |
|---|---|
| `destinations/mayon.jpg` | https://commons.wikimedia.org/wiki/Special:FilePath/Majestic_Beauty_of_Mayon_Volcano.jpg |
| `destinations/cagsawa.jpg` | https://commons.wikimedia.org/wiki/Special:FilePath/Cagsawa_ruins.jpg |
| `destinations/lignon.jpg` | https://commons.wikimedia.org/wiki/Special:FilePath/Li%C3%B1on%20Hill%20view%20from%20Daraga%20San%20Roque%20(Daraga,%20Albay;%2004-17-2023).jpg |
| `destinations/daraga.jpg` | https://commons.wikimedia.org/wiki/Special:FilePath/The_Daraga_Church_in_Albay_Province.jpg |
| `destinations/sumlang.jpg` | https://commons.wikimedia.org/wiki/Special:FilePath/Sumlang%20Lake%20(Camalig,%20Albay;%2004-20-2023).jpg |
| `destinations/quitinday.jpg` | https://commons.wikimedia.org/wiki/Special:FilePath/Quitinday%20Green%20Hills%20south%20view%20(Camalig,%20Albay;%2004-22-2023).jpg |
| `scenery/mayon-crater.jpg`, `scenery/cagsawa-mayon.jpg`, `scenery/bicol-express.jpg`, `scenery/pinangat.jpg`, `scenery/pili.jpg` | Wikimedia Commons (CC) — search the filename on https://commons.wikimedia.org to retrieve the exact file page + author/license |
| `scenery/laing.png` | **The project owner's own photograph** (not third-party) |
| Hotel photos (`assets/img/hotels/*`) | Sourced from each hotel's public listing/website for the demo; replace with owned/licensed images for any public/commercial use |

> Action item if this goes public: open each Wikimedia file page above and record the exact
> author + license (CC BY / CC BY-SA) for a formal attribution list.

---

## 7. Fonts

Bundled locally under `assets/fonts/` (SIL Open Font License; license texts in `*-OFL.txt`).
Originally from **Google Fonts**:
- [Schibsted Grotesk](https://fonts.google.com/specimen/Schibsted+Grotesk)
- [JetBrains Mono](https://fonts.google.com/specimen/JetBrains+Mono)
- [Hanken Grotesk](https://fonts.google.com/specimen/Hanken+Grotesk)

---

## 8. Map & libraries

- **Leaflet 1.9.4** — interactive map library, bundled locally in `assets/vendor/leaflet/`. Source: [leafletjs.com](https://leafletjs.com/).
- **Map tiles** — the slippy-map basemap draws from **CARTO** (`basemaps.cartocdn.com`, "Voyager" raster tiles), built on **OpenStreetMap** data. This is the one remaining online dependency. Attribution: © OpenStreetMap contributors, © CARTO ([carto.com/attribution](https://carto.com/attribution/)).

---

## 9. Summary of what is original vs sourced

| Information | Status |
|---|---|
| Destination existence, history, geography, festivals, cuisine | **Sourced** (links in §1, §4, §5) |
| Destination images | **Sourced** — Wikimedia Commons, CC (§6) |
| Star ratings, nightly prices, review counts, hotel distances | **Illustrative / representative** for the demo |
| Traveler reviews & names | **Fictional** sample testimonials |
| +15% peak surcharge & seeded peak dates | **Project pricing rule** (seasonality concept is real — §4) |
| Site code, design, booking/cancellation/admin logic | **Original project work** |
