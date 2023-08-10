# MediaLounge Developer Test

## Notes
- The configuration is found under `Catalog > Catalog > Country Exclusions` so that it is grouped with other product-level configurations.

- The `excluded_countries` attribute needs to be added to a product via the `Add Attribute` button, which avoids it cluttering up the product edit screen for products that don't need this functionality.

- The IP -> Country lookup service is [ip2c](https://ip2c.org).

- This took approximately 3 hours to complete initial functionality. Extra hour to investigate and implement the backend and source model to make the editing experience better.
