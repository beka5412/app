while [ 1 ]; do
curl -X GET https://app-test.speedsellx.com/api/queue/stripe/webhook
sleep 1
done
