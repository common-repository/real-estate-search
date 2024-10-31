=== Real Estate Search ===
Contributors: dom-rep
Donate link: http://www.altijdzon.nl/real-estate-plugin/
Tags: realestate,posts,plugin,links,tags,search,tag, real estate, real-estate
Requires at least: 1.5
Tested up to: 2.9
Stable tag: trunk

Real Estate Search plugin enables you to use browse through listings using tag combinations. Although it can be used on its own, it is meant to complements Real-Estate plugin by the same author. 

== Description ==

**Real Estate Agents**: make your property listings navigation more usable!

**[Download now!](http://downloads.wordpress.org/plugin/real-estate-search.zip)**

Some features:

* Simplifies the process of browsing listings

== Installation ==

1. Unzip and upload to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Add in theme file `archive.php` something like this:

// If this is a tag archive
if(is_tag()&&function_exists('res_get_tags'))res_get_tags();

4. Remember to delete WP-Cache if you have that plugin active. Otherwise, you may not see effect of this plugin immediately.

== Frequently Asked Questions ==

= Plugin shows wrong count numbers. What is going on? = 

[Read this](http://blog.andreineculau.com/2008/07/delete-wordpress-26-revisions/)

Above instructions require mysql version 5.+. If you need help with older mysql versions, contact me.

= Can I use this plugin without using Real-Estate plugin? =

Yes. You just need to edit a theme file `archive.php`

= How to add new features to the plugin? =

Submit inquiry through the author [dom rep](http://www.altijdzon.nl/real-estate-plugin/) plugin homepage - there's an email

== Other ==

= For frequent updates visit this page =

* **[real estate plugins](http://www.altijdzon.nl/real-estate-plugin/)**

== Demo ==

[Villas and Homes](http://www.altijdzon.nl/tag/villa/)
