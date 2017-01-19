# Liip Imagine (image manipulation) extensions
 
## Automatically generate srcsets

To automatically resize images for your responsive website use the `srcset` filter:

```twig
<img src="{{ media.url|imagine_filter("foo") }}" srcset="{{ media|srcset("foo") }}">
```

To specify your desired breakpoints / widths use the configuration:

```yaml
# app/config/config.yml

kunstmaan_extra:
    srcset:
        default_filter: 'srcset'
        breakpoints: [320, 640, 960, 1280, 1600, 1920, 2560]
```

For each of those widths a separate image will be genarated and the `srcset` filter will be applied, so you can add
your custom transformations there. If you need a specific filter, you may use the first parameter to the filter (`foo`
in the example above). If an imgix filter is defined by that name (see below) it will be used.

## Imgix filters

If you’d like to use [imgix.com](http://imgix.com/) transformations on your images, you need to configure a „Web folder”
source at imgix and add it’s name to the configuration:

```yaml
# app/config/config.yml

kunstmaan_extra:
    imgix:
        bucket: acme-bucket (without the .imgix.net suffix)
        presets:
            teaser:
                sat: -100
                gam: 5
                high: -15
            grayscale:
                sat: -100
                gam: 5
                high: -15
```

The imgix presets are just a set of imgix transformations. Their name **must** have a imagine `filter_set` counterpart 
(you don’t need to add any actual filters), because everything is handled by the imagine in the end. To run the transformations
use a twig filter:

```twig
<img src="{{ media.irl | imgix_filter("teaser") }}">
```

## Optimize images

The `tinypng_com` [imagine post processor](http://symfony.com/doc/current/bundles/LiipImagineBundle/post-processors.html)
allows you to optimize your image sizes. To use it you need to define the [tinypng API key](https://tinypng.com/developers):

```yaml
# app/config/config.yml

kunstmaan_extra:
    tinypng_api_key: ~
    
# and then set it up as a post_processor:

liip_imagine:
    filter_sets:
        foo:
            post_processors: 
                tinypng_com: ~
```

It will be used autimatically for your `srcsets` if you use them.
