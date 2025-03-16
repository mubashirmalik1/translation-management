<?php

namespace Tests\Feature;

use App\Models\Locale;
use App\Models\Tag;
use App\Models\Translation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class TranslationControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_list_translations()
    {
        $locale = Locale::factory()->create(['code' => 'en']);
        Translation::factory()->create([
            'locale_id' => $locale->id,
            'translation_key' => 'welcome.message',
            'translation_content' => 'Welcome!'
        ]);

        $response = $this->getJson('/api/translations');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'meta' => ['message', 'status', 'errors']
            ]);

        // Since index() returns a paginated result, the actual records are in data.data.
        $translations = $response->json('data.data');

        $this->assertTrue(
            collect($translations)->contains(fn($translation) =>
                $translation['translation_key'] === 'welcome.message' &&
                $translation['translation_content'] === 'Welcome!'
            ),
            'Translation not found.'
        );

        // Assert meta status equals 200.
        $this->assertEquals(200, $response->json('meta.status'));
    }

    #[Test]
    public function it_can_store_a_translation()
    {
        // Create a locale first.
        $locale = Locale::factory()->create(['code' => 'en']);

        // Controller expects locale code in 'locale_id'
        $data = [
            'locale_id'           => $locale->id,
            'translation_key'     => 'welcome.message1',
            'translation_content' => 'Welcome to our site!',
            'tags'                => ['greeting', 'home']
        ];

        $response = $this->postJson('/api/translations', $data);

        $response->assertStatus(201)
            ->assertJson([
                'data' => [

                    'locale_id'           => $locale->id,
                    'translation_key' => 'welcome.message1',
                    'translation_content'         => 'Welcome to our site!'
                ],
                'meta' => [
                    'message' => 'Translation Create successfully.',
                    'status' => 201,
                    'errors' => []
                ]
            ]);

        $this->assertDatabaseHas('translations', [
            'translation_key' => 'welcome.message1',
            'translation_content'         => 'Welcome to our site!'
        ]);

        // Assert that tags were created.
        $this->assertDatabaseHas('tags', ['name' => 'greeting']);
        $this->assertDatabaseHas('tags', ['name' => 'home']);
    }

    #[Test]
    public function it_can_show_a_translation()
    {
        $locale = Locale::factory()->create(['code' => 'en']);
        $translation = Translation::factory()->create([
            'locale_id' => $locale->id,
            'translation_key' => 'hello.world',
            'translation_content' => 'Hello World'
        ]);

        $response = $this->getJson("/api/translations/{$translation->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'translation_key' => 'hello.world',
                    'translation_content' => 'Hello World'
                ],
                'meta' => [
                    'message' => 'Translation Retrieve successfully.',
                    'status' => 200,
                    'errors' => []
                ]
            ]);
    }

    #[Test]
    public function it_can_update_a_translation()
    {
        $locale = Locale::factory()->create(['code' => 'en']);
        $translation = Translation::factory()->create([
            'locale_id' => $locale->id,
            'translation_key' => 'hello.world',
            'translation_content' => 'Hello World'
        ]);

        $data = [
            'locale_id'           => $locale->id,
            'translation_key'     => 'hello.updated',
            'translation_content' => 'Hello Updated',
            'tags'                => ['updated', 'greeting']
        ];

        $response = $this->putJson("/api/translations/{$translation->id}", $data);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'locale_id' => $locale->id,
                    'translation_key' => 'hello.updated',
                    'translation_content' => 'Hello Updated'
                ],
                'meta' => [
                    'message' => 'Translation update successfully.',
                    'status' => 200,
                    'errors' => []
                ]
            ]);

        $this->assertDatabaseHas('translations', [
            'id' => $translation->id,
            'translation_key' => 'hello.updated',
            'translation_content' => 'Hello Updated'
        ]);
    }

    #[Test]
    public function it_can_delete_a_translation()
    {
        $locale = Locale::factory()->create(['code' => 'en']);
        $translation = Translation::factory()->create([
            'locale_id' => $locale->id,
            'translation_key' => 'delete.test',
            'translation_content' => 'To be deleted'
        ]);

        $response = $this->deleteJson("/api/translations/{$translation->id}");

        $response->assertStatus(200)
            ->assertJson([
                'meta' => [
                    'message' => 'Translation delete successfully.',
                    'status' => 200,
                    'errors' => []
                ]
            ]);

        $this->assertDatabaseMissing('translations', ['id' => $translation->id]);
    }

    #[Test]
    public function it_can_search_translations()
    {
        $locale = Locale::factory()->create(['code' => 'en']);
        $translation = Translation::factory()->create([
            'locale_id' => $locale->id,
            'translation_key' => 'search.key',
            'translation_content' => 'Search content test'
        ]);

        // Attach a tag to the translation.
        $tag = Tag::firstOrCreate(['name' => 'search']);
        $translation->tags()->sync([$tag->id]);

        // Search by translation_key.
        $response = $this->getJson('/api/translations/search?translation_key=search.key');
        $response->assertStatus(200)
            ->assertJsonFragment(['translation_key' => 'search.key']);

        // Search by translation_content.
        $response = $this->getJson('/api/translations/search?translation_content=Search content test');
        $response->assertStatus(200)
            ->assertJsonFragment(['translation_content' => 'Search content test']);

        // Search by tag.
        $response = $this->getJson('/api/translations/search?tag=search');
        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'search']);
    }

    #[Test]
    public function it_can_export_translations()
    {
        $locale = Locale::factory()->create(['code' => 'en']);
        Translation::factory()->count(1)->create([
            'locale_id' => $locale->id,
            'translation_key' => 'export.test',
            'translation_content' => 'Export Content'
        ]);

        $response = $this->getJson('/api/translations/export');

        $response->assertStatus(200)
            ->assertJsonFragment(['translation_content' => 'Export Content']);
    }
}
