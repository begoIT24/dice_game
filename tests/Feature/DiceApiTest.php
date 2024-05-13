<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
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
      $player= User::find(11);
               
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
      $response->assertJsonFragment(['name' => $player-> name]);  
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
     $responseArray = json_decode($response->getContent(), true);  //json to array: fixing Cannot access offset of type string on string      
 
      /* check final status */
     $response->assertStatus(200);
     $response->assertJsonStructure([
        'Loser player',
        'message'
     ]);     
     // asserting if loser player in the table is response loser player
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
      $responseArray = json_decode($response->getContent(), true);  //json to array: fixing Cannot access offset of type string on string      
  
       /* check final status */
      $response->assertStatus(200);
      $response->assertJsonStructure([
         'Winner player',
         'message'
      ]);     
      // asserting if winner player in the table is response winner player
      $this->assertEquals($winnerPlayer->id, $responseArray['Winner player'][0]['id']);  // $response['name'] no funciona: es un array dentro de array
    } 
 
    public function test_player_updates_name()
    {
      
    }

    /* TESTS FOR GAMECONTROLLER */


 }  
 
 