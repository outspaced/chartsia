<?php

namespace Outspaced\ChartsiaBundle\Chart\Renderer;

use Outspaced\ChartsiaBundle\Chart\Charts;
use Outspaced\ChartsiaBundle\Chart\Config;
use Outspaced\ChartsiaBundle\Chart\Component;
use Outspaced\ChartsiaBundle\Chart\Axis;

/**
 * This library has been deprecated by Google, although the API is still available
 */
class Image
{
    /**
     * @var string
     */
    const BASE_URL = 'http://chart.googleapis.com/chart?';

    /**
     * @param string $type
     */
    protected function renderType($type = null)
    {
        if ($type === null) {
            return '';
        }

        return 'cht='.$type.'&';
    }

    /**
     * @param  Config\Size $size
     * @return string
     */
    protected function renderSize(Config\Size $size = null)
    {
        if ($size === null) {
            return '';
        }

        return 'chs='.implode('x', $size->getDimensions()).'&';
    }

    /**
     * @param  Config\Margin $margin
     * @return string
     */
    protected function renderMargin(Config\Margin $margin = null)
    {
        if ($margin === null) {
            return '';
        }

        return 'chma='.implode(',', $margin->getDimensions()).'&';
    }

    /**
     * @param  array $legendLabels
     * @return string
     */
    protected function renderLegendLabels(array $legendLabels = [])
    {
        if (empty($legendLabels)) {
            return '';
        }

        return 'chdl='.implode('|', $legendLabels).'&';
    }

    /**
     * @param  array $lineColors
     * @return string
     */
    protected function renderLineColors(array $lineColors = [])
    {
        if (empty($lineColors)) {
            return '';
        }

        return 'chco='.implode(',', $lineColors).'&';
    }

    /**
     * @param  Config\Title $title
     * @return string
     */
    protected function renderTitle(Config\Title $title = null)
    {
        if ($title === null) {
            return '';
        }

        $url = 'chtt='.urlencode($title->getTitle()).'&';

        if ($title->getColor() !== null) {
            $url .= 'chts='.$title->getColor()->getColor().'&';
        }

        return $url;
    }

    /**
     * @param Config\Legend $chartLegend
     * @return string
     */
    protected function renderChartLegend(Config\Legend $chartLegend = null)
    {
        if ($chartLegend === null) {
            return '';
        }

        //chdlp=<opt_position>|<opt_label_order>
        return 'chdls='.$this->renderColor($chartLegend->getColor()).','.$chartLegend->getFontSize().'&';
    }

    /**
     * Oh this is in dire need of a refactor
     *
     * @param  Axis\AxisCollectionCollection $axisCollectionCollection
     * @return string
     */
    protected function renderAxisCollectionCollection(Axis\AxisCollectionCollection $axisCollectionCollection = null)
    {
        if ($axisCollectionCollection === null) {
            return '';
        }

        /**
         * STEP 1
         */
        $possibleAxisKeys = [
            't' => 'top',
            'x' => 'bottom',
            'y' => 'left',
            'r' => 'right'
        ];

        $actualAxisKeys = [];

        foreach ($possibleAxisKeys as $possibleAxisKey => $possibleAxisName) {
            $method = 'get'.ucwords($possibleAxisName).'AxisCollection';

            $count = $this->countTheAxis($axisCollectionCollection->$method());

            $actualAxisKeys = array_pad($actualAxisKeys, count($actualAxisKeys) + $count, $possibleAxisKey);
        }

        $axesData = $actualAxisKeys;
        $axesData = array_filter($axesData);

        if (empty($axesData)) {
            return '';
        }

        $urlData = 'chxt='.implode(',', array_values($axesData)).'&';

        /**
         * STEP 2
         */
        // render the labels
        $labels = [];
        $positions = [];
        foreach ($possibleAxisKeys as $possibleAxisKey => $possibleAxisName) {
            $method = 'get'.ucwords($possibleAxisName).'AxisCollection';

            $localLabels = $this->renderAxisCollectionLabels($axisCollectionCollection->$method());
            $labels = array_merge($labels, $localLabels);

            $localPositions = $this->renderAxisCollectionPositions($axisCollectionCollection->$method());
            $positions = array_merge($positions, $localPositions);
        }

        $positions = array_filter($positions);
        $labels = array_filter($labels);

        // Labels
        if ( ! empty($labels)) {
            $urlData .= 'chxl=';
            foreach ($labels as $labelKey => $labelValue) {
                $urlData .= $labelKey .':|'.$labelValue.'|';
            }
            $urlData .= '&';
        }

        if ( ! empty($positions)) {
            $urlData .= 'chxp=';
            foreach ($positions as $positionKey => $positionValue) {
                $urlData .= $positionKey .','.$positionValue.'|';
            }
            $urlData = rtrim($urlData, "|");
            $urlData .= '&';
        }

        return $urlData;
    }

