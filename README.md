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
```php
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

## External handler example
In your configuration  you must define your callback functions that must return collection of materials (or structures) for creating sitemap.
If you have very big collection of data, we recommend to use two parameters for limiting in your function : 
 * ```integer $limitStart``` Limit start position
 * ```boolean & $response``` Return true if function must be called again

###Example using parameters
 
 ```php
function getBigProducts($limitStart = 0, & $response = false) {
	$query = dbQuery('material')->cond('type', 2)->limit($limitStart*200, 200);
	$count_query = clone $query;

    if ($count_query->count() < 200) {
        $response = false;
    } else {
        $response = true;
    }
    return $query->exec();
}
```

###Simple using example

 ```php
function getCompaniesForSitemap() {
    return dbQuery('material')->cond('type', 3)->exec();
}
```
 
Developed by [SamsonOS](http://samsonos.com/)
