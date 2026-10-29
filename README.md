# Google Reviews

> Google reviews for Statamic.

Syncs your Google reviews (via [Apify](https://apify.com)) into a `reviews` collection and keeps a `review` global with the average score and review count up to date.

Reviewer avatars and review photos are downloaded once into a `reviews` asset container, so entries never depend on Google's expiring image URLs:

```
{review_id}/avatar.jpg
{review_id}/photo-1.jpg
{review_id}/photo-2.jpg
```

Entries reference them through the `reviewer_photo` (single asset) and `review_images` (list of assets) fields.

This addon only handles the data. The frontend (review cards, masonry, summary) lives in [`letsdeploy/statamic-basecamp`](https://github.com/letsdeploy-nl/statamic-basecamp) as the `reviews` section and the `social_proof` component.

## How to Install

``` bash
composer require letsdeploy/statamic-reviews
php artisan vendor:publish --tag=ldp-reviews-collections
php artisan vendor:publish --tag=ldp-reviews-globals
php artisan vendor:publish --tag=ldp-reviews-assets
```

The asset container uses a `reviews` filesystem disk. If your `config/filesystems.php` does not define one, the addon registers a local disk at `public/reviews` served from `/reviews`. Define your own `reviews` disk (e.g. S3) to override it.

Add to your `.env`:

```
APIFY_TOKEN=
GOOGLE_PLACE_IDS=        # comma separated
GOOGLE_MAX_REVIEWS=1000
```

## How to Use

``` bash
php please reviews:sync
```

Images are only downloaded for reviews that do not have them stored yet, so re-running the sync is cheap.

In templates:

```antlers
{{ reviewer_photo }}{{ glide :src="url" width="60" height="60" }}{{ /reviewer_photo }}
{{ review_images }}{{ glide :src="url" width="400" }}{{ /review_images }}
```
