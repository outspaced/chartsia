<?php

namespace Outspaced\ChartsiaBundle\Chart\Renderer;

use Outspaced\ChartsiaBundle\Chart\Axis;
use Outspaced\ChartsiaBundle\Chart\Charts;
use Outspaced\ChartsiaBundle\Chart\Config;
use Outspaced\ChartsiaBundle\Chart\Component;
use Outspaced\ChartsiaBundle\Chart\DataSet;
use Outspaced\ChartsiaBundle\Chart\DataSet\DataSetCollection;

class JavaScript
{
    /**
     * @var \Twig_Environment
     */
    protected $engine;

    /**
     * @param \Twig_Environment $engine
     */
    public function __construct(\Twig_Environment $engine)
    {
        $this->engine = $engine;
    }

    /**
     * @param Charts\Chart $chart
     * @return string
     */
    public function render(Charts\Chart $chart)
    {
        return $this->renderWithTwig($chart, $this->engine);
    }

    /**
     * @param Charts\BaseChart $chart
     * @param \Twig_Environment $engine
     */
    public function renderWithTwig(Charts\Chart $chart, \Twig_Environment $engine)
    {
        $vars = [
            'title' => $this->renderTitle($chart->getTitle()),
            'title_color' => $this->renderTitleColor($chart->getTitle()),

            'chart_height' => $this->renderChartHeight($chart->getSize()),
            'chart_width'  => $this->renderChartWidth($chart->getSize()),

            'chart_legend' => $this->renderChartLegend($chart->getLegend()),

            'data_sets' => $this->renderDataSets(
                $chart->getDataSetCollection(),
                $chart->getBottomAxis()
            ),
        ];

        $return = $engine->render(
            'OutspacedChartsiaBundle:Charts:javascript.html.twig',
            $vars
        );

        return $return;
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

        return $title->getTitle();
    }

    /**
     * @param  Config\Title $title
     * @return string
     */
    protected function renderTitleColor(Config\Title $title = null)
    {
        if ($title === null) {
            return '';
        }

        return $this->renderColor($title->getColor());
    }

    /**
     * @param  Component\Color $color
     * @return string
     */
    protected function renderColor(Component\Color $color = null)
    {
        if ($color === null) {
            return '';
        }

        return $color->getColor();
    }

    /**
     * @param  Config\Size $size
     * @return string
     */
    protected function renderChartHeight(Config\Size $size = null)
    {
        if ($size === null) {
            return '';
        }

        return $size->getHeight();
    }

    /**
     * @param  Config\Size $size
     * @return string
     */
    protected function renderChartWidth(Config\Size $size = null)
    {
        if ($size === null) {
            return '';
        }

        return $size->getWidth();
    }

    /**
     * This will transform the datasets into an array, where the hAxis label is the first element of each row
     *
     * eg:
     *
     * $data[0] = ['January', 10, 20];
     * $data[1] = ['February', 35, 45];
     *
     * @param DataSet\DataSetCollection $dataSetCollection
     */
    protected function renderDataSets(DataSet\DataSetCollection $dataSetCollection, Axis\Axis $bottomAxis)
    {
        $data = [];

        // First put the labels into the resulting array
        foreach ($bottomAxis->getLabels() as $label) {
            $data[][] = $label->getLabel();
        }

        // Now add the data sets
        foreach ($dataSetCollection as $dataSet) {
            foreach ($dataSet->getData() as $dataIndex => $dataItem) {
                $data[$dataIndex][] = $dataItem;
            }
        }

        return $data;
    }

    /**
     * @param  DataSet\DataSetCollection $dataSets
     * @return array
     */
    protected function renderDataSetLegends(DataSet\DataSetCollection $dataSets = null)
    {
        if ($dataSets === null) {
            return [];
        }

        $legends = [''];

        foreach ($dataSets as $dataSet) {
            $legends[] = $this->renderDataSetLegend($dataSet->getLegend());
        }

        // If the array doesn't have any non-empty elements, then return an empty array
        if (!array_filter($legends)) {
            return [];
        }

        return $legends;
    }

    /**
     * @param  DataSet\Legend $legend
     * @return string
     */
    protected function renderDataSetLegend(DataSet\Legend $legend = null)
    {
        if ($legend === null) {
            return '';
        }

        return $legend->getLabel();
    }

    protected function renderChartLegend(Config\Legend $legend = null)
    {
        // hmmm is this right?
        if ($legend === null) {
            return [
                'color' => ''
            ];
        }

        return [
            'color' => $this->renderColor($legend->getColor())
        ];
    }
}
