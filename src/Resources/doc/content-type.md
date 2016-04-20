# Content types

## Introduction

Content type is all about dealing with different page types. It’s main goal is to map entity class names to human readable and „easy” names (and back). For example:

```
AcmeVendor\FoobarBundle\Entity\Pages\ProductOverviewPage -> product overview
```

This way you can pass the friendly name in URL or keep in it the database, and replace them back with class names when needed.

## Configuration

You need to register your pages using [the Pages Configuration](https://github.com/Kunstmaan/KunstmaanBundlesCMS/blob/master/src/Kunstmaan/NodeBundle/Resources/doc/PagesConfiguration.md). Make sure to mark the root page (`is_homepage: true`).

## Usage

The service is registered as `kunstmaan_extra.content_type` in the container:

 * `getContentTypeClass` converts friendly name to a class name
 * `getFriendlyName` does the reverse
 * `getAllContentTypeClasses` returns a friendly name to class name mapping for all the pages

## Twig functions

 * `page_type_name` function returns the friendly name given a page or a class name

## Other twig helpers

For each of the pages there is a set of fetchers, filters and testers. For example, given `ArticlePage`:

 * `3 | to_article` fetcher fetches an Article Page by id
 * `collection | article_pages` filter returns only Article Pages from collection
 * `page is article_page` returns true if the page is an Article Page
