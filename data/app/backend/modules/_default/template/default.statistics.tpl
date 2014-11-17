<h2>{$chartName}</h2>
<div id="{$chartId}" style="width: 100%; height: 355px;"></div>

<script type="text/javascript">
    {literal}
    (function () {
        {/literal}
        var chartData = {$raw};
        var chartId = '{$chartId}';
        {literal}

        for (var row in chartData) {
            if (chartData.hasOwnProperty(row)) {
                chartData[row]['date'] = new Date(chartData[row]['date']);
            }
        }

        var chart;

        // this method is called when chart is first inited as we listen for "dataUpdated" event
        // SERIAL CHART
        chart = new AmCharts.AmSerialChart();
        chart.marginTop = 0;
        chart.autoMarginOffset = 5;
        chart.pathToImages = "http://www.amcharts.com/lib/images/";
        chart.zoomOutButton = {
            backgroundColor:'#000000',
            backgroundAlpha:0.15
        };
        chart.dataProvider = chartData;
        chart.categoryField = "date";

        // AXES
        // category
        var categoryAxis = chart.categoryAxis;
        categoryAxis.parseDates = true; // as our data is date-based, we set parseDates to true
        categoryAxis.minPeriod = "DD"; // our data is daily, so we set minPeriod to DD
        categoryAxis.dashLength = 2;
        categoryAxis.gridAlpha = 0.15;
        categoryAxis.axisColor = "#DADADA";

        // GRAPHS
        // first graph


        var valueAxis = new AmCharts.ValueAxis();
        valueAxis.axisColor = "#FF6600";
        valueAxis.axisThickness = 2;
        valueAxis.gridAlpha = 0;
        chart.addValueAxis(valueAxis);

        var graph = new AmCharts.AmGraph();
        graph.valueAxis = valueAxis; // we have to indicate which value axis should be used
        graph.title = "Registrations";
        graph.valueField = "count";
        graph.bullet = "round";

        chart.addGraph(graph);

        // CURSOR
        var chartCursor = new AmCharts.ChartCursor();
        chartCursor.cursorPosition = "mouse";
        chart.addChartCursor(chartCursor);

        // SCROLLBAR
        var chartScrollbar = new AmCharts.ChartScrollbar();
        chart.addChartScrollbar(chartScrollbar);

        // LEGEND
        var legend = new AmCharts.AmLegend();
        legend.marginLeft = 110;
        chart.addLegend(legend);

        // WRITE
        chart.write(chartId);
    })();

    {/literal}
</script>