import * as am4core from "@amcharts/amcharts4/core";
import * as am4charts from "@amcharts/amcharts4/charts";
import am4lang_es_ES from "@amcharts/amcharts4/lang/es_ES";
import {Defaults} from "../../defaults";
import $ from 'jquery';

export class Estadisticas {
    constructor() {
        Defaults.ajaxSettings();
    }
    async getTradesChart(coin: string){
        const {data: {trades: data}}:ApiResponse = await $.ajax({
            url: 'trades/data',
            data:{coin}
        });
        this.loadChart($('#chartdiv'), data, {});
    }

    private loadChart($element, data, options) {
        // Create chart instance
        const chart = am4core.create($element.get(0), am4charts.XYChart);
        chart.language.locale = am4lang_es_ES;
        chart.language.locale["_decimalSeparator"] = ".";
        chart.language.locale["_thousandSeparator"] = ",";

        // Add data
        chart.data = data;

        // Create axes
        const dateAxis = chart.xAxes.push(new am4charts.DateAxis());
        const valueAxis = chart.yAxes.push(new am4charts.ValueAxis());

        // Set input format for the dates
        chart.dateFormatter.inputDateFormat = "yyyy-MM-dd H:mm";
        dateAxis.groupData = false;
        dateAxis.dateFormats.setKey("minute", "MMMM d, yyyy H:mm");
        dateAxis.baseInterval = {
            "timeUnit": "minute",
            "count": 1
        }

        dateAxis.min = (new Date(2018, 10, 1)).getTime();

        // Create series
        const series = chart.series.push(new am4charts.LineSeries());
        series.dataFields.valueY = "value";
        series.dataFields.dateX = "date";
        series.tooltipText = "{value}"
        series.strokeWidth = 2;
        series.minBulletDistance = 15;

        // Drop-shaped tooltips
        series.tooltipText = '$' + `{value.formatNumber('#,##0.00')}`;
        series.tooltip.background.cornerRadius = 20;
        series.tooltip.background.strokeOpacity = 0;
        series.tooltip.pointerOrientation = "vertical";
        series.tooltip.label.minWidth = 40;
        series.tooltip.label.minHeight = 40;
        series.tooltip.label.textAlign = "middle";
        series.tooltip.label.textValign = "middle";

        // Make bullets grow on hover
        const bullet = series.bullets.push(new am4charts.CircleBullet());
        bullet.circle.strokeWidth = 2;
        bullet.circle.radius = 4;
        bullet.circle.fill = am4core.color("#fff");

        const bullethover = bullet.states.create("hover");
        bullethover.properties.scale = 1.3;

        // Make a panning cursor
        chart.cursor = new am4charts.XYCursor();
        chart.cursor.behavior = "panXY";
        chart.cursor.xAxis = dateAxis;
        chart.cursor.snapToSeries = series;

        // Create vertical scrollbar and place it before the value axis
        chart.scrollbarY = new am4core.Scrollbar();
        chart.scrollbarY.parent = chart.leftAxesContainer;
        chart.scrollbarY.toBack();

        // Create a horizontal scrollbar with previe and place it underneath the date axis
        chart.scrollbarX = new am4charts.XYChartScrollbar();
        // chart.scrollbarX.series.push(series);
        chart.scrollbarX.parent = chart.bottomAxesContainer;
    }
}
