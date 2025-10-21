<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserApiTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test de création d'un utilisateur via l'API
     */
    public function test_creer_utilisateur(): void
    {
        $userData = [
            'name' => 'Mariam Coulibaly',
            'email' => 'coulibaly@gmail.com',
        ];

        $response = $this->postJson('/api/users', $userData);

        $response->assertStatus(201)
            ->assertJson([
                'status' => 'success',
                'message' => 'User created successfully',
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'id',
                    'name',
                    'email',
                    'created_at',
                    'updated_at',
                ]
            ]);

        // Vérification en base de données
        $this->assertDatabaseHas('users', [
            'name' => 'Mariam Coulibaly',
            'email' => 'coulibaly@gmail.com',
        ]);
    }

    /**
     * Test de récupération de la liste paginée des utilisateurs
     */
    public function test_liste_utilisateurs_avec_pagination(): void
    {
        $response = $this->getJson('/api/users');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'current_page',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'email',
                        'created_at',
                        'updated_at',
                    ]
                ],
                'first_page_url',
                'from',
                'last_page',
                'last_page_url',
                'links',
                'next_page_url',
                'path',
                'per_page',
                'prev_page_url',
                'to',
                'total'
            ]);

        // Vérification du contenu des données
        $responseData = $response->json();
        $this->assertGreaterThan(0, count($responseData['data']));
    }

    /**
     * Test de recherche d'utilisateurs avec filtre
     */
    public function test_liste_utilisateurs_avec_recherche(): void
    {
        $response = $this->getJson('/api/users?search=Mariam');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'email',
                    ]
                ]
            ]);

        // Vérification de la pertinence des résultats
        $responseData = $response->json();
        if (count($responseData['data']) > 0) {
            $this->assertStringContainsString('Mariam', $responseData['data'][0]['name']);
        }
    }

    /**
     * Test de récupération d'un utilisateur inexistant
     */
    public function test_afficher_utilisateur_inexistant(): void
    {
        $response = $this->getJson('/api/users/99');

        $response->assertStatus(404)
            ->assertJson([
                'status' => 'error',
                'message' => 'User not found',
            ]);
    }

    /**
     * Test de mise à jour des informations d'un utilisateur
     */
    public function test_mise_a_jour_utilisateur(): void
    {
        $user = User::first();

        $updateData = [
            'name' => 'Nom Modifié Test',
            'email' => 'modifie-test@example.com',
        ];

        $response = $this->putJson("/api/users/{$user->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'User updated successfully',
                'data' => [
                    'name' => 'Nom Modifié Test',
                    'email' => 'modifie-test@example.com',
                ]
            ]);

        // Vérification de la mise à jour en base
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Nom Modifié Test',
            'email' => 'modifie-test@example.com',
        ]);
    }

    /**
     * Test de suppression d'un utilisateur
     */
    public function test_suppression_utilisateur(): void
    {
        $user = User::first();

        $this->assertNotNull($user, 'Aucun utilisateur trouvé dans la base de données');

        $userId = $user->id;
        $userEmail = $user->email;

        $response = $this->deleteJson("/api/users/{$userId}");

        $response->assertStatus(204);

        // Vérification de la suppression en base
        $this->assertDatabaseMissing('users', [
            'id' => $userId,
            'email' => $userEmail,
        ]);

        // Confirmation de la suppression
        $this->assertNull(User::find($userId), "L'utilisateur avec l'ID {$userId} existe encore après suppression");
    }
}
