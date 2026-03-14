<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\Inspection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssetApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_post_assets_creates_asset_and_returns_201(): void
    {
        $payload = [
            'name' => 'Test Laptop',
            'serial_number' => 'SN-001',
            'status' => 'active',
        ];

        $response = $this->postJson('/api/assets', $payload);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'name' => 'Test Laptop',
                'serial_number' => 'SN-001',
                'status' => 'active',
            ])
            ->assertJsonStructure([
                'id',
                'name',
                'serial_number',
                'status',
                'created_at',
                'updated_at',
            ]);

        $this->assertDatabaseHas('assets', [
            'serial_number' => 'SN-001',
        ]);
    }

    public function test_post_assets_validates_required_fields(): void
    {
        $response = $this->postJson('/api/assets', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'serial_number', 'status']);
    }

    public function test_post_assets_validates_unique_serial_number(): void
    {
        Asset::factory()->create(['serial_number' => 'SN-DUP']);

        $response = $this->postJson('/api/assets', [
            'name' => 'Another Asset',
            'serial_number' => 'SN-DUP',
            'status' => 'active',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['serial_number']);
    }

    public function test_post_assets_validates_status_enum(): void
    {
        $response = $this->postJson('/api/assets', [
            'name' => 'Test Asset',
            'serial_number' => 'SN-002',
            'status' => 'invalid_status',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['status']);
    }

    public function test_get_assets_id_returns_asset_with_latest_three_inspections(): void
    {
        $asset = Asset::create([
            'name' => 'Laptop',
            'serial_number' => 'SN-003',
            'status' => 'active',
        ]);

        Inspection::create([
            'asset_id' => $asset->id,
            'inspector_name' => 'Inspector A',
            'passed' => true,
            'notes' => 'First',
        ]);
        Inspection::create([
            'asset_id' => $asset->id,
            'inspector_name' => 'Inspector B',
            'passed' => false,
            'notes' => 'Second',
        ]);
        Inspection::create([
            'asset_id' => $asset->id,
            'inspector_name' => 'Inspector C',
            'passed' => true,
            'notes' => 'Third',
        ]);
        Inspection::create([
            'asset_id' => $asset->id,
            'inspector_name' => 'Inspector D',
            'passed' => false,
            'notes' => 'Fourth (oldest)',
        ]);

        $response = $this->getJson("/api/assets/{$asset->id}");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'name' => 'Laptop',
                'serial_number' => 'SN-003',
            ])
            ->assertJsonStructure([
                'id',
                'name',
                'serial_number',
                'status',
                'inspections',
            ]);

        $inspections = $response->json('inspections');
        $this->assertCount(3, $inspections);
        $this->assertEquals('Inspector D', $inspections[0]['inspector_name']);
        $this->assertEquals('Inspector C', $inspections[1]['inspector_name']);
        $this->assertEquals('Inspector B', $inspections[2]['inspector_name']);
    }

    public function test_get_assets_id_returns_404_for_missing_asset(): void
    {
        $response = $this->getJson('/api/assets/99999');

        $response->assertStatus(404);
    }
}
