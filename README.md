# Kunstmaan Extra Bundle

 * [Concepts](src/Resources/doc/concepts.md)
 * [Page Conventions](src/Resources/doc/page-conventions.md)
 * [Content Category](src/Resources/doc/content-category.md)
 * [Content Types](src/Resources/doc/content-type.md)
 * [Site Tree](src/Resources/doc/site-tree.md)
 * [Form types](src/Resources/doc/form-types.md)
 * [Misc](src/Resources/doc/misc.md)
 * [Search](src/Resources/doc/search.md)
 
 
## Bug fixes

 * Sortable `AdminList` should respect the query builder filters when reordering items: [https://github.com/Kunstmaan/KunstmaanBundlesCMS/issues/1066](#1066).
 
   Instead of the standard classes use `AbstractAdminListController`, `AdminListConfigurator` and `SortableInterface` from `ArsThanea\KunstmaanExtraBundle\AdminList` namespace.
