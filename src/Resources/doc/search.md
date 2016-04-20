# Search improvements

**Those features are in a transitional state and the goal is to integrate them with the KunstmaanBundlesCMS library.**

## Elastic configuration

You may fine-tune the ElasticSearch backend:

```yam
# app/config/config.yml

kunstmaan_extra:
    search:
        url: "https://user:password@localhost:9200/"
        replicas: 1
        shards: 4
```

## Chain Search Provider

The existing search provider does not iterate over all of it’s providers and instead only uses the default one.
This component changes that allowing you to have multiple search backends.

For example using the [SyzygyPL/KunstmaanAlgoliaBundle](https://github.com/syzygypl/kunstmaan-algolia-bundle)

## Canceling node indexing

You may have a page that is indexed, but only under some conditions. Use the event:

 * `NodeTranslationEvent::NODE_TRANSLATION_INDEX_BEFORE` 
 
To cancel the indexation of any given `nodeTranslation`