    /**
     * Needs to be moved to a renderAxis class
     *
     * @param  Axis\AxisCollection $axisCollection
     * @return int;
     */
    protected function countTheAxis(Axis\AxisCollection $axisCollection = null)
    {
        if ($axisCollection === null) {
            return 0;
        }

        return $axisCollection->count();
    }

    /**
     * Needs to be moved to a renderAxis class
     *
     * @param  Axis\AxisCollection $axisCollection
     * @return array
     */
    protected function renderAxisCollectionLabels(Axis\AxisCollection $axisCollection = null)
    {
        if ($axisCollection === null) {
            return [];
        }

        $labelArray = [];

        foreach ($axisCollection as $axis) {
            $labelArray[] = $this->extractLabel($axis->getLabel());
        }

        return $labelArray;
    }

    protected function extractLabel(Axis\Label $label = null)
    {
        if ($label === null) {
            return '';
        }

        return $label->getLabel();
    }

    /**
     * Needs to be moved to a renderAxis class
     *
     * @param  Axis\AxisCollection $axisCollection
     * @return array
     */
    protected function renderAxisCollectionPositions(Axis\AxisCollection $axisCollection = null)
    {
        if ($axisCollection === null) {
            return [];
        }

        $positionArray = [];

        foreach ($axisCollection as $axis) {
            $positionArray[] = $this->extractPosition($axis->getLabel());
        }

        return $positionArray;
    }

    protected function extractPosition(Axis\Label $label = null)
    {
        if ($label === null) {
            return '';
        }

        return $label->getPosition();
    }

    /**
     * Needs to be moved to a renderAxis class
     *
     * Actually this doesn't work, these are still Axis objects
     *
     * @param  Axis\AxisCollection $axisCollection
     * @return array
     */
    protected function renderAxisCollection(Axis\AxisCollection $axisCollection = null)
    {
        if ($axisCollection === null) {
            return [];
        }

        return $axisCollection->getAxes();
    }

    /**
     * @param  Component\Color $color
     * @return string
     *
     * This is now DUPLICATED - need to make the decision if the renderers will extend a
     * common class now
     */
    protected function renderColor(Component\Color $color = null)
    {
        if ($color === null) {
            return '';
        }

        return $color->getColor();
    }

    /**
     * @param  Charts\BaseChart $chart
     * @return string
     */
    public function render(Charts\BaseChart $chart)
    {
        $url = self::BASE_URL;

        $url .= $this->renderType($chart->getType());
        $url .= $this->renderSize($chart->getSize());
        $url .= $this->renderMargin($chart->getMargin());
        $url .= $this->renderChartLegend($chart->getLegend());
        $url .= $this->renderTitle($chart->getTitle());
        $url .= $this->renderAxisCollectionCollection($chart->getAxisCollectionCollection());

        // DATA SETS
        $data = [];
        $lineColors = [];
        $legendLabels = [];

        foreach ($chart->getDataSetCollection() as $dataSet) {

            $data[] = implode(',', $dataSet->getData());

            $lineColors[] = $this->renderColor($dataSet->getColor());

            if ($legend = $dataSet->getLegend()) {
                $legendLabels[] = urlencode($legend->getLabel());
            }
        }

        // Dataset data
        $url .= 'chd=t:'.implode('|', $data).'&';

        $url .= $this->renderLineColors($lineColors);
        $url .= $this->renderLegendLabels($legendLabels);

        return $url;
    }
}
