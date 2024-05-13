<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Game;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Artisan;


class DiceApiTest extends TestCase
{
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
         ]
      ]);
      // last player is on the list     
      $response->assertJsonFragment(['name' => $player->name]);
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
         'Loser player',
         'message'
      ]);
      // loser player in DB matches response loser player
      $this->assertEquals($loserPlayer->id, $responseArray['Loser player'][0]['id']);  // $response['name'] no funciona: es un array dentro de array
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
         'Winner player',
         'message'
      ]);
      // winner player in DB matches response winner player
      $this->assertEquals($winnerPlayer->id, $responseArray['Winner player'][0]['id']);
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
         ]
      ]);
      // update name matches name in DB after updating
      $this->assertEquals('New Name', $updatedUser->name);
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
         ]
      ]);
      // the new game played has been counted (stored correctly)
      $this->assertEquals(($user->playedGames) + 1, $updatedUser->playedGames);
   }

   public function test_player_delete_all_own_games(){
       /* get player user */
       $user = User::find(2);
       $this->actingAs($user, 'api');
 
       /* actions */
       $response = $this->deleteJson("/api/dice_game/players/{$user->id}/games");
       $deletedGames = Game::where('user_id', $user->id)->count();
 
       /* check final status */
       $response->assertStatus(204);
       $this->assertEquals(0, $deletedGames);
   }

   public function test_player_gets_own_list_of_games(){
      /* get player user */
      $user = User::find(2);
      $this->actingAs($user, 'api');

      /* actions */
      $response = $this->getJson("/api/dice_game/players/{$user->id}/games");

      /* check final status */
      $response->assertStatus(200);
      $response->assertJsonStructure([
         
      ]);     
      $response->assertJsonFragment([]);
   }
}
