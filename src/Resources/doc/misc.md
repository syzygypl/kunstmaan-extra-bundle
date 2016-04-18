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
