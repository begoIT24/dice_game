<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Game;
use Illuminate\Support\Facades\Artisan;


class DiceApiTest extends TestCase
{
   use RefreshDatabase;
   
   public function test_set_database_config()
   {
      Artisan::call('migrate:reset');
      Artisan::call('migrate');
      Artisan::call('db:seed');

      $response = $this->get('/');
      $response->assertStatus(200);
   }

   /* TESTS FOR USERCONTROLLER */

   public function test_admin_gets_list_of_players()
   {
      /* get admin user */
      $user = User::find(1);
      $this->actingAs($user, 'api');

      /* get last player on the list */
      $player = User::find(11);

      /* actions */
      $response = $this->getJson('/api/dice_game/players');
      // dd($response->json());

      /* check final status */
      $response->assertStatus(200);
      $response->assertJsonStructure([
         '0' => [
            '*' => [
               'id',
               'name',
               'email',
               'successRate'
            ]
         ],
         'message'
      ]);
      // last player is on the list     
      $response->assertJsonFragment(['name' => $player->name]);
   }

   public function test_player_not_get_list_of_players()
   {
      /* get player user */
      $user = User::find(2);
      $this->actingAs($user, 'api');

      /* actions */
      $response = $this->getJson('/api/dice_game/players');

      /* check final status */
      $response->assertStatus(403);
   }

   public function test_admin_gets_ranking()
   {
      /* get admin user */
      $user = User::find(1);
      $this->actingAs($user, 'api');

      /* actions */
      $response = $this->getJson('/api/dice_game/players/ranking');

      /* check final status */
      $response->assertStatus(200);
      $response->assertJsonStructure([
         'averageRanking',
         'message'
      ]);
      $this->assertIsNumeric($response['averageRanking']);
   }

   public function test_player_not_get_ranking()
   {
      /* get player user */
      $user = User::find(2);
      $this->actingAs($user, 'api');

      /* actions */
      $response = $this->getJson('/api/dice_game/players/ranking');

      /* check final status */
      $response->assertStatus(403);
   }

   public function  test_admin_gets_loser()
   {
      /* get admin user */
      $user = User::find(1);
      $this->actingAs($user, 'api');

      /* get player with lowest success rate */
      $loserPlayer = User::role('player')->orderBy('successRate', 'asc')->first();

      /* actions */
      $response = $this->getJson('/api/dice_game/players/ranking/loser');

      //json to array: fixing Cannot access offset of type string on string
      $responseArray = $response->json();    // para testing Laravel: ->json() equivale a json_decode            

      /* check final status */
      $response->assertStatus(200);
      $response->assertJsonStructure([
         'Loser player' => [
            '*' => [
               'id',
               'name',
               'email',
               'successRate'
            ]
         ],
         'message'
      ]);
      // loser player in DB matches response loser player
      $this->assertEquals($loserPlayer->id, $responseArray['Loser player'][0]['id']);
   }

   public function  test_player_not_get_loser()
   {
      /* get player user */
      $user = User::find(2);
      $this->actingAs($user, 'api');

      /* actions */
      $response = $this->getJson('/api/dice_game/players/ranking/loser');

      /* check final status */
      $response->assertStatus(403);
   }

   public function test_admin_gets_winner()
   {
      /* get admin user */
      $user = User::find(1);
      $this->actingAs($user, 'api');

      /* get player with highest success rate */
      $winnerPlayer = User::role('player')->orderBy('successRate', 'desc')->first();

      /* actions */
      $response = $this->getJson('/api/dice_game/players/ranking/winner');
      $responseArray = $response->json();

      /* check final status */
      $response->assertStatus(200);
      $response->assertJsonStructure([
         'Winner player' => [
            '*' => [
               'id',
               'name',
               'email',
               'successRate'
            ]
         ],
         'message'
      ]);
      // winner player in DB matches response winner player
      $this->assertEquals($winnerPlayer->id, $responseArray['Winner player'][0]['id']);
   }

   public function  test_player_not_get_winner()
   {
      /* get player user */
      $user = User::find(2);
      $this->actingAs($user, 'api');

      /* actions */
      $response = $this->getJson('/api/dice_game/players/ranking/winner');

      /* check final status */
      $response->assertStatus(403);
   }

