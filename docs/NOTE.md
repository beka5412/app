curl --request POST \
     --url https://onesignal.com/api/v1/notifications \
     --header 'Authorization: Basic M2M2ZmEwZmIt....' \
     --header 'accept: application/json' \
     --header 'content-type: application/json' \
     --data '
     {
          "app_id": "86c9fd40-f373-4e5c-b690-c0e43c45b968",
          "contents": {
               "pt": "Sua comissão: R$285,00",
               "en": "Your earn: R$ 285,00"
          },
          "name": "INTERNAL_CAMPAIGN_NAME",
          "include_external_user_ids": ["quielbala@gmail.com"],
          "channel_for_external_user_ids": "push",
          "data": {"foo": "bar"},
          "headings": {
               "pt": "Compra aprovada",
               "en": "Purchase approved!"
          },
          "small_icon": {
               "pt": "https://img.onesignal.com/tmp/a72b0207-7b2a-4b26-adc7-410ee9f88072/i66eLXZSSOKCEsRZTsMg_icon_mobile.png",
               "en": "https://img.onesignal.com/tmp/a72b0207-7b2a-4b26-adc7-410ee9f88072/i66eLXZSSOKCEsRZTsMg_icon_mobile.png"
          }
     }
'


### LEMBRETE

verificar em todos os lugares onde se usa Entity::find() ou Entity::findOrFail() 
se esta considerando o id do usuario logado, para evitar que um usuario veja
conteudos de outro usuario por nao distinguir o que eh de quem

OneSignal
```
curl --request POST \
     --url https://onesignal.com/api/v1/notifications \
     --header 'Authorization: Basic YzQ3NmFmNGEtN2IyMi....' \
     --header 'accept: application/json' \
     --header 'content-type: application/json' \
     --data '
     {
          "include_player_ids": ["edfb1734-28c6-4e6c-b150-22cfe3007252"],
          "app_id": "306d362f-2e9b-4a0c-9cea-9fdb1316a15d",
          "contents": {
               "pt": "Mensagem em português",
               "en": "English or Any Language Message",
               "es": "Spanish Message"
          },
          "name": "INTERNAL_CAMPAIGN_NAME"
     }
'



curl --request POST \
     --url https://onesignal.com/api/v1/notifications \
     --header 'Authorization: Basic YzQ3NmFmNGEtN2I....' \
     --header 'accept: application/json' \
     --header 'content-type: application/json' \
     --data '
     {
          "included_segments": [
               "Segmento X"
          ],
          "app_id": "306d362f-2e9b-4a0c-9cea-9fdb1316a15d",
          "contents": {
               "pt": "Mensagem em português",
               "en": "English or Any Language Message",
               "es": "Spanish Message"
          },
          "name": "INTERNAL_CAMPAIGN_NAME"
     }
'
```









curl --request POST \
     --url https://onesignal.com/api/v1/notifications \
     --header 'Authorization: Basic M2M2ZmEwZmItYTlmMC00ZG....' \
     --header 'accept: application/json' \
     --header 'content-type: application/json' \
     --data '
     {
          "app_id": "86c9fd40-f373-4e5c-b690-c0e43c45b968",
          "contents": {
               "pt": "Mensagem em português",
               "en": "English or Any Language Message",
               "es": "Spanish Message"
          },
          "name": "INTERNAL_CAMPAIGN_NAME",
          "filters": [
               {"field": "tag", "key": "email", "relation": "=", "value": "contato@rocketleads.com.br"}
          ]
     }
'





curl --request POST \
     --url https://onesignal.com/api/v1/notifications \
     --header 'Authorization: Basic M2M2ZmEwZmItYTlmMC00Z...' \
     --header 'accept: application/json' \
     --header 'content-type: application/json' \
     --data '
     {
          "app_id": "86c9fd40-f373-4e5c-b690-c0e43c45b968",
          "contents": {
               "pt": "Mensagem em português",
               "en": "English or Any Language Message",
               "es": "Spanish Message"
          },
          "name": "INTERNAL_CAMPAIGN_NAME",
          "filters": [
               {"field": "tag", "key": "email", "relation": "=", "value": "contato@rocketleads.com.br"}
          ]
     }
'





curl --request POST \
     --url https://onesignal.com/api/v1/notifications \
     --header 'Authorization: Basic M2M2ZmEwZmItYTlmMC00Z....' \
     --header 'accept: application/json' \
     --header 'content-type: application/json' \
     --data '
     {
          "app_id": "86c9fd40-f373-4e5c-b690-c0e43c45b968",
          "contents": {
               "pt": "Mensagem em português",
               "en": "English or Any Language Message"
          },
          "name": "INTERNAL_CAMPAIGN_NAME",
          "external_user_id": "quielbala@gmail.com"
     }
'

You must include which players, segments, or tags you wish to send this notification to.









TODO DIA BAIXAR ESSA LISTA
https://publicsuffix.org/list/public_suffix_list.dat

USAR O ARQUIVO LOCAL




-------------

EXECUTAR ISSO: 
update users set sku = ( SELECT UPPER(LEFT(MD5( (select uuid()) ), 13)) );