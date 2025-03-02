<script>
    (function($) {





        const PeopleByNumbers = [
            {
                humans: 7684227416,
                date: '31 Dec 2007 00:00:00 GMT',
                dif: 0
            },
            {
                humans: 7831814138,
                date: '31 Dec 2009 00:00:00 GMT',
                dif: 0
            },
            {
                humans: 8102716701,
                date: '31 Dec 2010 00:00:00 GMT',
                dif: 0
            },
            {
                humans: 8199430908,
                date: '31 Dec 2011 00:00:00 GMT',
                dif: 0
            },
            {
                humans: 8301283002,
                date: '31 Dec 2012 00:00:00 GMT',
                dif: 0
            },
            {
                humans: 8424738019,
                date: '31 Dec 2013 00:00:00 GMT',
                dif: 0
            },
            {
                humans: 8532048007,
                date: '31 Dec 2014 00:00:00 GMT',
                dif: 0
            },
            {
                humans: 8634006014,
                date: '31 Dec 2015 00:00:00 GMT',
                dif: 0
            },
            {
                humans: 8739001024,
                date: '31 Dec 2016 00:00:00 GMT',
                dif: 0
            },
            {
                humans: 8844128002,
                date: '31 Dec 2017 00:00:00 GMT',
                dif: 0
            },
            {
                humans: 8953851418,
                date: '31 Dec 2018 00:00:00 GMT',
                dif: 0
            },
            {
                humans: 9060794141,
                date: '31 Dec 2019 00:00:00 GMT',
                dif: 0
            },
            {
                humans: 9154049012,
                date: '31 Dec 2020 00:00:00 GMT',
                dif: 0
            },
            {
                humans: 9248028002,
                date: '31 Dec 2021 00:00:00 GMT',
                dif: 0
            },
            {
                humans: 9339174098,
                date: '31 Dec 2022 00:00:00 GMT',
                dif: 0
            },
            {
                humans: 9446218012,
                date: '31 Dec 2023 00:00:00 GMT',
                dif: 0
            },
            {
                humans: 9539918089,
                date: '31 Dec 2023 00:00:00 GMT',
                dif: 0
            }
        ];



        attachCounter = function(countHumans, increment, time) {

            //console.log(Math.round(countHumans + increment * time))

            // <div class="container-overpop">$1 <span class="overpop"></span></div>
            let fmtCount = ' (' + Math.round(countHumans + increment * time).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") + ') ';
            $(".overpop").text(fmtCount)

            let timer = setTimeout(function() {
                attachCounter(countHumans, increment, time + 1);
            }, 1000);

        }


        initCounter = function() {

            let humanCount = 0;
            let years = 1; // since 2005 
            let amountOfYears = 0;
            //  count/year *year/day * day/hour * hour/min * min/sec

            for (i = 1; i < PeopleByNumbers.length; i++) {

                PeopleByNumbers[i].dif = PeopleByNumbers[i].humans - PeopleByNumbers[i - 1].humans;
                humanCount += PeopleByNumbers[i].dif;
                amountOfYears++;
            }


            let avgGrowth = Math.ceil(humanCount / (amountOfYears+1));

           // let n = PeopleByNumbers.length;
            
            let perSecondGrowth = (avgGrowth / 365 / 24 / 60 / 60)

            let now = new Date();
            let start = new Date(now.getFullYear(), 0, 0);
            let diff = now - start;
            let oneDay = 1000;
            let secondsday = Math.floor(diff / oneDay);
            // console.log('Seconds gone by this year include today: ' + secondsday);

            // console.log(avgGrowth); // average growth per year since 2005 

            // console.log(perSecondGrowth);
            // document.body.innerText = document.body.innerText.replace('Overpopulation', 'Overpopulation');

            attachCounter(PeopleByNumbers[PeopleByNumbers.length - 1].humans + Math.round(secondsday * perSecondGrowth), perSecondGrowth, 1);

            var arrayStr = ["Overpopulation", "Überbevölkerung", "sobrepoblacion"];

            arrayStr.forEach(function(item) {
                    $("*:containsIN('"+item+"')").filter(
                        function() {
                            return $(this).find("*:contains('" + item + "')").length == 0
                        }
                    ).html(function(_, html) {
                        if (html != 'undefined') {
                            var replace = item;
                            var re = new RegExp(replace,"gi");
                            return html.replace(re, '<span class="container-overpop">'+item+'<span class="overpop"></span></span>');
                        }

                    });
                }
            )


            attachCounter(PeopleByNumbers[PeopleByNumbers.length - 1].humans + Math.round(secondsday * perSecondGrowth), perSecondGrowth, 1);

            // $("*:contains('Overpopulation')").replace('Overpopulation', '<span class="marked">Overpopulation</span>');

            var changeTooltipPosition = function(event) {
                var tooltipX = event.pageX - 8;
                var tooltipY = event.pageY + 8;
                $('div.tooltip2').css({
                    top: tooltipY,
                    left: tooltipX,
                    zIndex: 1000
                });
            };

            var showTooltip = function(event) {
                $('div.tooltip2').remove();
                $(`<div class="tooltip2">Since 2007, ${perSecondGrowth.toFixed(3)} humans/second, are added to the earth's population.</div>`)
                    .appendTo('body');
                changeTooltipPosition(event);
            };

            var hideTooltip = function() {
                $('div.tooltip2').remove();
            };

            $("span.overpop").bind({
                mousemove: changeTooltipPosition,
                mouseenter: showTooltip,
                mouseleave: hideTooltip
            });

  
        }

        $.extend($.expr[":"], {
            "containsIN": function(elem, i, match, array) {
                return (elem.textContent || elem.innerText || "").toLowerCase().indexOf((match[3] || "").toLowerCase()) >= 0;
            }
        });






 

        initCounter();


    })(jQuery);
</script>