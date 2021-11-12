import * as am4core from "@amcharts/amcharts4/core";
import * as am4charts from "@amcharts/amcharts4/charts";
import am4lang_es_ES from "@amcharts/amcharts4/lang/es_ES";
import {Defaults} from "../../defaults";
import $ from 'jquery';
import moment from 'moment';
import numeral from 'numeral';

export class Estadisticas {
    async getTradesChart(coin: string) {
        const {data: {trades: data, buy, sell}}: ApiResponse<{ trades, buy, sell }> = await $.ajax({
            url: 'trades/data',
            data: {coin}
        });

        Estadisticas.loadChart($('#chartdiv'), data, {});
        Estadisticas.loadDatatable($('table'), data);

        $("#lastBuy").text(numeral(buy).format('$#,#.##'));
        $("#lastSell").text(numeral(sell).format('$#,#.##'));
    }

    private static loadDatatable($element, data) {
        $element.DataTable({
            stateSave: false,
            order: [[0, 'desc']],
            ajax: null,
            data: data,
            columnDefs: Defaults.global.dt.getColumns([
                {
                    title: 'Fecha',
                    data: 'date',
                    render(data, type) {
                        if (type == 'display') {
                            return moment(data).format('DD/MMM/YYYY hh:mma');
                        }
                        return data;
                    }
                },
                {
                    title: 'Tipo',
                    data: 'type',
                    render(data, type) {
                        if (type == 'display') {
                            return data == 'buy' ? '<span class="text-success">Compra</span>' : '<span class="text-danger">Venta</span>';
                        }
                        return data;
                    }
                },
                {
                    title: 'Costo',
                    data: 'cost',
                    render(data, type) {
                        if (type == 'display') {
                            return numeral(data).format('$#,#.##');
                        }
                        return data;
                    }
                },
                {
                    title: 'Cantidad',
                    data: 'quantity',
                    render(data, type) {
                        if (type == 'display') {
                            return numeral(data).format('#,#.########');
                        }
                        return data;
                    }
                },
                {
                    title: 'Precio',
                    data: 'price',
                    render(data, type) {
                        if (type == 'display') {
                            return numeral(data).format('$#,#.##');
                        }
                        return data;
                    }
                }
            ])
        });
    }

    private static loadChart($element, data, options) {
        // Create chart instance
        const chart = am4core.create($element.get(0), am4charts.XYChart);

        chart.language.locale = am4lang_es_ES;
        chart.language.locale["_decimalSeparator"] = ".";
        chart.language.locale["_thousandSeparator"] = ",";

        // Add data
        chart.data = data;

        // Create axes
        const dateAxis = chart.xAxes.push(new am4charts.DateAxis());
        const valueAxis1 = chart.yAxes.push(new am4charts.ValueAxis());
        valueAxis1.title.text = "Trades";

        const valueAxis2 = chart.yAxes.push(new am4charts.ValueAxis());
        valueAxis2.title.text = "Buys";
        valueAxis2.syncWithAxis = valueAxis1;

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
        series.dataFields.valueY = "sell";
        series.dataFields.dateX = "date";
        series.tooltipText = "{sell}"
        series.strokeWidth = 2;
        series.minBulletDistance = 15;
        series.smoothing = "monotoneX";
        series.stroke = am4core.color("#f44455");

        // Drop-shaped tooltips
        series.tooltipText = 'Sell: $' + `{sell.formatNumber('#,##0.00')}`;
        series.tooltip.background.cornerRadius = 20;
        series.tooltip.background.strokeOpacity = 0;
        series.tooltip.pointerOrientation = "vertical";
        series.tooltip.label.minWidth = 40;
        series.tooltip.label.minHeight = 40;
        series.tooltip.label.textAlign = "middle";
        series.tooltip.label.textValign = "middle";

        // Create series
        const series2 = chart.series.push(new am4charts.LineSeries());
        series2.dataFields.valueY = "buy";
        series2.dataFields.dateX = "date";
        series2.tooltipText = "{buy}"
        series2.strokeWidth = 2;
        series2.minBulletDistance = 15;
        series2.smoothing = "monotoneX";
        series2.stroke = am4core.color("#6cc788");

        // Drop-shaped tooltips
        series2.tooltipText = 'Buy: $' + `{buy.formatNumber('#,##0.00')}`;
        series2.tooltip.background.cornerRadius = 20;
        series2.tooltip.background.strokeOpacity = 0;
        series2.tooltip.pointerOrientation = "vertical";
        series2.tooltip.label.minWidth = 40;
        series2.tooltip.label.minHeight = 40;
        series2.tooltip.label.textAlign = "middle";
        series2.tooltip.label.textValign = "middle";

        // Make bullets grow on hover
        const bullet = series.bullets.push(new am4charts.CircleBullet());
        bullet.circle.strokeWidth = 2;
        bullet.circle.radius = 4;
        bullet.circle.fill = am4core.color("#fff");

        const bullethover = bullet.states.create("hover");
        bullethover.properties.scale = 1.3;

        // Make bullets grow on hover
        const bullet2 = series2.bullets.push(new am4charts.CircleBullet());
        bullet2.circle.strokeWidth = 2;
        bullet2.circle.radius = 4;
        bullet2.circle.fill = am4core.color("#fff");

        const bullethover2 = bullet2.states.create("hover");
        bullethover2.properties.scale = 1.3;

        // Make a panning cursor
        chart.cursor = new am4charts.XYCursor();
        chart.cursor.behavior = "panXY";
        chart.cursor.xAxis = dateAxis;
        chart.cursor.snapToSeries = [series, series2];

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
