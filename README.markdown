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

Choose a page where you want the redirect to happen and pick a datasource which filters by `Post` section.
I know, I said you would end up with urls like `$root/aB4` but I was actually lying.
There are fairly simple workarounds for that, see next section for some hints.

Let's keep it simple for now, so our first goal is to make `$root/?r=aB4` redirect to our full url.
On our Index page, we have a datasource that shows the latest posts: we're going to use that one.
Add a new filter by this field with `{$url-r}` as value. That's it!


## I'm not a liar

With a simple rewrite rule, you can get `$root/aB4` to work.
I'm not going to show how to do that anymore, as you can simply achieve the same result using the 
[Url Router](http://github.com/symphonists/url_router/) extension. Cool stuff.
