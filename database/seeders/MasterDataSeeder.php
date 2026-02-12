<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\CustodianMaterial;
use App\Models\Event;
use App\Models\EventCustodianRequest;
use App\Models\Venue;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Venues (School Specific)
        // -----------------------------------------------------------
        $venues = [
            [
                'name' => 'Main Auditorium',
                'address' => 'Academic Building, 2nd Floor',
                'capacity' => 500,
                'facilities' => 'Stage, Sound System, Air Conditioning, Projector',
            ],
            [
                'name' => 'School Gymnasium',
                'address' => 'Sports Complex, East Campus',
                'capacity' => 1200,
                'facilities' => 'Basketball Court, Bleachers, Sound System, Scoreboard',
            ],
            [
                'name' => 'Conference Room A',
                'address' => 'Administration Wing, Ground Floor',
                'capacity' => 30,
                'facilities' => 'Conference Table, Wi-Fi, LED Display',
            ],
            [
                'name' => 'Open Field / Quadrangle',
                'address' => 'Central Campus Grounds',
                'capacity' => 2000,
                'facilities' => 'Outdoor Lighting, Moveable Stage Access',
            ],
            [
                'name' => 'Multimedia Room',
                'address' => 'Library Building, 3rd Floor',
                'capacity' => 60,
                'facilities' => 'Computers, Sound Proofing, Projector, High-speed Internet',
            ],
        ];

        foreach ($venues as $venue) {
            Venue::create($venue);
        }

        // 2. Seed Employees
        // -----------------------------------------------------------
        $employees = [
            [
                'first_name' => 'John',
                'middle_name' => 'Alexander',
                'last_name' => 'Doe',
                'date_of_birth' => '1985-03-15',
                'gender' => 'Male',
                'marital_status' => 'Married',
                'nationality' => 'American',
                'email' => 'john@example.com',
                'phone_number' => '555-0101',
                'mobile_number' => '555-1101',
                'emergency_contact_name' => 'Jane Doe',
                'emergency_contact_phone' => '555-2101',
                'emergency_contact_relationship' => 'Spouse',
                'street_address' => '123 Tech Street',
                'city' => 'San Francisco',
                'state_province' => 'California',
                'postal_code' => '94105',
                'country' => 'United States',
                'residential_type' => 'Owned',
                'employee_id_number' => 'EMP-001',
                'department' => 'IT',
                'position_title' => 'IT Manager',
                'job_description' => 'Manages IT infrastructure and support team',
                'employment_status' => 'Active',
                'hire_date' => '2015-06-01',
                'probation_end_date' => '2015-09-01',
                'contract_type' => 'Full-time',
                'tax_id_number' => '123-45-6789',
                'id_document_type' => 'Passport',
                'id_document_number' => 'P12345678',
                'notes' => 'Senior IT Manager, leading digital transformation',
                'start_date' => '2015-06-01',
            ],
            [
                'first_name' => 'Jane',
                'middle_name' => 'Marie',
                'last_name' => 'Smith',
                'date_of_birth' => '1990-07-22',
                'gender' => 'Female',
                'marital_status' => 'Single',
                'nationality' => 'Canadian',
                'email' => 'jane@example.com',
                'phone_number' => '555-0102',
                'mobile_number' => '555-1102',
                'emergency_contact_name' => 'Robert Smith',
                'emergency_contact_phone' => '555-2102',
                'emergency_contact_relationship' => 'Parent',
                'street_address' => '456 HR Avenue',
                'city' => 'Toronto',
                'state_province' => 'Ontario',
                'postal_code' => 'M5H 2N2',
                'country' => 'Canada',
                'residential_type' => 'Rented',
                'employee_id_number' => 'EMP-002',
                'department' => 'HR',
                'position_title' => 'HR Director',
                'job_description' => 'Oversees human resources and employee relations',
                'employment_status' => 'Active',
                'hire_date' => '2018-01-15',
                'probation_end_date' => '2018-04-15',
                'contract_type' => 'Full-time',
                'tax_id_number' => '987-65-4321',
                'id_document_type' => 'Passport',
                'id_document_number' => 'P87654321',
                'notes' => 'HR Director with 12+ years experience',
                'start_date' => '2018-01-15',
            ],
            [
                'first_name' => 'Michael',
                'middle_name' => 'James',
                'last_name' => 'Brown',
                'date_of_birth' => '1988-11-08',
                'gender' => 'Male',
                'marital_status' => 'Married',
                'nationality' => 'American',
                'email' => 'mike@example.com',
                'phone_number' => '555-0103',
                'mobile_number' => '555-1103',
                'emergency_contact_name' => 'Lisa Brown',
                'emergency_contact_phone' => '555-2103',
                'emergency_contact_relationship' => 'Spouse',
                'street_address' => '789 Maintenance Road',
                'city' => 'Austin',
                'state_province' => 'Texas',
                'postal_code' => '78701',
                'country' => 'United States',
                'residential_type' => 'Owned',
                'employee_id_number' => 'EMP-003',
                'department' => 'Maintenance',
                'position_title' => 'Maintenance Supervisor',
                'job_description' => 'Supervises maintenance operations and facility upkeep',
                'employment_status' => 'Active',
                'hire_date' => '2016-03-20',
                'probation_end_date' => '2016-06-20',
                'contract_type' => 'Full-time',
                'tax_id_number' => '555-66-7777',
                'id_document_type' => 'Driver License',
                'id_document_number' => 'D123456789',
                'notes' => 'Experienced facilities manager',
                'start_date' => '2016-03-20',
            ],
            [
                'first_name' => 'Sarah',
                'middle_name' => 'Elizabeth',
                'last_name' => 'Wilson',
                'date_of_birth' => '1992-05-17',
                'gender' => 'Female',
                'marital_status' => 'Single',
                'nationality' => 'American',
                'email' => 'sarah@example.com',
                'phone_number' => '555-0104',
                'mobile_number' => '555-1104',
                'emergency_contact_name' => 'Michael Wilson',
                'emergency_contact_phone' => '555-2104',
                'emergency_contact_relationship' => 'Brother',
                'street_address' => '321 Marketing Lane',
                'city' => 'New York',
                'state_province' => 'New York',
                'postal_code' => '10001',
                'country' => 'United States',
                'residential_type' => 'Rented',
                'employee_id_number' => 'EMP-004',
                'department' => 'Marketing',
                'position_title' => 'Marketing Manager',
                'job_description' => 'Develops and executes marketing strategies',
                'employment_status' => 'Active',
                'hire_date' => '2019-08-05',
                'probation_end_date' => '2019-11-05',
                'contract_type' => 'Full-time',
                'tax_id_number' => '888-99-0000',
                'id_document_type' => 'National ID',
                'id_document_number' => 'N987654321',
                'notes' => 'Creative marketing lead',
                'start_date' => '2019-08-05',
            ],
            [
                'first_name' => 'Robert',
                'middle_name' => 'David',
                'last_name' => 'Garcia',
                'date_of_birth' => '1980-09-12',
                'gender' => 'Male',
                'marital_status' => 'Married',
                'nationality' => 'American',
                'email' => 'robert@example.com',
                'phone_number' => '555-0105',
                'mobile_number' => '555-1105',
                'emergency_contact_name' => 'Maria Garcia',
                'emergency_contact_phone' => '555-2105',
                'emergency_contact_relationship' => 'Spouse',
                'street_address' => '654 Finance Boulevard',
                'city' => 'Chicago',
                'state_province' => 'Illinois',
                'postal_code' => '60601',
                'country' => 'United States',
                'residential_type' => 'Owned',
                'employee_id_number' => 'EMP-005',
                'department' => 'Finance',
                'position_title' => 'Finance Controller',
                'job_description' => 'Manages financial operations and reporting',
                'employment_status' => 'Active',
                'hire_date' => '2014-02-10',
                'probation_end_date' => '2014-05-10',
                'contract_type' => 'Full-time',
                'tax_id_number' => '111-22-3333',
                'id_document_type' => 'Passport',
                'id_document_number' => 'P111222333',
                'notes' => 'Senior finance professional with CPA',
                'start_date' => '2014-02-10',
            ],
        ];

        foreach ($employees as $emp) {
            Employee::updateOrCreate(
                ['employee_id_number' => $emp['employee_id_number']],
                $emp
            );
        }

        // 3. Seed Custodian Materials
        // -----------------------------------------------------------
        $materials = [
            ['name' => 'Folding Chairs', 'category' => 'Furniture', 'stock' => 150],
            ['name' => 'Sound System', 'category' => 'Electronics', 'stock' => 5],
            ['name' => 'Projector Screen', 'category' => 'Electronics', 'stock' => 3],
            ['name' => 'Whiteboard Markers', 'category' => 'Office Supplies', 'stock' => 50],
            ['name' => 'Extension Cords', 'category' => 'Hardware', 'stock' => 20],
            ['name' => 'Monoblock Tables', 'category' => 'Furniture', 'stock' => 40],
        ];

        foreach ($materials as $mat) {
            CustodianMaterial::create($mat);
        }

        // 4. Seed Sample Requests (Only if an event exists)
        // -----------------------------------------------------------
        $event = Event::first();

        if ($event) {
            $material = CustodianMaterial::where('name', 'Folding Chairs')->first();
            
            if ($material) {
                EventCustodianRequest::create([
                    'event_id' => $event->id,
                    'custodian_material_id' => $material->id,
                    'quantity' => 20,
                    'status' => 'pending',
                ]);
            }

            $soundSystem = CustodianMaterial::where('name', 'Sound System')->first();
            if ($soundSystem) {
                EventCustodianRequest::create([
                    'event_id' => $event->id,
                    'custodian_material_id' => $soundSystem->id,
                    'quantity' => 1,
                    'status' => 'approved',
                ]);
            }
        }
    }
}