=== Link Products To Sendinblue Lists From Woocommerce ===
Contributors: Mze Ahmed
Tags: woocommerce, sendinblue, brevo
Requires at last: 5.1
Tested up to: 6.8
WC tested up to: 9.8.1
Stable tag: 2.0.9
Requires PHP: 8.0
Php tested up to: 8.3
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

== Description ==

Synchronize your WooCommerce customers with your Brevo (formerly Sendinblue) contact lists — automatically and conditionally.

This plugin lets you assign one or more Brevo contact lists to each WooCommerce product or variation. Once an order is completed, the buyer is automatically added to the corresponding list(s) — allowing you to run targeted marketing campaigns based on their purchases.

You can also define smart conditions (e.g., price threshold, user role) for simple products to fine-tune who gets added to which list.

= Features =

✔ Link any WooCommerce product to one or more Brevo contact lists
✔ Full support for product variations (each variation can be linked to a different list)
✔ Conditional rules for simple products:
  - Always add after purchase
  - Add if product price equals or exceeds a specific value
  - Add if customer has a specific user role
✔ Sync custom customer attributes (like phone, first name, etc.)
✔ Works with virtual and downloadable products
✔ Optimized for performance, with seamless integration into WooCommerce
✔ Clean and intuitive admin interface

No more manual imports. Save time and automate your email marketing based on what your customers actually buy.

== Requirements ==

- A Brevo (Sendinblue) account
- Your Brevo V3 API key
- WooCommerce plugin installed and activated

== Screenshots ==

1. Easily assign Brevo (Sendinblue) lists to each WooCommerce product.
2. Define advanced conditions for when to sync a customer to a list.
3. Manage product variations and link each variation to a specific Brevo list.
4. Customize the customer fields to sync with Brevo contact attributes.
5. View the Brevo list(s) linked to a product in the WooCommerce product edit screen.

== Changelog ==

= 2.0.7 - 06 May 2025 =
* Fixed: Empty api key

= 2.0.4 - 21 April 2025 =
* Fixed: Price type casting issue

= 2.0.2 - 26 March 2025 =
* Chore: Removed dev dependencies from vendor directory for cleaner deployment

= 2.0.1 - 27 March 2025 =
* Fixed: Re-enabled page reload after database upgrade completion

= 2.0.0 - 26 March 2025 =
* Feature: Support for WooCommerce product variations
* Feature: Assign specific Brevo lists to each product variation
* Feature: Conditional logic based on product price (equals, greater than or equal, less than)
* Improved: UI enhancements for assigning lists to products
* Improved: Separated conditions panel for simple and variable products
* Improved: Redirection to checkout with custom button labels for both simple and variable products
* Fixed: Variation list persistence on save
* Fixed: Accurate detection of product type and variation ID in backend and frontend

= 1.1.7.4 - 26 february 2025
* Improved: Improve code quality

= 1.1.7.3 - 26 february 2025
* fixed: Somes php notices

= 1.1.7.2 - 26 february 2025
* fixed: somes minor bugs

= 1.1.7.1 - 22 february 2025
* Improved: Improve code quality

= 1.1.7 - 22 february 2025
* Improved: Improve code quality

= 1.1.6 - 22 february 2025
* Fixed: json_decode error

= 1.1.5 - 25 may 2023
* Improved: Improve code quality
* Update: Update Senndiblue name to Brevo

= 1.1.1 - 1 december 2021
* Update: PHP 8 compatibility

= 1.1 - 29 august 2021
* Update: add required attibute on setting field

= 1.0.8 - 18 may 2021 =
* Fixed: 7.2 / 7.3 compatibilty

= 1.0.7 - 16 may 2021 =
* Fixed: Fatal error on plugin activation

= 1.0.6 - 16 may 2021 =
* Update: Upgrade Bootstrap to version 5

= 1.0.5 - 10 march 2021 =
* Fixed: Some PHP notice when Sendinblue api key is empty
* Improved: Hide User attibutes synch form when api key is empty

= 1.0.2 - 13 january 2021 =
* Fixed: Fatal error during Sendinblue Api call

= 1.0.1 - 12 janauary 2021 =
* Update: Plugin URI, Using WordPress uri instead of Github uri

= 1.0.0 - 11 january 2021 =
* Initial release
