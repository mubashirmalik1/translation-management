<?php

namespace Tests\Feature;

use App\Models\Locale;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class LocaleControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_list_locales()
    {
        // Create two locales.
        Locale::factory()->create(['code' => 'en', 'name' => 'English']);
        Locale::factory()->create(['code' => 'fr', 'name' => 'French']);

        $response = $this->getJson('/api/locales');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'meta' => ['message', 'status', 'errors']
            ]);

        // Instead of asserting an exact count, verify that our two locales exist.
        $locales = $response->json('data.data');
        $this->assertTrue(
            collect($locales)->contains(fn($locale) =>
                $locale['code'] === 'en' && $locale['name'] === 'English'
            ),
            'English locale not found.'
        );

        $this->assertTrue(
            collect($locales)->contains(fn($locale) =>
                $locale['code'] === 'fr' && $locale['name'] === 'French'
            ),
            'French locale not found.'
        );
    }

    #[Test]
    public function it_can_store_a_new_locale()
    {
        $data = [
            'code' => 'en',
            'name' => 'English'
        ];

        $response = $this->postJson('/api/locales', $data);

        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'code' => 'en',
                    'name' => 'English'
                ],
                'meta' => [
                    'message' => 'Language created successfully.',
                    'status' => 201,
                    'errors' => []
                ]
            ]);

        $this->assertDatabaseHas('locales', $data);
    }

    #[Test]
    public function it_can_show_a_locale()
    {
        $locale = Locale::factory()->create(['code' => 'en', 'name' => 'English']);

        $response = $this->getJson("/api/locales/{$locale->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'code' => 'en',
                    'name' => 'English'
                ],
                'meta' => [
                    'message' => 'Language retrieved successfully.',
                    'status' => 200,
                    'errors' => []
                ]
            ]);
    }

    #[Test]
    public function it_can_update_a_locale()
    {
        $locale = Locale::factory()->create(['code' => 'en', 'name' => 'English']);

        $data = [
            'code' => 'fr',
            'name' => 'French'
        ];

        $response = $this->putJson("/api/locales/{$locale->id}", $data);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'code' => 'fr',
                    'name' => 'French'
                ],
                'meta' => [
                    'message' => 'Language updated successfully.',
                    'status' => 200,
                    'errors' => []
                ]
            ]);

        $this->assertDatabaseHas('locales', [
            'id' => $locale->id,
            'code' => 'fr',
            'name' => 'French'
        ]);
    }

    #[Test]
    public function it_can_delete_a_locale()
    {
        $locale = Locale::factory()->create(['code' => 'en', 'name' => 'English']);

        $response = $this->deleteJson("/api/locales/{$locale->id}");

        // Expect a 204 No Content response.
        $response->assertStatus(204);

        $this->assertDatabaseMissing('locales', ['id' => $locale->id]);
    }
}
