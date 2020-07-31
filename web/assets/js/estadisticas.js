$(async () => {
    const {data: {trades: data}} = await $.ajax({
        'url': 'trades/data'
    });
    loadChart($('#chartdiv'), data, {});
});
