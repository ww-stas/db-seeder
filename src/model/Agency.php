<?php declare(strict_types=1);

namespace App\Model;

use App\Model;
use Faker\Generator;

class Agency extends Model
{
    public string $id;

    public function value(Generator $faker): array
    {
        $email = $faker->email;

        return [
            'id'              => $faker->uuid,
            'name'            => $faker->company,
            'support_email'    => $email,
            'support_phone'   => $email,
            'car_gurus_email' => $email,
            'edmunds_email'   => $email,
            'yelp_email'      => $email,
        ];
    }

    public function tableName(): string
    {
        return 'agency';
    }

}
