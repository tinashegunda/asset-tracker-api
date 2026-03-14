<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\Inspection;
use Illuminate\Database\Seeder;

class AssetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $assets = [
            ['name' => 'Laptop Dell XPS 15', 'serial_number' => 'DL-XPS-001', 'status' => 'active'],
            ['name' => 'MacBook Pro 14"', 'serial_number' => 'MBP-014-002', 'status' => 'active'],
            ['name' => 'Projector Epson EB-X41', 'serial_number' => 'EP-EB41-003', 'status' => 'active'],
            ['name' => 'Server Rack Unit 1', 'serial_number' => 'SRU-001-004', 'status' => 'maintenance'],
            ['name' => 'Monitor LG 27" 4K', 'serial_number' => 'LG-27K-005', 'status' => 'active'],
            ['name' => 'Printer HP LaserJet', 'serial_number' => 'HP-LJ-006', 'status' => 'inactive'],
            ['name' => 'Tablet iPad Pro', 'serial_number' => 'IPAD-PRO-007', 'status' => 'active'],
            ['name' => 'Camera Canon EOS R5', 'serial_number' => 'CAN-R5-008', 'status' => 'active'],
            ['name' => 'Workstation Dell OptiPlex', 'serial_number' => 'DO-7090-009', 'status' => 'active'],
            ['name' => 'NAS Synology DS920+', 'serial_number' => 'SYN-DS920-010', 'status' => 'active'],
        ];

        $inspectorNames = ['John Smith', 'Jane Doe', 'Mike Johnson', 'Sarah Williams', 'David Brown'];

        foreach ($assets as $assetData) {
            $asset = Asset::create($assetData);

            $inspectionCount = rand(2, 5);
            for ($i = 0; $i < $inspectionCount; $i++) {
                Inspection::create([
                    'asset_id' => $asset->id,
                    'inspector_name' => $inspectorNames[array_rand($inspectorNames)],
                    'passed' => (bool) rand(0, 1),
                    'notes' => $i === 0 ? 'Initial inspection completed.' : 'Routine inspection.',
                ]);
            }
        }
    }
}
