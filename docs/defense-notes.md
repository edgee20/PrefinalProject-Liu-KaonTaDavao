# Defense notes — Ej F. Liu

Use the actual source files during practice. Explain the decision first, then show the method that implements it.

| Concept | Explanation using this project |
|---|---|
| Resource routing | `Route::resource('eateries', EateryController::class)` defines index, create, store, show, edit, update, and destroy. The HTTP method and URI select the action. |
| Implicit model binding | Laravel reads the typed controller parameter, looks up the corresponding model from the URL value, and returns 404 if no model matches. |
| Slugs and IDs | Eatery overrides `getRouteKeyName()` to return `slug`, giving readable eatery URLs. Other models use Laravel's default ID binding. Renaming an eatery changes its slug. |
| Foreign keys | Migrations require each child record to refer to an existing parent. The permit's unique eatery ID also limits each eatery to one permit. |
| Fillable fields | `$fillable` lists attributes Eloquent may assign from an array. Controllers also use only validated input, so unrelated submitted fields are not saved. |
| Relationship choices | Districts have many eateries; dishes and reviews belong to an eatery; users have many reviews; eateries have one permit. Eateries and features can each have many of the other. |
| Has many through | `District::reviews()` follows district → eateries → reviews. Reviews need no district ID because their eatery already identifies the district. |
| Pivot table | `eatery_feature` stores eatery/feature pairs. Its composite primary key prevents duplicate pairs without requiring a separate ID. |
| Attach, sync, detach | Attach adds pairs, sync replaces a selection, and detach removes pairs. None of these deletes the feature itself. |
| Factories versus seeders | Factories describe generated sample records. Seeders decide which records are required, how many to make, and how to connect them. The personalized eatery is created manually. |
| Closed factory state | `closed()` overrides the default `is_open` value with false. The eatery seeder makes exactly three factory eateries using that state. |
| Eager loading | With/load fetch related records before rendering. Card and review loops read those loaded collections instead of causing another query for each row. WithCount/withAvg calculate aggregates. |
| Grouped filters | The name OR specialty search is inside a nested where group. District and feature conditions are then ANDed with that whole group, preventing search matches from bypassing filters. |
| Form Requests | StoreEateryRequest, UpdateEateryRequest, and StoreReviewRequest authorize and validate before the controller action runs. Controllers receive their validated arrays. |
| Review authorization | StoreReviewRequest checks the route-bound eatery's open flag. A closed eatery returns 403 before the controller can create a review. Hiding the form alone would not protect a crafted POST. |
| Update uniqueness | The unique rules ignore the route-bound eatery and its own permit. This permits an unchanged valid save while still rejecting another record's name or permit. IDs are taken from bound models, not submitted fields. |
| Components and partials | Eatery-card and rating are reusable Blade components with explicit props. Nav, flash, errors, and the shared form are included partials. The layout provides the shared page structure. |
| Old input and errors | `old()` restores submitted values; `@error` displays a field message. `@selected` and `@checked` restore selections. If an edit submits no features, the old-input check keeps them empty instead of restoring saved features. |
| Unchecked checkboxes | Browsers omit unchecked checkbox values. The hidden Open field submits 0; a checked checkbox submits 1 after it. Controllers also default missing Open input to false. |
| CSRF | `@csrf` adds the session token checked by Laravel, protecting form submissions from requests forged on another site. |
| Method spoofing | HTML forms send GET or POST. `@method('PUT')` and `@method('DELETE')` let Laravel route POST forms to update/delete actions. |
| Relationship deletion | The destroy action deletes the permit, dishes, and reviews, detaches feature pairs, and then deletes the eatery. Districts, features, and users remain. |
| Transactions | Multi-record eatery changes run together. An exception rolls the transaction back so an eatery is not left half-created or half-deleted. |
| Flash and redirect | Controllers redirect to a named route and flash the required status message to the session. The shared flash partial displays it on the next page. |
| Slug collisions | Different names can produce the same slug. A custom name-validation callback rejects collisions, empty slugs, and the reserved create URL before a database write. |
| Permit dates | The display checks whether expiry is before today. Form validation requires after today. An expired permit therefore needs a new date during editing. |

## Practice walk-through

1. Show the seed counts and open Carinderia ni Liu.
2. Follow its district link and explain the through-review count.
3. Follow Tita Liu's reviewer link and explain the two belongsTo relationships on Review.
4. Combine all three directory filters and move between pages.
5. Submit an invalid create form, then create a temporary eatery with a unique permit and future expiry.
6. Edit it, change features, and explain the pivot rows that sync removes or adds.
7. Post a review, close the eatery, and explain why the review form disappears and the server rejects submissions.
8. Delete the temporary eatery and explain the relationship cleanup order.

Practice questions: Why does deleting an eatery not delete its district? What would happen if the search OR were not grouped? Why must a uniqueness exception use the bound model? Why does a hidden checkbox input matter? Why is a closed form's absence insufficient protection?
