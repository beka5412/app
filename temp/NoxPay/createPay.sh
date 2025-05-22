source config.sh

echo "Creating payment"
payment="
{
    \"method\": \"PIX\",
    \"code\": \"123333\",      
    \"amount\": 1005.00
}
"
echo ${payment} | curl \
   --header 'Content-Type: application/json' \
   --header 'api-key: ${APIKEY}' \
  ${URL}/payment -d @-
echo