<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('products')->insert([
            [
                'name' => 'Táo Mỹ',
                'description' => 'Táo Mỹ nhập khẩu giòn ngọt.',
                'image' => 'https://i.pinimg.com/736x/eb/45/b7/eb45b72a76e9999f6558a1e763eee68f.jpg',
                'price' => '85000',
                'stock' => '120',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Chuối Cavendish',
                'description' => 'Chuối tươi giàu dinh dưỡng.',
                'image' => 'https://i.pinimg.com/1200x/b8/8f/10/b88f1054995a23a423ede41eea9d0343.jpg',
                'price' => '35000',
                'stock' => '200',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Cam Sành',
                'description' => 'Cam sành mọng nước.',
                'image' => 'https://i.pinimg.com/736x/9a/76/01/9a76016c2c9c56f0ab4d0d9105cbaece.jpg',
                'price' => '60000',
                'stock' => '180',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Xoài Cát Hòa Lộc',
                'description' => 'Xoài ngọt thơm đặc sản.',
                'image' => 'https://i.pinimg.com/1200x/08/8e/ae/088eaea64cf379c3c7d062dbcc0b1749.jpg',
                'price' => '90000',
                'stock' => '90',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Dưa Hấu',
                'description' => 'Dưa hấu đỏ ngọt mát.',
                'image' => 'https://i.pinimg.com/1200x/36/bb/4f/36bb4fe300eb6e76c8c05b5ef4569192.jpg',
                'price' => '45000',
                'stock' => '75',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Thanh Long',
                'description' => 'Thanh long ruột đỏ.',
                'image' => 'https://i.pinimg.com/736x/36/fd/95/36fd957fd688c5a22fd2d518ed437cc5.jpg',
                'price' => '55000',
                'stock' => '130',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Nho Đen',
                'description' => 'Nho đen không hạt.',
                'image' => 'https://i.pinimg.com/736x/ca/e0/2f/cae02f3606a576aef27b64b15fd609eb.jpg',
                'price' => '120000',
                'stock' => '60',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
