<?php
/**
 * Sitemap class file
 *
 * @package Greengrape
 */

namespace Greengrape;

use Greengrape\NavigationItem;

/**
 * Sitemap
 *
 * Detects the map of available content files from content directory
 *
 * @package Greengrape
 * @author Jansen Price <jansen.price@gmail.com>
 * @version $Id$
 */
class Sitemap
{
    /**
     * Content directory
     *
     * @var string
     */
    protected $_contentDir = '';

    /**
     * Map of URLs to files
     *
     * @var array
     */
    protected $_map = array();

    /**
     * Main navigation
     *
     * @var array
     */
    protected $_mainNavigation = array();

    /**
     * Base URL
     *
     * @var string
     */
    protected $_baseUrl = '/';

    /**
     * Constructor
     *
     * @param string $contentDir Content directory path
     * @return void
     */
    public function __construct($contentDir, $baseUrl = '/')
    {
        $this->setContentDir($contentDir);
        $this->setBaseUrl($baseUrl);

        $this->_map = $this->createMap();
        $this->_mainNavigation = $this->createMainNavigation();
    }

    /**
     * Set content directory
     *
     * @param string $contentDir Content directory
     * @return \Greengrape\Sitemap
     */
    public function setContentDir($contentDir)
    {
        $this->_contentDir = $contentDir;
        return $this;
    }

    /**
     * Get content directory
     *
     * @return string
     */
    public function getContentDir()
    {
        return $this->_contentDir;
    }

    /**
     * Set Base URL
     *
     * @param string $url URL
     * @return \Greengrape\Sitemap
     */
    public function setBaseUrl($url)
    {
        $this->_baseUrl = $url;
        return $this;
    }

    /**
     * Get the base URL
     *
     * @return string
     */
    public function getBaseUrl($file = '')
    {
        if ($file == '') {
            return $this->_baseUrl;
        }

        return $this->_baseUrl . $file;
    }

    /**
     * Get content location for given URL
     *
     * @param string $url Url
     * @return string|null
     */
    public function getLocationForUrl($url)
    {
        if (array_key_exists($url, $this->_map)) {
            $location = new Location($this->_map[$url]);
            return $location;
        }

        return new Location($url);
    }

    /**
     * Get main navigation items
     *
     * @return void
     */
    public function getMainNavigation()
    {
        return $this->_mainNavigation;
    }

    /**
     * Create map of available folders and files in the content directory
     *
     * @return array
     */
    public function createMap()
    {
        $files = self::rglob($this->getContentDir() . DIRECTORY_SEPARATOR . '*');

        $map = array();
        
        foreach ($files as $file) {
            if (is_dir($file)) {
                $isDir = true;
            } else {
                $isDir = false;
            }

            // Remove the common first part
            $file = str_replace($this->getContentDir() . '/', '', $file);

            $url = str_replace('.md', '', $file);
            $url = NavigationItem::translateOrderedName($url);

            if ($url == 'index') {
                // If we're left with just index, change to home page
                $map['/'] = $file;
                continue;
            }

            if ($isDir) {
                $map[$url . '/'] = $file;
                $map[$url] = array('canonical' => $url . '/');
                continue;
            }

            // If the last segment is 'index', add an entry for the
            // file without the word 'index'
            $urlSegments = explode('/', $url);

            if (end($urlSegments) == 'index') {
                $url = str_replace('index', '', $url);

                $map[$url] = $file;

                $map[rtrim($url, '/')] = array('canonical' => $url);
                continue;
            }

            $map[$url] = $file;
        }

        return $map;
    }

    /**
     * Create main navigation
     *
     * @return array
     */
    public function createMainNavigation()
    {
        $path = $this->getContentDir() . DIRECTORY_SEPARATOR . '*';
        $items = glob($path, GLOB_ONLYDIR);

        $mainNavigation = array();
        foreach ($items as $item) {
            $item = str_replace($this->getContentDir() . '/', '', $item);

            if (substr($item, 0, 1) == '_') {
                // skip items that start with underscore, they are hidden
                continue;
            }

            $mainNavigation[] = new NavigationItem($item, $item . '/', $this->getBaseUrl());
        }

        if (!empty($mainNavigation)) {
            $home = new NavigationItem('Home', '/', $this->getBaseUrl());
            array_unshift($mainNavigation, $home);
        }

        return $mainNavigation;
    }

    /**
     * Create sub navigation items for a main item
     *
     * @param \Greengrape\NavigationItem $item Main navigation item
     * @return array
     */
    public function createSubNavigationItems($item)
    {
        if (!$item || $item->getHref() == '/') {
            // If no item available or if it is the home page, ignore the sub 
            // navigation
            return array();
        }

        $location = $this->getLocationForUrl($item->getHref());

        if (dirname($location->getFile()) == '.') {
            $basePath = $this->getContentDir() . DIRECTORY_SEPARATOR
                . $location->getFile() . DIRECTORY_SEPARATOR;
        } else {
            $basePath = $this->getContentDir() . DIRECTORY_SEPARATOR
                . dirname($location->getFile()) . DIRECTORY_SEPARATOR;
        }

        $items = glob($basePath . '*', GLOB_ONLYDIR);

        $navigationItems = array();
        foreach ($items as $subItem) {
            $subItem = str_replace($basePath, '', $subItem);

            if (substr($subItem, 0, 1) == '_') {
                // skip items that start with underscore, they are hidden
                continue;
            }

            $baseUrl = $item->getHref();
            $navigationItems[] = new NavigationItem(
                $subItem, $baseUrl . $subItem . '/', $this->getBaseUrl()
            );
        }

        return $navigationItems;
    }

    /**
     * Recursive Glob
     * 
     * @param string $pattern Pattern
     * @param int $flags Flags to pass to glob
     * @param string $path Path to glob in
     * @return array
     */
    public static function rglob($pattern, $flags = 0, $path = '')
    {
        if (!$path && ($dir = dirname($pattern)) != '.') {
            if ($dir == '\\' || $dir == '/') {
                // This gets into infinite loop
                return array();
            }
            return self::rglob(
                basename($pattern),
                $flags, $dir . DIRECTORY_SEPARATOR
            );
        }

        $paths = glob($path . '*', GLOB_ONLYDIR | GLOB_NOSORT);
        $files = glob($path . $pattern, $flags);

        foreach ($paths as $p) {
            $files = array_merge(
                $files, self::rglob($pattern, $flags, $p . DIRECTORY_SEPARATOR)
            );
        }

        return $files;
    }
}
