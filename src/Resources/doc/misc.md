# Misc

## Request Parameter Converter

The Doctrine Param Converter is enabled by default and will automatically convert scalar values to entities. It will even do this automatically without any indication if you type hint the controller arguments. But sometimes you don’t know the name of the class — you’d rather use an interface or make a reusable component for multiple websites. This param converter will help you to do the same thing Doctirne Converter does but using the [Content Type](content-type.md) to convert the argument name to a class name:

```php

/**
 * @Route("/article/{article}/rate", methods={"POST"})
 * @ParamConverter("article", converter="kunstmaan_extra.page")
 **/
 public function rateAction($article)
 ```
 
Or maybe you’d like to accept various pages sharing a common interface. In this example the argument name won’t be used, but rather the value of `type` attribute.
 
```php

/**
 * @Route("/foo/bar/{type}/{page}")
 * @ParamConverter("page", converter="kunstmaan_extra.page", "options": { "type_field": "type" })
 **/
 public function fooBarAction(HasFooBarInterface $page)
 ```
 
## Date Formatter

### Usage

```twig
{{ "today"|pretty_date("d MMMM, EEEE, H:mm") }}

{# Custom format preset, different for each locale #} 
{{ "today"|pretty_date("home_page") }}
```

### Configuration

Configure custom formats for each locale:

```yml
# app/config/config.yml

kunstmaan_extra:
  date_formats: 
    home_page:
      en: "d MMMM, EEEE, H:mm"
      fr: …
```

## Form attributes

Easily add HTML attributes to a form view in templates. Decouples `FormTypes` from their templates. Instead of setting
`attr` in the `FormType` options you can do it / extend it in the template:

```twig
{% set form = form|form_attributes({
    "child.birth_date": "-additional-modifier",
    "profile.email": {
        "title": "Hello!",
        "class": "zzz"
    }
}) %}
```

## Convert elements to use BEM notation

When using WYSIWYG all you get is simple tags with no classes, but when using BEM you’re required to target only 
classes instead of HTML elements in your CSS. This filter automatically adds given classes to given elements, and
strips all other tags by the way:

```twig
 {{ '<div><p>Hello <b>World!</b></p></div>'|bem({
      'p': "landingPageAnswer__text",
      'b': null,
    })
 }}
 
 {# Output:
     <p class="landingPageAnswer__text">Hello <b>World!</b></p>
 #}
```

### Configuration

You can extract common bem settings to `config.yml`:

```yaml
kunstmaan_extra:
    bem:
        common: 
            p: landingPageAnswer__text
            b: null
```

And use the key instead of configuration array:

```twig
{{ content|bem("common") }}
```

### Advanced usage

Since the filter uses a whitelist aproach, all other tags and attributes are removed. To whitelist them, use an array
with null values or a dot notation. Notice, that those two filters are the same:

```yaml
kunstmaan_extra:
    bem:
        allowed_attributes:
            a: 
                class: bem__anchor
                href: null
                target: null
                
        dot_notation: 
            a.href.target: bem__anchor
```

## Automatically fix orphans

Replaces phases like `a cat` with `a&nbsp;cat` to keep them in the same line.

```twig
{{
   page.content|orphans
}}
```

## AdminList Generator

To make routes work, add this entry to your bundle routing config: 

```yaml
app_admin_lists:
    resource: '@WebsiteBundle/Resources/config/routing/admin'
    type:     directory
    prefix:   /{_locale}/admin/
    requirements:
         _locale: "%requiredlocales%"

```
