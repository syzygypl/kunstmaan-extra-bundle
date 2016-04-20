# Form types

## Node entity

Use: `ArsThanea\KunstmaanExtraBundle\Form\NodeEntityType`

The idea behind it is to select an existing page entity. Since pages are cloned for each `node_version` it would be difficult to use the standard `entity` type. You could select a `node_translation` instead, but that doesn’t limit you to page types you need; and it may be more prefferable to you to have `HasNodeInterface` instead of a `NodeTranslation`. 

This form type will give you only the pages associated to a publisged, non-deleted and a latest version CMS node. Just add it to your builder:

```php
$builder->add('relatedArticle', NodeEntityType::class, [
   'label' => "Choose one article to promote"
   'page_name' => 'article'
]);
```

Instead of providing a class name like the in `entity` type, use `page_name` with a friendly name. The [Content Type](content-type.md) service will convert it to a class name.


## `ArsThanea\KunstmaanExtraBundle\Form\PageUrlType`

Uses a link chooser in the CMS panel, but saves a `NodeTranslation` on the entity.

## Advanced select

`ChoiceType` elements now have `advanced_select` option to change them into a `select2` widget (a dynamic select with
autocompletion, etc). This works by adding `advanced-select` class to the widget and the native Kunstmaan mechanisms
take over.
