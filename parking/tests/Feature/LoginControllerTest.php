<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class LoginControllerTest extends TestCase
{
    use RefreshDatabase;
    protected function setUp(): void
    {
        parent::setUp();
    
        // Insérer les rôles dans la base de données de test
        DB::table('roles')->insert([
            ['id' => 1, 'nomrole' => 'admin', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'nomrole' => 'utilisateur', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
    
    public function test_cree_users()
    {
        $data = [
            'name' => 'nihad amhine',
            'email' => 'ni@gmail.com',
            'password' => 'nihadni',
            'password_confirmation' => 'nihadni',
            'idrole' => 2, // Ajout d'idrole pour éviter l'erreur SQL
        ];

        $response = $this->postJson('/api/auth/register', $data);


        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'status',
                     'message',
                     'token'
                 ]);

        $this->assertDatabaseHas('users', ['email' => 'ni@gmail.com']);
    }

    public function test_create_user_validation_error()
    {
        $data = [
            'name' => '',
            'email' => 'invalid-email',
            'password' => '123',
            'idrole' => 'not-a-number'
        ];

        $response = $this->postJson('/api/auth/register', $data);

        $response->assertStatus(422) 
                 ->assertJsonValidationErrors(['name', 'email', 'password', 'idrole']);
    }

    public function test_login_users()
    {
        $user = User::factory()->create([
            'email' => 'ni@gmail.com',
            'password' => bcrypt('nihadni'),
            'idrole' => 2, // Ajout d'idrole pour éviter l'erreur SQL
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'ni@gmail.com',
            'password' => 'nihadni'
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'status',
                     'message',
                     'token'
                 ]);
    }

    public function test_login_user_invalid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'ni@gmail.com',
            'password' => bcrypt('nihadni'),
            'idrole' => 2, // Ajout d'idrole pour éviter l'erreur SQL
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'wrong-email@gmail.com', // Faux email pour tester l'échec
            'password' => 'wrongpassword'
        ]);

        $response->assertStatus(401)
                 ->assertJson([
                     'status' => false,
                     'message' => 'Email & Password does not match with our record.'
                 ]);
    }
}
