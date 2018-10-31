Alvolia API Official Client
===========================

Recommendation
--------------
```
// initialize client
$client = new Alvolia\API\Client('ADB-XXXXXXXXXX', 'TOKEN_FROM_INTERFACE');

// get recommender client instance
$recommender = $client->recommender();

// get recommendations
$res = $recommender->recommend('USER_ID', 'ITEM_IDENTIFIER', 'RECOMMENDER_UUID');
```
