<?php declare(strict_types=1);

namespace App\Model;

use App\OldModel;
use Faker\Generator;

class Customer extends OldModel
{
    public string $id;
    public string $company_name;
    public string $address;
    public string $status;
    public string $created_on;
    public string $last_modified_on;
    public string $created_by;
    public string $last_modified_by;


    public function tableName(): string
    {
        return 'customer';
    }

    public function value(Generator $faker): array
    {
        $date = (new \DateTime())->format('Y-m-d H:i:s');

        return [
            'id'               => $faker->uuid,
            'company_name'     => $faker->company,
            'address'          => $faker->address,
            'status'           => 'NEW',
            'created_on'       => $date,
            'last_modified_on' => $date,
            'created_by'       => 'widewail-db-seeder',
            'last_modified_by' => 'widewail-db-seeder',
        ];
    }
}
