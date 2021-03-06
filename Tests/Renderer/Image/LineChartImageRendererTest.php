<?php

namespace Outspaced\ChartsiaBundle\Tests\Chart\Renderer\Image;

use Outspaced\ChartsiaBundle\Chart\Axis;
use Outspaced\ChartsiaBundle\Chart\Charts;
use Outspaced\ChartsiaBundle\Chart\Component;
use Outspaced\ChartsiaBundle\Chart\Config;
use Outspaced\ChartsiaBundle\Chart\DataSet;
use Outspaced\ChartsiaBundle\Chart\Renderer;
use Outspaced\ChartsiaBundle\Chart\Type;
use Outspaced\ChartsiaBundle\Tests\Traits;

class LineChartImageRendererTest extends \PHPUnit_Framework_TestCase
{
    use Traits\AssertStringContainsOnce;

    /**
     * @var Image
     */
    protected $object;

    /**
     * @var string
     */
    protected $renderedChart;

    /**
     */
    public function providerLineChart()
    {
        $type = new Type\LineChart();

        $title = (new Config\Title())
            ->setTitle('Wahey what a chart')
            ->setColor(new Component\Color('00FF00'));

        $size = (new Config\Size())
            ->setHeight(300)
            ->setWidth(800);

        $margin = new Config\Margin(50, 80, 20, 100);

        $legend = (new Config\Legend())
            ->setPosition('up')
            ->setFontSize(23)
            ->setColor(new Component\Color('FFFF44'));

        $data = [
            "02/04" => 63,
            "09/04" => 72,
            "16/04" => 91,
            "04/06" => 84,
            "11/06" => 97
        ];

        $dataSet = (new DataSet\DataSet())
            ->setData($data)
            ->setColor(new Component\Color('0000FF'))
            ->setLegend(new DataSet\Legend('Set 2'));

        $dataSetCollection = (new DataSet\DataSetCollection())
            ->add($dataSet);

        $bottomAxis = (new Axis\Axis())
            ->createLabels(array_keys($data), 1);

        $leftAxis = (new Axis\Axis())
            ->setGridlines(new Axis\Gridlines(20));

        $chart = (new Charts\Chart())
            ->setType($type)
            ->setTitle($title)
            ->setSize($size)
            ->setMargin($margin)
            ->setLegend($legend)
            ->setDataSetCollection($dataSetCollection)
            ->setLeftAxis($leftAxis)
            ->setBottomAxis($bottomAxis);


        $return = (new Renderer\Image())
            ->render($chart);

        return [[$return]];
    }

    /**
     */
    public function testCanBeInstantiated()
    {
        $this->assertInstanceOf(Renderer\Image::class, new Renderer\Image());
    }

    /**
     * @dataProvider providerLineChart
     * @covers Outspaced\ChartsiaBundle\Chart\Renderer\Image::render
     */
    public function testRenderContainsUrl($chart)
    {
        $this->assertStringContainsOnce(
            'http://chart.googleapis.com/chart',
            $chart
        );
    }

    /**
     * @dataProvider providerLineChart
     * @covers Outspaced\ChartsiaBundle\Chart\Renderer\Image::render
     */
    public function testRenderContainsChartType($chart)
    {
        $this->assertStringContainsOnce(
            'cht=lc',
            $chart
        );
    }

    /**
     * @dataProvider providerLineChart
     * @covers Outspaced\ChartsiaBundle\Chart\Renderer\Image::render
     */
    public function testRenderContainsChartSize($chart)
    {
        $this->assertStringContainsOnce(
            'chs=800x300',
            $chart
        );
    }

    /**
     * @dataProvider providerLineChart
     * @covers Outspaced\ChartsiaBundle\Chart\Renderer\Image::render
     */
    public function testRenderContainsMargins($chart)
    {
        $this->assertStringContainsOnce(
            'chma=50,80,20,100',
            $chart
        );
    }

    /**
     * @dataProvider providerLineChart
     * @covers Outspaced\ChartsiaBundle\Chart\Renderer\Image::render
     */
    public function testRenderContainsLegend($chart)
    {
        $this->assertStringContainsOnce(
            'chdls=FFFF44,23',
            $chart
        );
    }

    /**
     * @dataProvider providerLineChart
     * @covers Outspaced\ChartsiaBundle\Chart\Renderer\Image::render
     */
    public function testRenderContainsTitleColor($chart)
    {
        $this->assertStringContainsOnce(
            'chts=00FF00&',
            $chart
        );
    }

    /**
     * @dataProvider providerLineChart
     * @covers Outspaced\ChartsiaBundle\Chart\Renderer\Image::render
     */
    public function testRenderContainsAxes($chart)
    {
        $this->assertStringContainsOnce(
            'chxt=x,y',
            $chart
        );
    }

    /**
     * @dataProvider providerLineChart
     * @covers Outspaced\ChartsiaBundle\Chart\Renderer\Image::render
     */
    public function testRenderContainsBottomAxisLabels($chart)
    {
        $this->assertStringContainsOnce(
            'chxl=|0:|02%2F04|09%2F04|16%2F04|04%2F06|11%2F0',
            $chart
        );
    }

    /**
     * @dataProvider providerLineChart
     * @covers Outspaced\ChartsiaBundle\Chart\Renderer\Image::render
     */
    public function testRenderDoesNotContainAxisPositions($chart)
    {
        $this->assertNotContains(
            'chxp=',
            $chart
        );
    }

    /**
     * @dataProvider providerLineChart
     * @covers Outspaced\ChartsiaBundle\Chart\Renderer\Image::render
     */
    public function testRenderContainsGridlines($chart)
    {
        $this->assertStringContainsOnce(
            'chg=0,20,0,0,0,0',
            $chart
        );
    }

    /**
     * @dataProvider providerLineChart
     * @covers Outspaced\ChartsiaBundle\Chart\Renderer\Image::render
     */
    public function testRenderContainsDataSet($chart)
    {
        $this->assertStringContainsOnce(
            'chd=t:63,72,91,84,97',
            $chart
        );
    }

    /**
     * @dataProvider providerLineChart
     * @covers Outspaced\ChartsiaBundle\Chart\Renderer\Image::render
     */
    public function testRenderContainsLineColours($chart)
    {
        $this->assertStringContainsOnce(
            'chco=0000FF',
            $chart
        );
    }

    /**
     * @dataProvider providerLineChart
     * @covers Outspaced\ChartsiaBundle\Chart\Renderer\Image::render
     */
    public function testRenderContainsDataSetLabel($chart)
    {
        $this->assertStringContainsOnce(
            'chdl=Set+2',
            $chart
        );
    }

    /**
     * @dataProvider providerLineChart
     * @covers Outspaced\ChartsiaBundle\Chart\Renderer\Image::render
     */
    public function testRenderContainsTitle($chart)
    {
        $this->assertStringContainsOnce(
            'chtt=Wahey+what+a+chart',
            $chart
        );
    }
}
