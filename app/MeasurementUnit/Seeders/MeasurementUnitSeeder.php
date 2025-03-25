<?php

namespace App\MeasurementUnit\Seeders;

use App\Role\Models\Role;
use App\MeasurementUnit\Models\MeasurementUnit;
use Illuminate\Database\Seeder;

class MeasurementUnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //====== UNITY PRIMARY ======
        //=====> ID 54 
        
        $units = [
            ['name' => 'BOBINAS', 'symbol' => '4A'],
            ['name' => 'BALDE', 'symbol' => 'BJ'],
            ['name' => 'BARRILES', 'symbol' => 'BLL'],
            ['name' => 'BOLSA', 'symbol' => 'BG'],
            ['name' => 'BOTELLAS', 'symbol' => 'BO'],
            ['name' => 'CAJA', 'symbol' => 'BX'],
            ['name' => 'CARTONES', 'symbol' => 'CT'],
            ['name' => 'CENTIMETRO CUADRADO', 'symbol' => 'CMK'],
            ['name' => 'CENTIMETRO CUBICO', 'symbol' => 'CMQ'],
            ['name' => 'CENTIMETRO LINEAL', 'symbol' => 'CMT'],
            ['name' => 'CIENTO DE UNIDADES', 'symbol' => 'CEN'],
            ['name' => 'CILINDRO', 'symbol' => 'CY'],
            ['name' => 'CONOS', 'symbol' => 'CJ'],
            ['name' => 'DOCENA', 'symbol' => 'DZN'],
            ['name' => 'DOCENA POR 10**6', 'symbol' => 'DZP'],
            ['name' => 'FARDO', 'symbol' => 'BE'],
            ['name' => 'GALON INGLES (4,545956L)', 'symbol' => 'GLI'],
            ['name' => 'GRAMO', 'symbol' => 'GRM'],
            ['name' => 'GRUESA', 'symbol' => 'GRO'],
            ['name' => 'HECTOLITRO', 'symbol' => 'HLT'],
            ['name' => 'HOJA', 'symbol' => 'LEF'],
            ['name' => 'JUEGO', 'symbol' => 'SET'],
            ['name' => 'KILOGRAMO', 'symbol' => 'KGM'],
            ['name' => 'KILOMETRO', 'symbol' => 'KTM'],
            ['name' => 'KILOVATIO HORA', 'symbol' => 'KWH'],
            ['name' => 'KIT', 'symbol' => 'KIT'],
            ['name' => 'LATAS', 'symbol' => 'CA'],
            ['name' => 'LIBRAS', 'symbol' => 'LBR'],
            ['name' => 'LITRO', 'symbol' => 'LTR'],
            ['name' => 'MEGAWATT HORA', 'symbol' => 'MWH'],
            ['name' => 'METRO', 'symbol' => 'MTR'],
            ['name' => 'METRO CUADRADO', 'symbol' => 'MTK'],
            ['name' => 'METRO CUBICO', 'symbol' => 'MTQ'],
            ['name' => 'MILIGRAMOS', 'symbol' => 'MGM'],
            ['name' => 'MILILITRO', 'symbol' => 'MLT'],
            ['name' => 'MILIMETRO', 'symbol' => 'MMT'],
            ['name' => 'MILIMETRO CUADRADO', 'symbol' => 'MMK'],
            ['name' => 'MILIMETRO CUBICO', 'symbol' => 'MMQ'],
            ['name' => 'MILLARES', 'symbol' => 'MIL'],
            ['name' => 'MILLON DE UNIDADES', 'symbol' => 'UM'],
            ['name' => 'ONZAS', 'symbol' => 'ONZ'],
            ['name' => 'PALETAS', 'symbol' => 'PF'],
            ['name' => 'PAQUETE', 'symbol' => 'PK'],
            ['name' => 'PAR', 'symbol' => 'PR'],
            ['name' => 'PORCION', 'symbol' => 'PT'],
            ['name' => 'RESMA', 'symbol' => 'RM'],
            ['name' => 'ROLLO', 'symbol' => 'RO'],
            ['name' => 'SACO', 'symbol' => 'SA'],
            ['name' => 'SET', 'symbol' => 'ST'],
            ['name' => 'TAMBOR', 'symbol' => 'TU'],
            ['name' => 'TANQUE', 'symbol' => 'TK'],
            ['name' => 'TONELADA', 'symbol' => 'TNE'],
            ['name' => 'TUBOS', 'symbol' => 'TU'],
            ['name' => 'UNIDAD', 'symbol' => 'NIU'], //======== ID 54 =========
            ['name' => 'YARDA', 'symbol' => 'YRD'],
            ['name' => 'GRAMO NETO', 'symbol' => 'GRN'],
            ['name' => 'KILOGRAMO NETO', 'symbol' => 'KGN'],
            ['name' => 'TONELADA LARGA', 'symbol' => 'LTON'],
            ['name' => 'TONELADA CORTA', 'symbol' => 'STON'],
            ['name' => 'PIES CUBICOS', 'symbol' => 'FTQ'],
            ['name' => 'PIES CUADRADOS', 'symbol' => 'FTK'],
            ['name' => 'PIE LINEAL', 'symbol' => 'FT'],
            ['name' => 'MIL', 'symbol' => 'ML'],
            ['name' => 'KILOMETRO POR HORA', 'symbol' => 'KMH'],
            ['name' => 'MILLAS POR HORA', 'symbol' => 'MPH'],
            ['name' => 'MILILITROS POR HORA', 'symbol' => 'MLH'],
            ['name' => 'MILIGRAMOS POR HORA', 'symbol' => 'MGH'],
            ['name' => 'MILIMETROS POR HORA', 'symbol' => 'MMH'],
            ['name' => 'GRAMOS POR HORA', 'symbol' => 'GRH'],
            ['name' => 'PIES POR HORA', 'symbol' => 'FTH'],
            ['name' => 'CENTIMETROS POR HORA', 'symbol' => 'CMH'],
            ['name' => 'METROS POR HORA', 'symbol' => 'MTH'],
            ['name' => 'YARDAS POR HORA', 'symbol' => 'YDH'],
            ['name' => 'MILILITROS POR MINUTO', 'symbol' => 'MLM'],
            ['name' => 'MILIGRAMOS POR MINUTO', 'symbol' => 'MGM'],
            ['name' => 'GRAMOS POR MINUTO', 'symbol' => 'GRM'],
            ['name' => 'PIES POR MINUTO', 'symbol' => 'FTM'],
            ['name' => 'CENTIMETROS POR MINUTO', 'symbol' => 'CMM'],
            ['name' => 'METROS POR MINUTO', 'symbol' => 'MTM'],
            ['name' => 'YARDAS POR MINUTO', 'symbol' => 'YDM'],
            ['name' => 'MILILITROS POR SEGUNDO', 'symbol' => 'MLS'],
            ['name' => 'MILIGRAMOS POR SEGUNDO', 'symbol' => 'MGS'],
            ['name' => 'GRAMOS POR SEGUNDO', 'symbol' => 'GRS'],
            ['name' => 'PIES POR SEGUNDO', 'symbol' => 'FTS'],
            ['name' => 'CENTIMETROS POR SEGUNDO', 'symbol' => 'CMS'],
            ['name' => 'METROS POR SEGUNDO', 'symbol' => 'MTS'],
            ['name' => 'YARDAS POR SEGUNDO', 'symbol' => 'YDS'],
            ['name' => 'TONELADA MÉTRICA', 'symbol' => 'T'],
            ['name' => 'UNIDADES POR METRO', 'symbol' => 'UPM'],
            ['name' => 'UNIDADES POR CENTÍMETRO', 'symbol' => 'UPC'],
            ['name' => 'UNIDADES POR MILÍMETRO', 'symbol' => 'UPMM'],
            ['name' => 'UNIDADES POR GRAMO', 'symbol' => 'UPG'],
            ['name' => 'UNIDADES POR KILOGRAMO', 'symbol' => 'UPKG'],
            ['name' => 'UNIDADES POR LITRO', 'symbol' => 'UPL']
        ];
        
        foreach ($units as $unidad_medida) {
            $item                   = new MeasurementUnit();
            $item->name             = $unidad_medida['name'];
            $item->symbol           = $unidad_medida['symbol'];
            $item->save();
        }
    }
}
