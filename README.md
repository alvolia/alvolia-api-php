Alvolia API Official Client
===========================

Recommendation
--------------
```
// initialize client
$client = new Alvolia\API\Client('ADB-XXXXXXXXXX', 'TOKEN_FROM_INTERFACE');

// get recommender client instance
$recommender = $client->recommender();

// get list available recommenders
$recommenders = $recommender->recommenders()->get();

// get recommendations
$result = $recommender->recommend('USER_ID', 'ITEM_IDENTIFIER', 'RECOMMENDER_UUID');

// get top 3 items based on content based similarity
$items = $result->contentBasedItems(3); // associative array of ITEM_ID -> SIMILARITY

// get top 5 items based on collaborative filtering
$items = $result->collaborativeItems(5); // associative array of ITEM_ID -> SIMILARITY
```
