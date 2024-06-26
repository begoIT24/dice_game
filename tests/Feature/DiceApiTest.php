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
      Artisan::call('passport:install');
      // create a passport client
      Artisan::call('passport:client', ['--personal' => true, '--name' => 'TestClient']);

      $response = $this->get('/');
      $response->assertStatus(200);
   }

   /**
    ** TESTS FOR USERCONTROLLER
    **/

   public function test_admin_gets_list_of_players()
   {
      /* get admin user */
      $user = User::find(1);
      $this->actingAs($user, 'api');

      /* get last player on the list */
      $player = User::find(11);

      /* actions */
      $response = $this->getJson('/api/players');
      // dd($response->json());

      /* check final status */
      $response->assertStatus(200);
      $response->assertJsonStructure([
         '0' => [
            '*' => [
               'id',
               'name',
               'email',
               'success_rate'
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
      $response = $this->getJson('/api/players');

      /* check final status */
      $response->assertStatus(403);
   }

   public function test_admin_gets_ranking()
   {
      /* get admin user */
      $user = User::find(1);
      $this->actingAs($user, 'api');

      /* actions */
      $response = $this->getJson('/api/players/ranking');

      /* check final status */
      $response->assertStatus(200);
      $response->assertJsonStructure([
         'average_ranking',
         'message'
      ]);
      $this->assertIsNumeric($response['average_ranking']);
   }

   public function test_player_not_get_ranking()
   {
      /* get player user */
      $user = User::find(2);
      $this->actingAs($user, 'api');

      /* actions */
      $response = $this->getJson('/api/players/ranking');

      /* check final status */
      $response->assertStatus(403);
   }

   public function  test_admin_gets_loser()
   {
      /* get admin user */
      $user = User::find(1);
      $this->actingAs($user, 'api');

      /* get player with lowest success rate */
      $loserPlayer = User::role('player')->orderBy('success_rate', 'asc')->first();

      /* actions */
      $response = $this->getJson('/api/players/ranking/loser');

      //json to array: fixing Cannot access offset of type string on string
      $responseArray = $response->json();    // para testing Laravel: ->json() equivale a json_decode            

      /* check final status */
      $response->assertStatus(200);
      $response->assertJsonStructure([
         'loser_player' => [
            '*' => [
               'id',
               'name',
               'email',
               'success_rate'
            ]
         ],
         'message'
      ]);
      // loser player in DB matches response loser player
      $this->assertEquals($loserPlayer->id, $responseArray['loser_player'][0]['id']);
   }

   public function  test_player_not_get_loser()
   {
      /* get player user */
      $user = User::find(2);
      $this->actingAs($user, 'api');

      /* actions */
      $response = $this->getJson('/api/players/ranking/loser');

      /* check final status */
      $response->assertStatus(403);
   }

   public function test_admin_gets_winner()
   {
      /* get admin user */
      $user = User::find(1);
      $this->actingAs($user, 'api');

      /* get player with highest success rate */
      $winnerPlayer = User::role('player')->orderBy('success_rate', 'desc')->first();

      /* actions */
      $response = $this->getJson('/api/players/ranking/winner');
      $responseArray = $response->json();

      /* check final status */
      $response->assertStatus(200);
      $response->assertJsonStructure([
         'winner_player' => [
            '*' => [
               'id',
               'name',
               'email',
               'success_rate'
            ]
         ],
         'message'
      ]);
      // winner player in DB matches response winner player
      $this->assertEquals($winnerPlayer->id, $responseArray['winner_player'][0]['id']);
   }

   public function  test_player_not_get_winner()
   {
      /* get player user */
      $user = User::find(2);
      $this->actingAs($user, 'api');

      /* actions */
      $response = $this->getJson('/api/players/ranking/winner');

      /* check final status */
      $response->assertStatus(403);
   }

   public function test_player_updates_name()
   {
      /* get player user */
      $user = User::find(2);
      $this->actingAs($user, 'api');

      /* actions */
      $response = $this->putJson("/api/players/{$user->id}", [
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
            'success_rate'
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
      $response = $this->putJson("/api/players/{$user->id}", [
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
      $response = $this->putJson("/api/players/{$user2->id}", [
         'name' => 'New Name',
      ]);

      /* check final status */
      $response->assertStatus(403);
   }

   /**
    ** TESTS FOR GAMECONTROLLER
    **/


   public function test_player_plays_game()
   {
      /* get player user */
      $user = User::find(2);
      $this->actingAs($user, 'api');

      /* actions */
      $response = $this->postJson("/api/players/{$user->id}/games");
      $updatedUser = User::find($user->id);

      /* check final status */
      $response->assertStatus(200);
      $response->assertJsonStructure([
         'game' => [
            'game_number',
            'dice1',
            'dice2',
            'result'
         ],
         'message'
      ]);
      // the new game played has been counted (stored correctly)
      $this->assertEquals(($user->played_games) + 1, $updatedUser->played_games);
   }

   public function test_player_not_play_another_player_game()
   {
      /* get player user */
      $user = User::find(2);
      $this->actingAs($user, 'api');

      /* get another player */
      $user2 = User::find(3);

      /* actions */
      $response = $this->postJson("/api/players/{$user2->id}");

      /* check final status */
      $response->assertStatus(405);
   }

   public function test_player_with_games_deletes_all_own_games()
   {
      /* get player user */
      $user = User::find(10);
      $this->actingAs($user, 'api');

      /* actions */
      $response = $this->deleteJson("/api/players/{$user->id}/games");
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
      $response = $this->deleteJson("/api/players/{$user2->id}/games");

      /* check final status */
      $response->assertStatus(403);
   }

   public function test_player_not_delete_an_empty_games_list()
   {
      /* get player user with an empty list of games */
      $user = User::find(11);
      $this->actingAs($user, 'api');

      /* actions */
      $response = $this->deleteJson("/api/players/{$user->id}/games");

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
      $response = $this->getJson("/api/players/{$user->id}/games");

      /* check final status */
      $response->assertStatus(200);
      $response->assertJsonStructure([
         'your_success_rate',
         'your_games' => [
            '*' => [
               'game_number',
               'dice1',
               'dice2',
               'result'
            ]
         ],
         'message'
      ]);
      $response->assertJsonFragment(['your_success_rate' => $user->success_rate]);
   }

   public function test_player_not_get_another_player_list_of_games()
   {
      /* get player user */
      $user = User::find(2);
      $this->actingAs($user, 'api');

      /* get another player */
      $user2 = User::find(3);


      /* actions */
      $response = $this->getJson("/api/players/{$user2->id}/games");

      /* check final status */
      $response->assertStatus(403);
   }

   /**
    ** TESTS FOR AUTHCONTROLLER
    **/

   public function test_new_user_can_signup()
   {
      $userData = [
         'name' => 'New User',
         'email' => 'newuser@example.com',
         'password' => '1234',
         'password_confirmation' => '1234',
      ];

      $response = $this->postJson('/api/players', $userData);

      $response->assertStatus(201)
         ->assertJson([
            'message' => 'Successfully created user!'
         ]);
   }

   public function test_an_existing_user_can_not_signup()
   {
      $user = User::find(3);

      $userData = [
         'name' => 'New User',
         'email' => $user->email,
         'password' => '1234',
         'password_confirmation' => '1234',
      ];

      $response = $this->postJson('/api/players', $userData);

      $response->assertStatus(422)
         ->assertJson([
            'message' => 'The email has already been taken.'
         ]);

      /* Same tests for existing name and for existing name and email.
         The json returns the adequate answer in each case*/
   }
   public function test_an_existing_user_can_login()
   {
      /* get existing user */
      $user = User::find(2);

      $loginData = [
         'email' => $user->email,
         'password' => '1234',
      ];
      $response = $this->postJson('/api/login', $loginData);

      $response->assertStatus(200)
         ->assertJsonStructure([
            'access_token',
            'token_type'
         ]);
   }
   public function test_a_not_registered_user_can_not_login()
   {
      $loginData = [
         'email' => 'mail@mail.com',
         'password' => '1234',
      ];
      $response = $this->postJson('/api/login', $loginData);

      $response->assertStatus(401)
         ->assertJson([
            'message' => 'Unauthorized'
         ]);
   }

   public function test_user_can_not_login_with_incorrect_password()
   {
      /* get existing user */
      $user = User::find(2);

      $loginData = [
         'email' => $user->email,
         'password' => 'hola',
      ];
      $response = $this->postJson('/api/login', $loginData);

      $response->assertStatus(401)
         ->assertJson([
            'message' => 'Unauthorized'
         ]);
   }

}
