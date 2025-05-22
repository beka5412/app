while [ 1 ]; do
curl -X GET https://app.speedsellx.com/api/cronjob/sendmail
sleep 3
done;
