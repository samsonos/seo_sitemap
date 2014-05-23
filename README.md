#SamsonPHP module for creating sitemap of web-site for [SamsonPHP](http://samsonphp.com) framework

> Module can automatically create common sitemap or sitemap for different sections (products, categories, etc..)
> compression using external tools.

## Automatic sitemaps generation
For creating sitemap you must visit url ```[domain]/sitemap```
System will automatically create
 * ```sitemap.xml``` xml file with general sitemap

###Module Configuration
Available two configurable parameters:
 * ```array $schema``` Array, where key is sitemap name and value is array of callback function, which returns array of elements for creating XNL 'url' objects and url prefix
 * ```string $imageSchemaHandler``` Callback function, which returns array of images for creating XML

##Example configuration class for this module:
```
class SitemapConfig extends \samson\core\Config
{
    public $__module = 'sitemap';

    public $schema = array(
        'companies' => array('sitemap_companies', 'companies/'),
        'pages' => array('sitemap_pages', ''),
    );

    public $imageSchemaHandler = 'sitemap_images';
}
```

Developed by [SamsonOS](http://samsonos.com/)