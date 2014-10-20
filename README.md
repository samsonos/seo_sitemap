#SamsonPHP module for creating sitemap of web-site for [SamsonPHP](http://samsonphp.com) framework

> Module can automatically create common sitemap or sitemap for different sections (products, categories, etc..)
> compression using external tools.

## Automatic sitemaps generation
For creating sitemap you must visit url ```[domain]/sitemapcreate```
System will automatically create
 * ```sitemap.xml``` xml file with general sitemap

###Module Configuration
Available two configurable parameters:
 * ```array $schema``` Array, where key is sitemap name and value is array of callback function, which returns array of elements for creating XNL 'url' objects and url prefix. Also value can contain collection of arrays
 * ```string $imageSchemaHandler``` Callback function, which returns collection of images for creating XML

##Example configuration class for this module:
```
class SitemapConfig extends \samson\core\Config
{
    public $__module = 'sitemapcreate';

    public $schema = array(
        'products' => array(
            array('getSmallProducts', 'small/'),
            array('getBigProducts', 'big/')
        ),
        'companies' => array('getCompaniesForSitemap, 'companies/'),
        'pages' => array('getPagesForSitemap', ''),
    );

    public $imageSchemaHandler = 'getImagesForSitemap';
}
```

Developed by [SamsonOS](http://samsonos.com/)