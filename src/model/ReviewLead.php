<?php declare(strict_types=1);

namespace App\Model;

use App\Model;
use Faker\Generator;

class ReviewLead extends Model
{
    public string $id;

    public function tableName(): string
    {
        return 'review_leads';
    }

    public function value(Generator $faker): array
    {
        return [
            'id' => $faker->uuid,
        ];
    }
}
