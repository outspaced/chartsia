<?php

namespace Outspaced\GoogleChartMakerBundle\Chart\Config;

/**
 * Contains config elements that are common to all charts
 *
 * @author Alex Brims <alex.brims@gmail.com>
 */
class Size
{
    /**
     * @var int
     */
    protected $height;

    /**
     * @var int
     */
    protected $width;

    public function __construct($height=NULL, $width=NULL)
    {
        if ($height) {
            $this->setHeight($height);
        }

        if ($width) {
            $this->setWidth($width);
        }
    }

    /**
     * @param  int  $height
     * @return self
     */
    public function setHeight($height)
    {
        $this->height = $height;

        return $this;
    }

    /**
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param  int  $width
     * @return self
     */
    public function setWidth($width)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

}