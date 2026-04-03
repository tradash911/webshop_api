<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       $categories = [
    [
        "name" => "Koporsók",
        "description" => "Különböző típusú és anyagú koporsók",
    ],
    [
        "name" => "Urnák",
        "description" => "Hamvak tárolására szolgáló urnák",
    ],
    [
        "name" => "Kegyeleti virágok",
        "description" => "Koszorúk, csokrok és sírdíszek",
    ],
    [
        "name" => "Sírkövek",
        "description" => "Gránit és egyéb síremlékek",
    ],
    [
        "name" => "Emléktárgyak",
        "description" => "Fotókeretek, emléklapok és személyes tárgyak",
    ],
    [
        "name" => "Gyertyák és mécsesek",
        "description" => "Kegyeleti gyertyák és mécsesek különböző kivitelben",
    ],
    [
        "name" => "Kegyeleti kiegészítők",
        "description" => "Sírgondozási és egyéb kiegészítő eszközök",
    ],
    [
        "name" => "Ruházat temetésre",
        "description" => "Alkalomhoz illő öltözékek és kiegészítők",
    ],
    [
        "name" => "Szertartási kellékek",
        "description" => "Temetési szertartáshoz szükséges eszközök",
    ],
    [
        "name" => "Egyedi megemlékezések",
        "description" => "Személyre szabott emléktárgyak és szolgáltatások",
    ],
];

    foreach ($categories as $category) {
        \App\Models\Category::create([
            ...$category,
            "is_active" => true,
        ]);
    }
    }
}
