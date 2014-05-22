<?php
/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>
 * on 21.05.14 at 10:51
 */
 
namespace samsonos\seo;
use samson\activerecord\dbRelation;

/**
 * Class for interacting with SamsonPHP
 * @author Vitaly Egorov <egorov@samsonos.com>
 * @copyright 2014 SamsonOS
 * @version 1.0.0
 */
class Sitemap extends \samson\core\ExternalModule
{
    /** @var string module identifier */
    public $id = 'sitemap';

    /** @var array  */
    public $schema = array();

    /** @var string External Handler for images sitemap */
    public $imageSchemaHandler = '';

    /** @var string attribute for creating map */
    public $attribute = 'Url';

    /**
     * @param $array - array with users function and url prefix
     * @param \SimpleXMLElement $xml - XML string
     *
     * @return mixed - XML string
     */
    private function xmlCreate($array, $xml)
    {
        $callback = $array[0];
        $prefix = $array[1];

        // If exists external handler
        if (is_callable($callback)) {
            // Call external handler
            foreach (call_user_func($callback) as $item) {
                $url = $xml->addChild('url');
                if (isset($item->Url)) {
                    $url_path = url()->build().$prefix.$item->Url;
                } else {
                    $url_path = url()->build().$prefix.$item;
                }
                $url->addChild('loc', $url_path);
            }
        }
        return $xml;
    }

    public function __HANDLER()
    {
        /** @var $generalMap String Main sitemap path */
        $generalMap = 'sitemap.xml';

        // delete file if exists
        if (file_exists($generalMap)) {
            unlink($generalMap);
        }

        /** @var \SimpleXMLElement $sitemap XML template for general sitemap */
        $sitemap = new \SimpleXMLElement(<<<XML
<?xml version='1.0' standalone='yes'?>
        <sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/siteindex.xsd">
        </sitemapindex>
XML
    );

        foreach ($this->schema as $key=>$value) {
            /** @var \SimpleXMLElement $xml XML template for sitemap */
            $xml = new \SimpleXMLElement(<<<XML
<?xml version='1.0' standalone='yes'?>
    <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
    </urlset>
XML
            );

            if (is_array(($this->schema[$key][0]))) {
                $fileName = 'sitemap-'.$key.'.xml';

                foreach ($this->schema[$key] as $key1 => $value1) {
                    $xml = $this->xmlCreate($value1, $xml);
                }

                $this->saveXml($fileName, $xml);

            } else {
                $fileName = 'sitemap-'.$key.'.xml';

                $xml = $this->xmlCreate($value, $xml);

                $this->saveXml($fileName, $xml);
            }

            // Add elemet in general map
            $sitemapBlock = $sitemap->addChild('sitemap');
            $sitemapBlock->addChild('loc', 'http://'.$_SERVER['HTTP_HOST'].'/'.$fileName);
            $sitemapBlock->addChild('lastmod', date('c', filemtime($fileName)));
        }

        if (is_callable($this->imageSchemaHandler))  {
            $sitemap_images = 'sitemap-images.xml';
            $this->createImageSitemap($sitemap_images, call_user_func($this->imageSchemaHandler));

            // Add elemet in general map
            $sitemapBlock = $sitemap->addChild('sitemap');
            $sitemapBlock->addChild('loc', 'http://'.$_SERVER['HTTP_HOST'].'/'.$sitemap_images);
            $sitemapBlock->addChild('lastmod', date('c', filemtime($sitemap_images)));
        }

        // Write XML in general sitemap file
        $file = fopen($generalMap, "w");
        fwrite($file, $sitemap->asXML());
        fclose($file);

        $this->html(' ');
    }

    /** Creating sitemape-images.xml
     * @param $filename
     * @param $array - array of images
     */
    private function createImageSitemap($filename, $array)
    {
        /** @var \SimpleXMLElement $xml XML template for sitemap-image */
        $xml = new \SimpleXMLElement(<<<XML
<?xml version="1.0" encoding="UTF-8"?>
 <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
</urlset>
XML
        );

        foreach ($array as $key => $value) {
            $url = $xml->addChild('url');
            $url->addChild('loc', url()->build().$value->Url);
            $gallery = dbQuery('gallery')->MaterialID($value->id)->exec();

            foreach ($gallery as $image) {
                $img = $url->addChild('image:image', null, 'http://www.google.com/schemas/sitemap-image/1.1');
                $img->addChild('image:loc', $image->Path);
                $img->addChild('image:title',  str_replace(array(',','"'),'',strip_tags($value->Name)));
            }
        }

        $this->saveXml($filename, $xml);
    }

    /** Save XML object in file
     * @param $filename
     * @param \SimpleXMLElement $xml
     */
    private function saveXml($filename, $xml)
    {
        // delete file if exists
        if (file_exists($filename)) {
            unlink($filename);
        }

        // Write XML in file
        $file = fopen($filename, "w");
        fwrite($file, $xml->asXML());
        fclose($file);
    }
}
 