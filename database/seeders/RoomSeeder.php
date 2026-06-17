<?php

namespace Database\Seeders;

use App\Models\Room;
use App\Models\RoomStatus;
use App\Models\RoomType;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Seed Room Statuses
        $statuses = [
            ['code' => 'O', 'name' => 'Occupied', 'description' => 'Room is currently occupied by a registered guest.'],
            ['code' => 'V', 'name' => 'Vacant', 'description' => 'Room is empty.'],
            ['code' => 'OC', 'name' => 'Occupied Clean', 'description' => 'Room is occupied and has been cleaned.'],
            ['code' => 'OD', 'name' => 'Occupied Dirty', 'description' => 'Room is occupied but needs cleaning.'],
            ['code' => 'VCI', 'name' => 'Vacant Clean Inspected', 'description' => 'Room is vacant, clean, inspected, and ready for check-in.'],
            ['code' => 'VC', 'name' => 'Vacant Clean', 'description' => 'Room is vacant and clean.'],
            ['code' => 'VD', 'name' => 'Vacant Dirty', 'description' => 'Room is vacant but dirty and needs cleaning.'],
            ['code' => 'Comp', 'name' => 'Complimentary', 'description' => 'Room is occupied free of charge.'],
            ['code' => 'HU', 'name' => 'House Use', 'description' => 'Room is occupied for management or staff use.'],
            ['code' => 'DND', 'name' => 'Do Not Disturb', 'description' => 'Guest requested not to be disturbed.'],
            ['code' => 'SO', 'name' => 'Sleep Out', 'description' => 'Guest is registered but did not sleep in the room.'],
            ['code' => 'Skip', 'name' => 'Skipper', 'description' => 'Guest left the hotel without settling their bill.'],
            ['code' => 'OS', 'name' => 'Out of Service', 'description' => 'Room is temporarily out of service for light repairs.'],
            ['code' => 'OOO', 'name' => 'Out of Order', 'description' => 'Room is out of order for major repairs or renovation.'],
            ['code' => 'DO', 'name' => 'Due Out', 'description' => 'Room is scheduled to check out today.'],
            ['code' => 'ED', 'name' => 'Expected Departure', 'description' => 'Guest is expected to depart today.'],
            ['code' => 'EA', 'name' => 'Expected Arrival', 'description' => 'Guest is expected to arrive today.'],
            ['code' => 'CO', 'name' => 'Check Out', 'description' => 'Guest has completed the check-out process.'],
            ['code' => 'LCO', 'name' => 'Late Check Out', 'description' => 'Guest requested check-out past the standard checkout time.'],
            ['code' => 'ONL', 'name' => 'Occupied No Luggage', 'description' => 'Room is occupied but there is no luggage.'],
            ['code' => 'DL', 'name' => 'Double Lock', 'description' => 'Room is locked from inside and cannot be accessed from outside.'],
        ];

        $statusModels = [];
        foreach ($statuses as $status) {
            $statusModels[$status['code']] = RoomStatus::create($status);
        }

        // Default initial status for rooms is VCI (Vacant Clean Inspected)
        $defaultStatusId = $statusModels['VCI']->id;

        // 2. Seed Room Types
        $types = [
            [
                'name' => 'Standard Room',
                'description' => 'Queen Bed, AC, TV, WiFi, Shower, Mineral Water',
                'capacity' => 2,
                'base_price' => 350000.00,
                'breakfast_included' => false,
            ],
            [
                'name' => 'Superior Room',
                'description' => 'Queen Bed, AC, Smart TV, WiFi, Shower Water Heater, Coffee & Tea Maker',
                'capacity' => 2,
                'base_price' => 550000.00,
                'breakfast_included' => false, // Under 600k threshold
            ],
            [
                'name' => 'Deluxe Room',
                'description' => 'King Bed, Sofa, Smart TV, WiFi, Water Heater, Coffee Maker',
                'capacity' => 3,
                'base_price' => 750000.00,
                'breakfast_included' => true, // Over 600k threshold
            ],
            [
                'name' => 'Studio Room',
                'description' => 'King Bed, Mini Pantry, Microwave, Smart TV, WiFi',
                'capacity' => 3,
                'base_price' => 950000.00,
                'breakfast_included' => true, // Over 600k threshold
            ],
            [
                'name' => 'Suite Room',
                'description' => 'Living Room, King Bed, Bathtub, Smart TV, Mini Bar',
                'capacity' => 4,
                'base_price' => 1600000.00,
                'breakfast_included' => true, // Over 600k threshold
            ],
            [
                'name' => 'Connecting Room',
                'description' => 'Dua Kamar Terhubung, 2 Bathroom, Smart TV, WiFi, Living Area',
                'capacity' => 6,
                'base_price' => 2000000.00,
                'breakfast_included' => true, // Over 600k threshold
            ],
        ];

        $typeModels = [];
        foreach ($types as $type) {
            $typeModels[$type['name']] = RoomType::create($type);
        }

        // 3. Seed Rooms (101-105 on Floor 1, 201-205 on Floor 2, 301-305 on Floor 3)
        $roomsToSeed = [
            // Floor 1 - Standard & Superior
            ['room_number' => '101', 'room_type' => 'Standard Room', 'floor' => 1],
            ['room_number' => '102', 'room_type' => 'Standard Room', 'floor' => 1],
            ['room_number' => '103', 'room_type' => 'Standard Room', 'floor' => 1],
            ['room_number' => '104', 'room_type' => 'Superior Room', 'floor' => 1],
            ['room_number' => '105', 'room_type' => 'Superior Room', 'floor' => 1],

            // Floor 2 - Deluxe & Studio
            ['room_number' => '201', 'room_type' => 'Deluxe Room', 'floor' => 2],
            ['room_number' => '202', 'room_type' => 'Deluxe Room', 'floor' => 2],
            ['room_number' => '203', 'room_type' => 'Deluxe Room', 'floor' => 2],
            ['room_number' => '204', 'room_type' => 'Studio Room', 'floor' => 2],
            ['room_number' => '205', 'room_type' => 'Studio Room', 'floor' => 2],

            // Floor 3 - Suite & Connecting
            ['room_number' => '301', 'room_type' => 'Suite Room', 'floor' => 3],
            ['room_number' => '302', 'room_type' => 'Suite Room', 'floor' => 3],
            ['room_number' => '303', 'room_type' => 'Connecting Room', 'floor' => 3],
            ['room_number' => '304', 'room_type' => 'Connecting Room', 'floor' => 3],
        ];

        foreach ($roomsToSeed as $room) {
            Room::create([
                'room_number' => $room['room_number'],
                'room_type_id' => $typeModels[$room['room_type']]->id,
                'floor' => $room['floor'],
                'current_status_id' => $defaultStatusId,
            ]);
        }
    }
}
