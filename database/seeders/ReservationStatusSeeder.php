<?php

namespace Database\Seeders;

use App\Models\ReservationStatus;
use Illuminate\Database\Seeder;

class ReservationStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public $model = ReservationStatus::class;

    public function run()
    {
        $statuses = [
            ["name" => "Iniciada"],
            ["name" => "Confirmada"],
            ["name" => "Cancelada"],
        ];

        foreach($statuses as $status)
        {
            $this->model::firstOrCreate(['name' => $status['name']], $status);
        }
    }
}
