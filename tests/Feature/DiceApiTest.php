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
         
       /* actions */
       $response = $this->getJson('/api/dice_game/players');
 
       /* check final status */
      $response->assertStatus(200);

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
     } 
 
    public function  test_admin_gets_loser()
    {
       /* get admin user */
       $user = User::find(1);       
 
       $this->actingAs($user, 'api');          
         
       /* actions */
       $response = $this->getJson('/api/dice_game/players/ranking/loser');
  
       /* check final status */
       $response->assertStatus(200);
    } 
 
    public function test_admin_gets_winner()
    {
       /* get admin user */
       $user = User::find(1);       
 
       $this->actingAs($user, 'api');          
         
       /* actions */     
       $response = $this->getJson('/api/dice_game/players/ranking/winner');      
  
       /* check final status */
       $response->assertStatus(200);
    } 
 
    public function test_player_updates_name()
    {
      
    }

    /* TESTS FOR GAMESCONTROLLER */


 }  
 
 