Install Steps:
1) Install pimcore
2) create folder " bundles " on pimcore project root directory
3) Past "ShopifyBundle " folder in "bundles" folder
4) Add below code in config/bundles.php to register shopfiy bundle

return [
      ShopifyBundle\ShopifyBundle::class => ['all' => true],
];
