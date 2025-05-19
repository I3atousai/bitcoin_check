// import { createChart } from 'lightweight-charts';

// const firstChart = createChart(document.getElementById('firstContainer'));
// const secondChart = createChart(document.getElementById('secondContainer'));

// import { AreaSeries, BarSeries, BaselineSeries, createChart } from 'lightweight-charts';

const chartOptions = { layout: { textColor: 'black', background: { type: 'solid', color: 'white' }, width:400, height:400 } };
const chart = LightweightCharts.createChart(document.getElementById('chart'), chartOptions);

const candlestickSeries = chart.addSeries(LightweightCharts.CandlestickSeries, {
    upColor: '#26a69a', downColor: '#ef5350', borderVisible: false,
    wickUpColor: '#26a69a', wickDownColor: '#ef5350',
}, chart.timeScale().applyOptions({barSpacing: 1000}));

$.ajax({
        url: `getCandles.php`,
        method: "GET",
        dataType: "json",
        success: function (data) {
            
    data.candles.forEach(el => {
        el.time = Date.parse(el.time)/1000;
    });
    console.log(data.candles);
            candlestickSeries.setData(data.candles);
            
        },
        error: function (err) {
            console.log(err)
        }
    })

chart.timeScale().fitContent();