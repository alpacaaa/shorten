# Shorten Field

A Symphony CMS field that shorten entry ids to tiny alphanumeric strings (like **u8X**).
You could build, for example, your own in-site url shortener service having urls like `$root/u8X`. How cool is that?


* Author: [Marco Sampellegrini](http://github.com/alpacaaa)
* Release date: 30th March 2012
* Version: 0.4

This is based on the [Entry URL Field](http://github.com/nickdunn/entry_url_field/) by Nick Dunn.


## Use

Let's say you have an url structure like `post/category-handle/post-id`.
You don't want to rely on services like bit.ly but that url is just too long. Time to give this a try then!

The first thing you'd want to do is to add the field to your `Post` section.

There's a `Url` field which should reflect your url structure so, in this case, 
we're going to populate it with a value of `post/{entry/category/item/@handle}/{entry/@id}`.

It's pretty straightforward, you just use xpath expressions as you normally do when building `@href` links.
That's a relative url, so `$root` will be prepended automatically.


## Filtering

Ok, this is cool so far, but you need a way to specify if and how the redirect should happen.
This is done through datasource filtering.

Symphony 2.3.1 supports page params in index page, which is exactly what you need for this extension to work.  
Simply add a parameter to your index page (let's call it `r`). Now create a datasource which filters by 
this field with `{$r}` as value and attach it to the index page. Whenever a page isn't matched (you don't have 
page handles like `55u`, don't you? :D), Symphony will fallback to the index page and execute the datasource.
If a match is found, it will automatically redirect to the url specified in the entry. Cool stuff.