   public function test_player_updates_name()
   {
      /* get player user */
      $user = User::find(2);
      $this->actingAs($user, 'api');

      /* actions */
      $response = $this->putJson("/api/dice_game/players/{$user->id}", [
         'name' => 'New Name',
      ]);
      $updatedUser = User::find($user->id);

      /* check final status */
      $response->assertStatus(200);
      $response->assertJsonStructure([
         'user' => [
            'id',
            'name',
            'email',
            'successRate'
         ],
         'message'
      ]);
      // update name matches name in DB after updating
      $this->assertEquals('New Name', $updatedUser->name);
   }

   public function test_player_not_update_existing_name()
   {
      /* get player user */
      $user = User::find(2);
      $this->actingAs($user, 'api');

      /* get another player name */
      $user2 = User::find(3);
      $existingName = $user2->name;

      /* actions */
      $response = $this->putJson("/api/dice_game/players/{$user->id}", [
         'name' => $existingName,
      ]);

      /* check final status */
      $response->assertStatus(422);
   }

   public function test_player_not_update_another_player_name()
   {
      /* get player user */
      $user = User::find(2);
      $this->actingAs($user, 'api');

      /* get another player */
      $user2 = User::find(3);

      /* actions */
      $response = $this->putJson("/api/dice_game/players/{$user2->id}", [
         'name' => 'New Name',
      ]);

      /* check final status */
      $response->assertStatus(403);
   }

   /* TESTS FOR GAMECONTROLLER */

   public function test_player_plays_game()
   {
      /* get player user */
      $user = User::find(2);
      $this->actingAs($user, 'api');

      /* actions */
      $response = $this->postJson("/api/dice_game/players/{$user->id}/games");
      $updatedUser = User::find($user->id);

      /* check final status */
      $response->assertStatus(200);
      $response->assertJsonStructure([
         'game' => [
            'Game Number',
            'Dice 1',
            'Dice 2',
            'Result'
         ],
         'message'
      ]);
      // the new game played has been counted (stored correctly)
      $this->assertEquals(($user->playedGames) + 1, $updatedUser->playedGames);
   }

   public function test_player_not_play_another_player_game()
   {
      /* get player user */
      $user = User::find(2);
      $this->actingAs($user, 'api');

      /* get another player */
      $user2 = User::find(3);

      /* actions */
      $response = $this->postJson("/api/dice_game/players/{$user2->id}");

      /* check final status */
      $response->assertStatus(405);
   }

   public function test_player_with_games_deletes_all_own_games()
   {
      /* get player user */
      $user = User::find(10);
      $this->actingAs($user, 'api');

      /* actions */
      $response = $this->deleteJson("/api/dice_game/players/{$user->id}/games");
      $deletedGames = Game::where('user_id', $user->id)->count();

      /* check final status */
      $response->assertStatus(200);
      $this->assertEquals(0, $deletedGames);
      $response->assertExactJson([
         'message' => 'All games deleted'
     ]);
   }

   public function test_player_not_delete_another_player_games()
   {
      /* get player user */
      $user = User::find(2);
      $this->actingAs($user, 'api');

      /* get another player */
      $user2 = User::find(3);

      /* actions */
      $response = $this->deleteJson("/api/dice_game/players/{$user2->id}/games");

      /* check final status */
      $response->assertStatus(403);
   }

   public function test_player_not_delete_an_empty_games_list()
   {
      /* get player user with an empty list of games */
      $user = User::find(11);
      $this->actingAs($user, 'api');

       /* actions */
       $response = $this->deleteJson("/api/dice_game/players/{$user->id}/games");

       /* check final status */
      $response->assertStatus(400);
      $response->assertExactJson([
         'error' => 'Failed to delete games'
     ]);     
   }

   public function test_player_gets_own_list_of_games()
   {
      /* get player user */
      $user = User::find(2);
      $this->actingAs($user, 'api');

      /* actions */
      $response = $this->getJson("/api/dice_game/players/{$user->id}/games");

      /* check final status */
      $response->assertStatus(200);
      $response->assertJsonStructure([
         'Your success rate',
         'Your games' => [
            '*' => [
               'Game Number',
               'Dice 1',
               'Dice 2',
               'Result'
            ]
         ],
         'message'
      ]);
      $response->assertJsonFragment(['Your success rate' => $user->successRate]);
   }

   public function test_player_not_get_another_player_list_of_games()
   {
      /* get player user */
      $user = User::find(2);
      $this->actingAs($user, 'api');

      /* get another player */
      $user2 = User::find(3);


      /* actions */
      $response = $this->getJson("/api/dice_game/players/{$user2->id}/games");

      /* check final status */
      $response->assertStatus(403);
   }

   
}
