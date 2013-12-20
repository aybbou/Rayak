function showInventorsBar(data, divId) {
    var cats = new Array();
    var d = new Array();
    var l = data.length;
    for (var i = 0; i < l; i++) {
        cats.push(data[i].name);
        d.push(data[i].count);
    }

    $(divId).highcharts({
        chart: {
            type: 'column'
        },
        title: {
            text: 'Top 10 inventeurs'
        },
        xAxis: {
            categories: cats
        },
        yAxis: {
            min: 0,
            allowDecimals:false,
            title: {
                text: 'Nombre de brevets'
            }
        },
        legend: {
          enabled: false  
        },
        tooltip: {
            headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
            pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                    '<td style="padding:0"><b>{point.y}</b></td></tr>',
            footerFormat: '</table>',
            shared: true,
            useHTML: true
        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0
            }
        },
        series: [{
                name: 'Brevets ',
                data: d
            }]
    });
}

function showKeywordsCloud(data) {
    //var data =[['foo',123],['bar',145]];
    WordCloud($('#keywordsCloud')[0], {list: data});
}

function showKeywordsPieChart(data) {
    $('#keywordsPiechart').highcharts({
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false
        },
        title: {
            text: 'Top 10 mots-clés'
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    color: '#000000',
                    connectorColor: '#000000',
                    format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                }
            }
        },
        series: [{
                type: 'pie',
                name: 'Pourcentage ',
                data: data
            }]
    });
}

function createMap(data) {
    jQuery('#vmap').vectorMap({
        values: data,
        map: 'world_en',
        backgroundColor: '#a5bfdd',
        borderColor: '#818181',
        borderOpacity: 0.25,
        borderWidth: 1,
        color: '#f4f3f0',
        enableZoom: true,
        hoverColor: '#c9dfaf',
        hoverOpacity: null,
        normalizeFunction: 'linear',
        scaleColors: ['#b6d6ff', '#005ace'],
        selectedColor: '#c9dfaf',
        selectedRegion: null,
        showTooltip: true,
        onRegionClick: function(element, code, region)
        {
            inventors(element, code, region);
        }
    });
}

function showEvolution(data) {
    $('#charts').highcharts({
        chart: {
            type: 'spline'
        },
        title: {
            text: 'Evolution du nombre de brevets publiés'
        },
        legend: {
          enabled: true  
        },
        xAxis: {
            type: 'datetime',
            dateTimeLabelFormats: {// don't display the dummy year
                month: '%e. %b',
                year: '%b'
            }
        },
        yAxis: {
            title: {
                text: 'Nombre de brevets'
            },
            min: 0
        },
        tooltip: {
            formatter: function() {
                return '<b>' + this.series.name + '</b><br/>' +
                        Highcharts.dateFormat('%e. %b', this.x) + ': ' + this.y + ' brevets';
            }
        },
        series: [{
                name: 'Brevets publiés 15/09/2013 - 15/11/2013',
                data: data
            }]
    });
}

function sortArray ( array ) {
  var arrayLength = array.length;  
    for (var x = 0; x < arrayLength; x++) {
        var max = array[x];
        for (var y = x + 1; y < arrayLength; y++) {
            if (array[y][1] > max[1]) {
                max = array[y];
                array[y] = array[x];
                array[x] = max;
            }
        }
    }

  return array;
}

function renderKeywords(data, parameter) {
    parameter = false;
    var len = data.length;
    var keywordsFinal = new Array();
    var sortedAndDone = new Array();
    var forbidden =new Array('between','therefor','such','via','thereof','use','methods','method');
    var ingWords = new RegExp(/.*ing$/g);
    // Checks if the keyword doesn't belong to the list above and/or is an -ing word, then adds it to the keywordsFinal array
    for (var i = 0; i < len; i++) {
        if ( ( forbidden.indexOf(data[i].keyword) === -1 ) && ( ingWords.test(data[i].keyword) === false) && (data[i].count >= 7) ){
            keywordsFinal.push([data[i].keyword, data[i].count]);
        }
    }
    // Manipulating the keywords array to combine both singular/plural words into one keyword (here we choose the plural)
    console.log(keywordsFinal);
    for (var indice = 0; indice < keywordsFinal.length; indice++) {
        if (keywordsFinal[indice][0].toString().match(/.+s$/i) !== null) {
            var singular = keywordsFinal[indice][0].toString().replace(/s$/, "");
            for (var i = 0; i < keywordsFinal.length; i++) {
                if (keywordsFinal[i][0] === singular) {
                    keywordsFinal[indice][1] += keywordsFinal[i][1];
                    keywordsFinal.splice(i, 1);
                    indice = 0; // because keywords' length changes all the time (cf. previous line)
                    break;
                }
            }
        }
    }
    var doneKeywords = sortArray(keywordsFinal);
    
    return doneKeywords;
}