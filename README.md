# elio_test_project

Shopware 6 plugin created using Symfony and Shopware 6 frameworks.

/product/add/multiple endpoint offers a form, where user can add multiple products available in stock

By default there is 10 fields, but it can be set up in .env file. 

There is no restriction on product quantity, though there are filters down the code that add only max available quantity to cart.

There are hints while typing, showing product ID, product name, available stock and price. Products could be found by their names.
