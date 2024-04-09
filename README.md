# LEXO ACF SEO
SEO addon based on ACF.

---
## Versioning
Release tags are created with Semantic versioning in mind. Commit messages were following convention of [Conventional Commits](https://www.conventionalcommits.org/).

---
## Compatibility
- WordPress version `>=6.4`. Tested and works fine up to `6.5`.
- PHP version `>=7.4.1`. Tested and works fine up to `8.2.10`.

---
## Installation
1. Go to the [latest release](https://github.com/lexo-ch/acf-seo/releases/latest/).
2. Under Assets, click on the link named `Version x.y.z`. It's a compiled build.
3. Extract zip file and copy the folder into your `wp-content/plugins` folder and activate LEXO ACF SEO in plugins admin page. Alternatively, you can use downloaded zip file to install it directly from your plugin admin page.

---
## Filters
#### - `acfseo/location`
*Parameters*
`apply_filters('acfseo/location', $location);`
- $location (array) Change the locations where LEXO ACF SEO fields should be shown. By default it's shwon only on `page` post type.

Default `$location` array definition is:
```php
$location = [
    [
        [
            'param'     => 'post_type',
            'operator'  => '==',
            'value'     => 'page',
        ]
    ]
];
```
For durther changes use ACF logic for `location` of the groups.

#### - `acfseo/show-heading-2`
*Parameters*
`apply_filters('acfseo/show-heading-2', $show_h2);`
- $show_h2 (bool) Control the presence of the second title. This filter allows you to hide it on specific pages, pages with specific teplates, sepcific post types...

Default `$show_h2` value is `true`.

#### - `acfseo/heading-1-type`
*Parameters*
`apply_filters('acfseo/heading-1-type', $h1_type);`
- $h1_type (string) Accepted values are `editor` and `text`. It will always fallback to `text`, which is default value.

#### - `acfseo/admin/localized-script`
*Parameters*
`apply_filters('acfseo/admin/localized-script', $args);`
- $args (array) The array which will be used for localizing `acfseoAdminLocalized` variable in the admin.

#### - `acfseo/enqueue/admin-acfseo.js`
*Parameters*
`apply_filters('acfseo/enqueue/admin-acfseo.js', $args);`
- $args (bool) Printing of the file `admin-acfseo.js` (script id is `acfseo/admin-acfif.js-js`). It also affects printing of the localized `acfseoAdminLocalized` variable.

#### - `acfseo/seo-title/maxlength`
*Parameters*
`apply_filters('acfseo/seo-title/maxlength', $maxlength);`
- $maxlength (int) Increases the `maxlength` for Seo Title field.

#### - `acfseo/description/maxlength`
*Parameters*
`apply_filters('acfseo/description/maxlength', $maxlength);`
- $maxlength (int) Increases the `maxlength` for Description field.

#### - `acfseo/options-page/capability`
*Parameters*
`apply_filters('acfseo/options-page/capability', $args);`
- $args (string) Change minimum user capability for options page.

#### - `acfseo/options-page/parent-slug`
*Parameters*
`apply_filters('acfseo/options-page/parent-slug', $args);`
- $args (string) Change parent slug for options page.

---
## Actions
#### - `acfseo/init`
- Fires on LEXO ACF SEO init.

---
## Changelog
Changelog can be seen on [latest release](https://github.com/lexo-ch/acf-seo/releases/latest/).
