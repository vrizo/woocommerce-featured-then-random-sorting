=== Featured First then Random for WooCommerce ===
Contributors: killbill-sbor
Tags: woocommerce, sorting, random, featured
Requires at least: 4.3
Tested up to: 4.6.1
Stable tag: trunk
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

This plugin adds extra product sorting option to your WooCommerce shop: Featured items First then Random.

== Description ==

This is a small plugin for Wordpress which adds extra product sorting option: Featured First then Random. It can be very useful for stores with a large amount automatically imported items, e.g., from AliExpress. 

It works perfectly with WooCommerce v. 2.3 and newer.

It is available in English and Russian for now.

* This plugin uses Session seed (unique session id) to randomize item order so you can use random product list even with pagination!
* It is automatically reset when user opens the first page or by timer (every hour/day. Timer starts when user opens your store).
* Demo web-site is available [here](http://toryjoy.ru/shop/?orderby=featured_first_then_random).


== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/woocommerce-featured-then-random-sorting` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress
1. Use the Settings &rarr; Plugin Name screen to configure the plugin
1. New sorting option will be available on all your store pages.

Also you can customize the plugin:
1. Go to Dashboard &rarr; WooCommerce &rarr; Settings &rarr; Items &rarr; Display<br>
1. Here you can choose default sorting option, default sorting label, randomize event and Featured First then Random custom label.

== Frequently Asked Questions ==

= Is it possible to rename the sorting option? =

Yes, just go to WooCommerce Settings &rarr; Products &rarr; Display settings and enter your own label for Default or Featured First sorting option.

= How can I add "Featured Item" label to my existing item? =
There are two ways to do that:
1. To mark a product as featured go to Products &rarr; Products. Find the product you would like to feature and click the Featured Star. Featured products will have the star icon filled in.
2. Or just open your item and change Catalog visibility option: enable "Featured Product" checkbox.

= Will it work with pagination? =
Yes! This plugin uses Session seed (unique session id) to randomize item order so you can use random product list even with pagination!

== Screenshots ==

1. Product catalog with the plugin
2. Display settings

== Changelog ==

= 1.0 =
* Added capability to change randomize event (hourly, daily or on the first page)
* Improved styling of settings page
* Code improvements
* New logo

= 0.9 =
* Initial release