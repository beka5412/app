while [ 1 ]; do
curl -X GET https://app-test.speedsellx.com/api/cronjob/sendmail
sleep 3
done;
