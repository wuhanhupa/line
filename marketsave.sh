#!/bin/bash
step=10 #间隔的秒数，不能大于60
 
for (( i = 0; i &lt; 60; i=(i+step) )); do
    $(php '/www/wwwroot/bnbjy/cli.php Mapi/Bcb/marketTrade')
    sleep $step
done

exit 0