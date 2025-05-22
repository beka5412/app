while [ 1 ]; do
curl -X GET https://app.speedsellx.com/api/queue/stripe/webhook
sleep 1
done
