# Prefinal rubric audit

Audited November 19, 2026 against the official Kaon Ta! Davao handout. Pass means the requirement has supporting implementation and verification; this is not a promised grade. The author has reviewed the documentation and relationship wording and authorized the final commit and push. Publication timing is checked after the push; live-defense understanding remains unverified.

| Criterion | Requirement | Evidence | Status |
|---|---|---|---|
| A1 | Seven exact project tables, columns, and migration types; default users retained | Seven `2026_11_19_*` migrations; `DatabaseSchemaTest`; inspected SQLite schema | Pass |
| A2 | Required foreign/unique/composite keys and relationship-based cleanup | `DatabaseSchemaTest`; `EateryController::destroy`; deletion integration test | Pass |
| A3 | Working down methods and fresh migration/seeding | Each new migration rolled back individually in memory; fresh seeded clean clone succeeded | Pass |
| B1 | Exact 13 routes, controller actions, invokable home, resource controller | Actual route list in README; `RoutesAndLayoutTest` | Pass |
| B2 | Slug binding for every eatery parameter and unknown-slug 404 | `Eatery::getRouteKeyName`; route and management tests, including nested review URLs | Pass |
| B3 | Required CRUD/review redirects, flash text, named links, and POST delete forms | `EateryManagementTest`; Blade route helpers, CSRF and method spoofing | Pass |
| B4 | Thin controllers, validated input, Str::slug, relationship writes, sync | Three Form Requests; controller source and management tests | Pass |
| C1 | Shared layout, exact title, Vite/Tailwind, escaped output, successful build | `layouts/app`; actual screenshots; source inspection; clean-clone production build | Pass |
| C2 | Eatery/rating components, required partials, 1/2/3-column grid | Components used on home/directory/district and reviews; browser widths 375/768/1440 | Pass |
| D1 | Required factories/closed state, five seeders, exact totals | `DatabaseSeedingTest`; fresh-seed repetitions; clean-clone database counts | Pass |
| D2 | Six exact districts and features | DistrictSeeder/FeatureSeeder and exact-value seed tests | Pass |
| D3 | Personalized users, hashed passwords, hand-made eatery/features/permit | UserSeeder/EaterySeeder; exact-value and password-hash assertions | Pass |
| D4 | Personalized dishes/reviews, factory bounds, attach, expired permit, closed eatery rules | Seed tests and seeder source; development personalized-record queries | Pass |
| E1 | District/eatery and eatery/dish relationships in both directions and used | Models; `ModelRelationshipsTest`; card district links and dish list | Pass |
| E2 | Eatery/review and user/review inverses, reviewer links/count/newest-first list | Model tests; detail/reviewer views and management integration test | Pass |
| E3 | Both belongsToMany directions, feature links and working edit sync | Model pivot tests; directory filter test; update feature assertions | Pass |
| E4 | Permit hasOne/belongsTo, relationship creation/update and expired display | Model/management tests; past-date versus today boundary verified | Pass |
| E5 | District hasManyThrough and exact review count text | Model test excludes another district's reviews; page test verifies count text | Pass |
| F1 | Eager loading in controllers; no lazy relationship queries in Blade loops | Controller/view inspection; page integration test with lazy loading disabled | Pass |
| F2 | One-decimal ratings/counts, exact no-review text, accurate counts/top three | HomeAndDirectoryTest; rating component; live screenshots | Pass |
| F3 | Combined grouped search, retained filters, A–Z, six/page, retained query string, empty state | HomeAndDirectoryTest and browser grid checks | Pass |
| G1 | All store rules and authorization true | StoreEateryRequest; valid creation and invalid-field integration tests | Pass |
| G2 | Update uniqueness exceptions, unchanged valid edit, unchecked open false | UpdateEateryRequest and unchanged/renamed/closed update tests | Pass |
| G3 | Review rules and closed-eatery 403 with no saved review | StoreReviewRequest; invalid-review and crafted closed-POST tests | Pass |
| G4 | All seven custom validation messages exactly personalized | Exact-message assertions for create/update/reviews; real HTTP invalid submission | Pass |
| H1 | Summary, field errors, old values, selected/checked, CSRF on all three forms | Shared form and review form; unchecked-feature test; live invalid POST; validation screenshot | Pass |
| I1 | At least eight meaningful commits before the cutoff | Eight meaningful development commits including this approved final documentation commit; count and remote are checked during publication | Pass |
| I2 | Correct public repository and no prohibited local/dependency/AI files | GitHub repository verified public; tracked-path/ignore/dependency inspection | Pass |
| I3 | Complete README, relationship explanations in own words, actual routes, three screenshots | Completed README and actual route output; three genuine screenshots; author reviewed and approved relationship wording | Pass |
| I4 | Understandable course-scoped implementation and live-defense understanding | Standard MVC/Eloquent/Form Requests; walkthroughs provided; professor assesses defense | Unverified |

## Verification record

- Automated suite: 36 tests, 546 assertions passed after the functional changes.
- Schema: exact columns, unique indexes, foreign-key enforcement, defaults, one permit per eatery, and duplicate-pivot prevention verified.
- Rollback: every project table's down method verified individually in an in-memory database; re-migration and fresh migration succeeded.
- Seeding: exact personalized values and totals checked; three additional isolated fresh seeds with different random seeds passed.
- HTTP: validation summary, custom errors, and old input verified through a real redirected form submission.
- Browser: actual screenshots captured; responsive grid verified at three widths; create form controls have labels; screenshots visually inspected.
- Clean clone: `b9b1a16` installed in a fresh temporary folder using only the handout's setup steps. All eight checked pages returned HTTP 200, including create/edit, district, and reviewer pages. Personalized data and totals matched. No application files were changed to fix the clone. npm only normalized the lockfile's root name to the temporary folder name; dependency versions were unchanged.
- Fresh/reset commands were confined to test databases and the disposable clean clone. The development database was not reset.

## Submission handoff

The author reviewed the final documentation and authorized commit 8 and its push. The publication procedure verifies that the remote main branch matches the final local commit, the history contains exactly eight meaningful commits, and publication finishes before the cutoff.

The author must submit the repository link to Moodle before 3:30 PM Philippine time on November 19, 2026, and prepare for the live defense. No Moodle submission is automated. The professor ultimately assesses authenticity and whether the author can explain the submitted code.
