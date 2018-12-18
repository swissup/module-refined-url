# Refined Url

This module forces Magento to generate product urls where url path is not longer then 255 charaters.

**Refined Urls** can help stores with very deep category trees and/or very long product urls. When url path is longer then 255 characters then remaining part of url path cutted of during save into DB table `url_rewrite`. And it can lead to ***'Unique constraint violation found'*** error in future. This error occurs while save product or category and have no info that can help to identify where is the source of the problem.

## Shortened URL

When Magento generated too long url then module places in with its shortened version. Check example below.

Magento generate url 261 characters long:

```
https://argento.mage/absolutly-fantastic-gear/.../fitness-equipment/sprite-foam-roller.html
```

Module replaces it with new url 255 characters long:

```
https://argento.mage/absolutly-fantastic-gear/.../fitness-equipment/sprite-foa~1.html
```

## Only verification (no shortened urls)

It is possible to disable shortened url and only show detailed message when original url path is too long. There is no admin interface at this moment. So you have to modify `config.xml`. Set flag `swissup_refinedurl/product_url/shortened` to `0`.

## Installation

```bash
cd <magento_root>
composer config repositories.swissup/module-refined-url vcs git@github.com:swissup/module-refined-url.git
composer require swissup/module-refined-url
bin/magento module:enable Swissup_RefinedUrl
bin/magento setup:upgrade
```