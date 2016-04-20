# Site Tree

Site tree returns a tree-like structure (branch instances) with subpages of given page/node. The service is registered in the container as `kunstmaan_extra.site_tree` and is available as a `get_page_children` twig helper.

There are following options you can pass to modify behaviour:

 * `depth` (default: 1) — limit the resuts to this many levels
 * `refName` — an array of class names. for instance: only fetch
 * `include_root` (false) — by default the result doesn’t return the parent node, only the children
 * `include_hidden` (false) - add nodes with the ”hide from menu” option selected
 * `include_offline` (false) - include unpublished
 * `limit` (no limit) — return not more than limit results

Twig helper has a litte different interface. Since it’s harder to use class names in twig templates, the `refName` option is exposed as a second parameter and instead of class names it expects friendly names (converted to class names using [Content Type](content-type.md)).

```twig
{% set articles = get_page_children(page, 'article', {"limit": 3}) }
```


## Navigation

Navigation component provides a simple prev/next page via two twig functions:

 * `get_navigation_next(page)`
 * `get_navigation_prev(page)`

Those functions return a Branch instance and return only siblings of the same type as a given page.
