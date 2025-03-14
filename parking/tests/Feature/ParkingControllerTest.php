<?php 
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Parkings;
use App\Models\Places;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ParkingControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_parking_success()
    {
        Parkings::create([
            'titre' => 'Parking 1',
            'adress' => 'Rue Test',
            'nombre_total_places' => 10,
            'places_disponibles' => 10
        ]);

        $response = $this->json('GET', '/api/parking/search', [
            'adress' => 'Rue Test'
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Liste des parkings trouvés',
                 ]);
    }

    public function test_search_parking_no_results()
    {
        $response = $this->json('GET', '/api/parking/search', [
            'adress' => 'Nonexistent Street'
        ]);

        $response->assertStatus(404)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Aucun parking trouvé pour cette recherche',
                 ]);
    }

   
    public function test_create_parking_success()
    {
        $data = [
            'titre' => 'Parking Test',
            'adress' => 'Test Street',
            'nombre_total_places' => 5,
            'places_disponibles'=>5,
        ];

        $response = $this->postJson('/api/parking/store', $data);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Parking ajouté avec succès',
                 ]);

        $this->assertDatabaseHas('parkings', [
            'titre' => 'Parking Test',
            'adress' => 'Test Street'
        ]);
    }

    
    public function test_create_parking_failure()
    {
        $data = [
            'titre' => '',
            'adress' => 'Test Street',
            'nombre_total_places' => 5,
            'places_disponibles'=>5,
        ];

        $response = $this->postJson('/api/parking/store', $data);

        $response->assertStatus(422); 
    }

    public function test_update_parking_success()
    {
        $parking = Parkings::create([
            'titre' => 'Old Parking',
            'adress' => 'Old Street',
            'nombre_total_places' => 5,
            'places_disponibles' => 5
        ]);

        $data = [
            'titre' => 'Updated Parking',
            'adress' => 'Updated Street',
            'nombre_total_places' => 10,
            'places_disponibles'=>5,
        ];

        $response = $this->putJson("/api/parking/modifier/{$parking->id}", $data);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Parking mis à jour avec succès',
                 ]);

        $this->assertDatabaseHas('parkings', [
            'titre' => 'Updated Parking',
            'adress' => 'Updated Street',
        ]);
    }

    public function test_update_parking_not_found()
    {
        $data = [
            'titre' => 'Updated Parking',
            'adress' => 'Updated Street',
            'nombre_total_places' => 10,
            'places_disponibles'=>5,
        ];

        $response = $this->putJson('/api/parking/modifier/999', $data); 

        $response->assertStatus(404);
    }

    public function test_delete_parking_success()
    {
        $parking = Parkings::create([
            'titre' => 'Parking to delete',
            'adress' => 'Delete Street',
            'nombre_total_places' => 10,
            'places_disponibles' => 10
        ]);

        $response = $this->json('DELETE', '/api/parking/supprimer', ['id' => $parking->id]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Parking supprimé avec succès',
                 ]);

        $this->assertDatabaseMissing('parkings', [
            'id' => $parking->id,
        ]);
    }

    public function test_delete_parking_failure()
    {
        $response = $this->json('DELETE', '/api/parking/supprimer', ['id' => 999]); 

        $response->assertStatus(404);
    }

    public function test_initialize_places_success()
    {
        $parking = Parkings::create([
            'titre' => 'Parking for places',
            'adress' => 'Place Street',
            'nombre_total_places' => 10,
            'places_disponibles' => 10
        ]);

        $response = $this->json('POST', "/api/initialiserplaces/{$parking->id}");


        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                     'message' => '10 places ont été créées pour ce parking',
                 ]);
    }

    public function test_initialize_places_already_exists()
    {
        $parking = Parkings::create([
            'titre' => 'Parking with places',
            'adress' => 'Another Street',
            'nombre_total_places' => 10,
            'places_disponibles' => 10
        ]);

        Places::insert([
            ['numero' => 1, 'parking_id' => $parking->id, 'est_disponible' => true],
            ['numero' => 2, 'parking_id' => $parking->id, 'est_disponible' => true],
        ]);
        $response = $this->json('POST', "/api/initialiserplaces/{$parking->id}");


        $response->assertStatus(400)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Ce parking a déjà des places initialisées',
                 ]);
    }
}
