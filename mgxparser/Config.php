<?php
/**
 * Defines Config class.
 *
 * @package RecAnalyst
 */

namespace RecAnalyst;

/**
 * Class Config.
 *
 * Configuration class.
 * Config implements configuration constants used for RecAnalyst.
 *
 * @package RecAnalyst
 */
class Config
{

    /**
     * Defines a path (absolute or relative) to directory where we store
     * resources required for generating research timelines.
     *
     * @var string
     */
    public $resourcesDir;

    /**
     * Defines a width of the map image we wish to generate.
     * @var int
     */
    public $mapWidth;

    /**
     * Defines a height of the map image we wish to generate.
     * @var int
     */
    public $mapHeight;

    /**
     * Defines width and height of one research tile in research timelines image.
     * @var int
     */
    public $researchTileSize;

    /**
     * Defines vertical spacing between players in research timelines image.
     * @var int
     */
    public $researchVSpacing;

    /**
     * Defines background image for research timelines image.
     * @var string
     */
    public $researchBackgroundImage;

    /**
     * Defines color for Dark Age in the research timelines image.
     * Array consist of red, green, blue color and alpha.
     * @var array
     */
    public $researchDAColor;

    /**
     * Defines color for Feudal Age in the research timelines image.
     * @var array
     * @see $researchDAColor
     */
    public $researchFAColor;

    /**
     * Defines color for Castle Age in the research timelines image.
     * @var array
     * @see $researchDAColor
     */
    public $researchCAColor;

    /**
     * Defines color for Imperial Age in the research timelines image.
     * @var array
     * @see $researchDAColor
     */
    public $researchIAColor;

    /**
     * Determines if to show players positions on the map.
     * @var bool
     */
    public $showPositions;
    
    //Add by lichifeng <lichifeng.com>
    public $researchfontFile;
    
    public $stringEncoding;

    /**
     * Class constructor. Tries to do some default stuff.
     *
     * @return void
     */
    public function __construct()
    {
        $baseDir = dirname(__FILE__) . DIRECTORY_SEPARATOR;
        $this->resourcesDir = $baseDir . 'resources' . DIRECTORY_SEPARATOR;
        $this->stringEncoding = 'gbk';

        // map image generation
        $this->mapWidth = 600;
        $this->mapHeight = 300;
        $this->showPositions = true;
        // research image generation
        $this->researchTileSize = 25;
        $this->researchVSpacing = 0;
        $this->researchBackgroundImage = $this->resourcesDir . 'background.jpg';
        $this->researchDAColor = array(0xd9, 0x53, 0x4f, 0x00); // red
        $this->researchFAColor = array(0x5c, 0xb8, 0x5c, 0x00); // green
        $this->researchCAColor = array(0x42, 0x8b, 0xca, 0x00); // blue
        $this->researchIAColor = array(0xf0, 0xad, 0x4e, 0x00); // orangy
        $this->researchfontFile = $this->resourcesDir . 'STXIHEI.TTF';
    }

}
