# Content Category

## Introduction

This module introduces an idea of a category. It’s the most basic way of representing a
page with only an url, title and nodeId. Categories are great to build breadcrumbs, navigate
to parents / siblings, distinguish between your sites main sections, etc.

## Usage

The service is available from the container: `kunstmaan_extra.content_category` and via some twig functions:

 * main_category — returns first level parent of given page (or root page for 1st level pages).
    This is best for getting main sections of the website.

    ```
    Home Page > Main Section > Sub Section > Current Page
    ```

    Points to „Main Section”

 * parent_category — returns first parent
 * get_breadcrumbs - gets all the pages from the root to the current page

## is `under page` test

For convenience, twig has a `under page` test that tests if one of the given page parent’s match url or title. Given:

```
Home Page > Main Section > Sub Section > Current Page
```

The following twig will return:

```twig
{{ dump(page is under page "sub section") }} {# True #} 
{{ dump(page is under page "/main-section/sub-section") }} {# True #} 
{{ dump(page is under page "Current Page") }} {# False #} 
```

## Cache

The function results are cached. By default an in-memory cache us used, so it’s safe to call them multiple times during one request. If you’d like to speed up your website and use more permanent cache like redis or memcached, you’d have to overwrite `doctrine_cache` provider by the name `kunstmaan_extra_content_category`
