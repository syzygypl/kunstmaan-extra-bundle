# Concepts

Kunstmaan bundles CMS already introduces two distinct domains: pages and nodes. This bundle both extends this idea and combines some of those domains together.

## Domains

 * `nodes` – represents CMS status, hierarchy, urls
 * `pages` — this is the content and content only
 * `categories` — this bundle introduces a simple structure perfect for building menus, breadcrumbs, etc. More about it in the [Content Category section](content-category.md)
 * `branches / refs` — contain both titles, urls and hierarchy and page information on hand

## Strict vs loose parameter types

Since there are a lot of different object types you may encounter, most of the twig functions / helpers won’t expect a certain type but rather try to figure out it’s way with what you’ll provide. For example, the `ref_id` filter will return the page id for any of the node entities, pages, branches, categories and even search results. 

A lot of effort put into this whole bundle and it’s services is to be able to get results regardles of object at hand. 

There is even a way of creating your own ways of extracting page id from various objects. Just implement the `RefIdProviderInterface` and register the class in the container tagged with `kunstmaan_extra.ref_id_provider`.

## Performance optimization

The [Public node versions module](public-node-versions.md) was created mainly for performance optimizations. It uses only one mysql query and uses an array as a result (instead of entities). The main drawback is that it returns scalars or lightweight objects (branches / categories) that may be hard to work with (they are neither nodes or pages).

The best way to deal with it and not hurting the site’s performance is to switch context between the lightweight page views and cached blocks, subrendered using esi. For instance:

```twig
{# ArticleHolder\view.html.twig #}
<h1>{{ page.title }}</h1>
<ul>
  {% for article in get_page_children(page) %}
    {#
      iterate over lightweight branch instances,
      and convert them to pages in the cached subrequest
    #}
  	<li>{{ esi(controller("AcmeBundle:Article:teaser", { "article": article } )) }}</li>
  {% endfor %}
</ul>
```

