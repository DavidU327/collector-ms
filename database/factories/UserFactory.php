<?php

namespace Database\Factories;

use App\Models\Rol;
use App\Models\State;
use App\Models\TypeIdentification;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => 'Luis David Gonzalez Martinez',
            'phone' => '3178874640',
            'identification' => '1014263996',
            'type_identification_id' => TypeIdentification::first()->id,
            'rol_id' => Rol::first()->id,
            'email' => 'david327_@outlook.es',
            'state_id' => State::first()->id,
            'password' => bcrypt('David.327'),
        ];
    }

}
