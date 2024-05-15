<p align="center"><img src="dices.webp" width="400" alt="Get 7 Dice Game Logo"></a></p>


<title align="center">
Get 7 Dice Game Api
</title>


## About Dice Game Api

Api Rest created with Laravel 10 with Passport authentication and Spatie roles and permissions system. It includes a complete feature testing with PHPUnit.
The game logic is simple: It is a dice game played with two dice. If the sum of the result of the two dices is 7, the game is won, otherwise it is lost.

## Api Endpoints

- POST /players : creates a player.
- PUT /players/{id} : modifies the player's name.
- DELETE /players/{id}/games: deletes the player's rolls.
- GET /players: returns the list of all players in the system with their average success rate.
- GET /players/{id}/games: returns the list of games played by a player.
- GET /players/ranking: returns the average ranking of all the players in the system. That is, the average percentage of successes.
- GET /players/ranking/loser: returns the player with the highest success rate.
- GET /players/ranking/winner: returns the player with the best success rate.

